<?php

namespace App\Services;

use Throwable;

/**
 * Hands an approved applicant over to the SIS (POST /api/students/promote)
 * the moment an Admission Officer approves their application.
 *
 * Flow:
 *   1. ApplicantsController::decide()/bulkUpdate() calls queueForApplicant()
 *      right after setting application_status = 'approved' — this only
 *      inserts a sis_sync row, so it can never fail the approval itself.
 *   2. attempt() does the HTTP call (run after the response is sent, and again
 *      by `php spark sis:sync` for anything that failed or was missed).
 *   3. On success the SIS student number is stored and the applicant becomes
 *      'enrolled'.
 *
 * The SIS call is idempotent on applicant_id, so retries are always safe.
 */
class SisSyncService
{
    public const MAX_ATTEMPTS = 6;

    // Minutes to wait before attempt N+1 (index = attempts already made - 1).
    private const BACKOFF_MINUTES = [1, 5, 15, 60, 240, 720];

    /**
     * Queue a handoff for an applicant an Admission Officer just approved —
     * the trigger point, in place of the old Reservation-Fee-PAID one.
     * Returns the sync_id when a row was created, null when one already
     * exists for this applicant (unique key on applicant_id backs this up).
     */
    public function queueForApplicant(int $applicantId): ?int
    {
        $db = db_connect();

        $exists = $db->table('sis_sync')
            ->where('applicant_id', $applicantId)
            ->countAllResults() > 0;

        if ($exists) {
            return null;
        }

        $now = date('Y-m-d H:i:s');

        $db->table('sis_sync')->insert([
            'applicant_id'    => $applicantId,
            'payment_id'      => null,
            'status'          => 'pending',
            'attempts'        => 0,
            'next_attempt_at' => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        return (int) $db->insertID();
    }

    /**
     * Try to deliver one queued handoff. Never throws.
     * Returns ['ok' => bool, 'message' => string].
     */
    public function attempt(int $syncId): array
    {
        $db = db_connect();

        // Atomic claim so the post-response attempt and the spark worker
        // never process the same row at once.
        $db->table('sis_sync')
            ->where('sync_id', $syncId)
            ->whereIn('status', ['pending', 'failed'])
            ->where('attempts <', self::MAX_ATTEMPTS)
            ->update(['status' => 'processing', 'updated_at' => date('Y-m-d H:i:s')]);

        if ($db->affectedRows() === 0) {
            return ['ok' => false, 'message' => 'Not claimable (already synced, in progress, or out of attempts).'];
        }

        $row = $db->table('sis_sync')->where('sync_id', $syncId)->get()->getRowArray();

        try {
            $payload = $this->buildPayload((int) $row['applicant_id']);
            $result  = $this->post($payload);
        } catch (\RuntimeException $e) {
            // Bad data on our side (missing applicant/program code): retrying won't fix it.
            $result = ['ok' => false, 'retry' => false, 'error' => $e->getMessage()];
        } catch (Throwable $e) {
            $result = ['ok' => false, 'retry' => true, 'error' => $e->getMessage()];
        }

        return $this->record($row, $result);
    }

    /** Rows due for (re)delivery — used by the spark worker. */
    public function dueIds(int $limit = 20): array
    {
        $rows = db_connect()->table('sis_sync')
            ->select('sync_id')
            ->whereIn('status', ['pending', 'failed'])
            ->where('attempts <', self::MAX_ATTEMPTS)
            ->groupStart()
                ->where('next_attempt_at <=', date('Y-m-d H:i:s'))
                ->orWhere('next_attempt_at IS NULL')
            ->groupEnd()
            ->orderBy('sync_id')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'sync_id'));
    }

    private function buildPayload(int $applicantId): array
    {
        $row = db_connect()->table('applicants a')
            ->select('a.applicant_id, u.firstname, u.lastname, u.email, u.mobile, p.program_code, p.program_name')
            ->join('users u', 'u.user_id = a.user_id')
            ->join('programs p', 'p.program_id = a.program_id', 'left')
            ->where('a.applicant_id', $applicantId)
            ->get()
            ->getRowArray();

        if (! $row) {
            throw new \RuntimeException("Applicant {$applicantId} not found.");
        }

        $payment = db_connect()->table('sis_sync')
            ->select('payment_id')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getRowArray();

        $paidAt = null;
        if (! empty($payment['payment_id'])) {
            $paidAt = db_connect()->table('payment')
                ->select('payment_date')
                ->where('payment_id', $payment['payment_id'])
                ->get()
                ->getRowArray()['payment_date'] ?? null;
        }

        $payload = [
            'applicant_id' => (string) $row['applicant_id'],
            'first_name'   => trim((string) $row['firstname']),
            'last_name'    => trim((string) $row['lastname']),
            'email'        => strtolower(trim((string) $row['email'])),
        ];

        // Programs are dynamic in Admissions, so the SIS creates any code it
        // doesn't know yet — which is why the name travels with the code.
        // A program with no code can't be represented in the SIS at all.
        if (empty($row['program_code'])) {
            throw new \RuntimeException(
                'The applicant\'s program has no program code in Admissions. Set one, then run: php spark sis:sync --requeue ' . $applicantId
            );
        }

        $payload['program_code'] = trim((string) $row['program_code']);

        if (! empty($row['program_name'])) {
            $payload['program_name'] = trim((string) $row['program_name']);
        }
        if (! empty($row['mobile'])) {
            $payload['contact_number'] = $row['mobile'];
        }
        if (! empty($paidAt)) {
            $payload['admission_date'] = $paidAt;
        }

        return $payload;
    }

    /**
     * @return array{ok: bool, retry?: bool, error?: string, student_number?: string}
     */
    private function post(array $payload): array
    {
        $url = rtrim((string) env('SIS_API_URL'), '/');
        $key = (string) env('SIS_API_KEY');

        if ($url === '' || $key === '') {
            return ['ok' => false, 'retry' => false, 'error' => 'SIS_API_URL / SIS_API_KEY are not configured.'];
        }

        $response = \Config\Services::curlrequest(['timeout' => 10, 'http_errors' => false])
            ->post($url . '/api/students/promote', [
                'headers' => ['X-API-KEY' => $key, 'Accept' => 'application/json'],
                'json'    => $payload,
            ]);

        $status = $response->getStatusCode();
        $body   = json_decode((string) $response->getBody(), true);

        if ($status === 200 && ($body['success'] ?? false) === true && ! empty($body['student']['student_number'])) {
            return ['ok' => true, 'student_number' => (string) $body['student']['student_number']];
        }

        $message = $body['message'] ?? ('HTTP ' . $status);

        // 5xx / 429 are worth retrying; 401 (bad key) and 422 (bad data such as
        // an unknown program code or an email that already has a SIS account)
        // will not fix themselves, so they wait for a human.
        $retry = $status >= 500 || $status === 429 || $status === 0;

        return ['ok' => false, 'retry' => $retry, 'error' => "SIS responded {$status}: {$message}"];
    }

    private function record(array $row, array $result): array
    {
        $db       = db_connect();
        $now      = date('Y-m-d H:i:s');
        $attempts = (int) $row['attempts'] + 1;

        if ($result['ok']) {
            $db->transStart();

            $db->table('sis_sync')->where('sync_id', $row['sync_id'])->update([
                'status'         => 'synced',
                'attempts'       => $attempts,
                'student_number' => $result['student_number'],
                'last_error'     => null,
                'synced_at'      => $now,
                'updated_at'     => $now,
            ]);

            // Only promote applicants who were actually approved.
            $db->table('applicants')
                ->where('applicant_id', $row['applicant_id'])
                ->where('application_status', 'approved')
                ->update(['application_status' => 'enrolled']);

            $db->transComplete();

            return ['ok' => true, 'message' => 'Synced as ' . $result['student_number']];
        }

        $giveUp = empty($result['retry']) || $attempts >= self::MAX_ATTEMPTS;
        $delay  = self::BACKOFF_MINUTES[min($attempts - 1, count(self::BACKOFF_MINUTES) - 1)];

        $db->table('sis_sync')->where('sync_id', $row['sync_id'])->update([
            'status'          => 'failed',
            // A permanent failure burns the remaining attempts so the worker skips it.
            'attempts'        => $giveUp ? self::MAX_ATTEMPTS : $attempts,
            'next_attempt_at' => $giveUp ? null : date('Y-m-d H:i:s', strtotime("+{$delay} minutes")),
            'last_error'      => mb_substr((string) $result['error'], 0, 1000),
            'updated_at'      => $now,
        ]);

        log_message('error', '[SIS sync] applicant ' . $row['applicant_id'] . ': ' . $result['error']);

        return ['ok' => false, 'message' => $result['error']];
    }
}
