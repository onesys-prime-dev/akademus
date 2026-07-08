<?php

namespace App\Services;

use App\Models\Application\PersonalInformationModel;
use App\Models\Application\FamilyBackgroundModel;
use App\Models\Application\EducationalBackgroundModel;
use App\Models\FormPageModel;
use App\Models\ThemeModel;
use App\Services\RequirementService;
use App\Services\ApplicantDocumentService;

class ApplicationProgressService
{
    public function build(array $applicant, ?array $application): array
    {
        $db = db_connect();

        $applicantStatus = $applicant['application_status'] ?? 'pending';
        $decisionRemarks = $applicant['decision_remarks'] ?? null;
        $appStatus       = $application['application_status'] ?? '';
        $isFastTrack     = !empty($application['is_fast_track']);

        // ── Revision request ──────────────────────
        $revisionRequested = $appStatus === 'needs_revision';
        $revisionNote      = $application['revision_note'] ?? null;
        $revisionPageIds   = $revisionRequested
            ? (json_decode($application['revision_pages'] ?? '[]', true) ?: [])
            : [];

        $revisionPageTitles = [];
        if ($revisionPageIds) {
            $revisionPageTitles = array_column(
                (new FormPageModel())->whereIn('id', $revisionPageIds)->orderBy('sort_order', 'ASC')->findAll(),
                'title'
            );
        }

        // ── Program ID ─────────────────────────────
        $programId = $application['program_id']
            ?? $applicant['program_id']
            ?? session()->get('selected_program_id');

        // ── Form Completion ───────────────────────
        if ($programId) {
            $presetSvc = new PresetService();
            $formPages = $presetSvc->getFormPagesForApplicant((int) $programId);

            if (empty($formPages)) {
                $formPages = (new FormPageModel())->getEnabledOrdered();
            }
        } else {
            $formPages = (new FormPageModel())->getEnabledOrdered();
        }

        $formPageCount  = count($formPages);
        $formCompletion = array_fill_keys(array_column($formPages, 'id'), false);

        if ($application) {
            $appId = $application['application_id'];

            $pi = (new PersonalInformationModel())
                ->where('application_id', $appId)
                ->first();

            $fb = (new FamilyBackgroundModel())
                ->where('application_id', $appId)
                ->first();

            $eb = (new EducationalBackgroundModel())
                ->where('application_id', $appId)
                ->first();

            foreach ($formPages as $page) {
                $pageId = (int) $page['id'];

                $formCompletion[$pageId] = match ($pageId) {
                    1 => !empty($application['applicant_type']),
                    2 => !empty($pi['firstname']),
                    3 => !empty($fb['father_lastname']) || !empty($fb['guardian_name']),
                    4 => !empty($eb['school_name']),
                    default => false,
                };
            }
        }

        $formPagesCompleted = count(array_filter($formCompletion));
        $formComplete       = $formPageCount > 0
            && $formPagesCompleted >= $formPageCount;

        $formSubmitted = $application
            && !in_array($appStatus, ['draft', '']);

        // ── Fast Track deferred pages ──────────────
        // Family Background / Educational Background were never required to reach
        // "pending" for a Fast Track applicant — surface which ones are still unfilled
        // so the dashboard can remind them, the same way revision_pages does.
        $pendingFastTrackPages = [];
        if ($isFastTrack && $formSubmitted) {
            $incompleteIds = array_keys(array_filter(
                $formCompletion,
                fn($done, $pageId) => !$done && in_array($pageId, [3, 4], true),
                ARRAY_FILTER_USE_BOTH
            ));

            if ($incompleteIds) {
                $pendingFastTrackPages = array_column(
                    (new FormPageModel())->whereIn('id', $incompleteIds)->orderBy('sort_order', 'ASC')->findAll(),
                    'title'
                );
            }
        }

        // ── Document Completion ───────────────────
        $docStats = [
            'total_required'    => 0,
            'uploaded_required' => 0,
            'approved_required' => 0,
        ];

        if ($programId) {
            $allReqs = (new RequirementService())->getRequirements($programId);

            if (!empty($allReqs)) {
                $uploadedDocs = (new ApplicantDocumentService())
                    ->getStepDocuments($applicant['applicant_id'], $allReqs);

                $requiredReqs = array_filter(
                    $allReqs,
                    fn($r) => $r['is_required']
                );

                $docStats['total_required'] = count($requiredReqs);

                $docStats['uploaded_required'] = count(
                    array_filter(
                        $requiredReqs,
                        fn($r) =>
                            isset($uploadedDocs[$r['program_requirement_id']])
                    )
                );

                $docStats['approved_required'] = count(
                    array_filter(
                        $requiredReqs,
                        fn($r) =>
                            isset($uploadedDocs[$r['program_requirement_id']]) &&
                            ($uploadedDocs[$r['program_requirement_id']]['status'] ?? null) === 'approved'
                    )
                );
            }
        }

        $docsVerified =
            $docStats['total_required'] > 0 &&
            $docStats['approved_required'] >= $docStats['total_required'];

        $docsComplete =
            $docStats['total_required'] > 0
            && $docStats['uploaded_required'] >= $docStats['total_required'];

        // ── Decision ──────────────────────────────
        $decisionMade = in_array(
            $applicantStatus,
            ['approved', 'rejected', 'enrolled']
        );

        $isApproved = in_array(
            $applicantStatus,
            ['approved', 'enrolled']
        );

        $isRejected = $applicantStatus === 'rejected';
        $isEnrolled = $applicantStatus === 'enrolled';

        // ── Exam ──────────────────────────────────
        $examEnabled =
            (new ThemeModel())->getSetting('exam_enabled', '1') === '1';

        $examBooking = null;

        if ($examEnabled) {
            $examBooking = $db->table('exam_bookings eb')
                ->select([
                    'eb.status AS booking_status',
                    'eb.booked_at',
                    'es.exam_date',
                    'es.start_time',
                    'es.location',
                    'eb.checked_in_at',
                    'eb.result_status',
                    'eb.result_remarks',
                ])
                ->join(
                    'exam_schedules es',
                    'es.exam_schedule_id = eb.exam_schedule_id'
                )
                ->where('eb.applicant_id', $applicant['applicant_id'])
                ->whereIn('eb.status', ['booked', 'attended'])
                ->orderBy('es.exam_date', 'ASC')
                ->limit(1)
                ->get()
                ->getRowArray();
        }

        // ── Build Steps ───────────────────────────
        $steps = [
            [
                'title'    => 'Account Created',
                'subtitle' => 'Account and Applicant ID issued.',
                'date'     => $applicant['application_date'] ?? null,
                'status'   => 'done',
                'remarks'  => null,
            ],
            [
                'title'    => 'Application Form Submitted',
                'subtitle' => 'Your application details have been successfully submitted.',
                'date'     => $formSubmitted
                    ? ($application['application_date'] ?? null)
                    : null,
                'status'   => $revisionRequested
                    ? 'active'
                    : (($isFastTrack ? $formSubmitted : $formComplete) ? 'done' : ($formPagesCompleted > 0 ? 'active' : 'pending')),
                'remarks'  => null,
            ],
            [
                'title'    => 'Required Documents Submitted',
                'subtitle' => 'All required supporting documents have been uploaded.',
                'date'     => $docsComplete
                    ? ($application['updated_at'] ?? null)
                    : null,
                'status'   => $docsComplete
                    ? 'done'
                    : ($docStats['uploaded_required'] > 0 ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => 'Documents Verified',
                'subtitle' => 'Admissions staff are reviewing and verifying your submitted documents.',
                'date'     => $docsVerified
                    ? ($application['updated_at'] ?? null)
                    : null,
                'status'   => $docsVerified
                    ? 'done'
                    : ($docsComplete ? 'active' : 'pending'),
                'remarks'  => null,
            ],
        ];

        if ($examEnabled) {
            $examResult = strtoupper($examBooking['result_status'] ?? 'PENDING');

            if ($examBooking) {
                $examDate = date('M j, Y', strtotime($examBooking['exam_date']));
                $examTime = $examBooking['start_time']
                    ? date('g:i A', strtotime($examBooking['start_time']))
                    : '';

                $examVenue = $examBooking['location'] ?? 'TBA';

                $examTitle = match ($examResult) {
                    'PASSED' => 'Entrance Examination Passed',
                    'FAILED' => 'Entrance Examination Failed',
                    default => 'Entrance Examination',
                };

                $examSubtitle = match (true) {
                    $examResult === 'PASSED' =>
                        'You passed the entrance examination. Please wait for the final admission decision.',

                    $examResult === 'FAILED' =>
                        'Your entrance examination result has been released. Please check your email or contact admissions for next steps.',

                    $examBooking['booking_status'] === 'attended' =>
                        'You have completed the exam. Your result is currently being reviewed.',

                    $examBooking['booking_status'] === 'booked' =>
                        'Your exam is scheduled on '
                        . $examDate
                        . ($examTime ? ' at ' . $examTime : '')
                        . ' • '
                        . $examVenue,

                    default =>
                        'Your entrance examination status is being updated.',
                };

                $steps[] = [
                    'title' => $examTitle,
                    'subtitle' => $examSubtitle,
                    'date' => $examBooking['booking_status'] === 'attended'
                        ? $examBooking['checked_in_at'] ?? $examBooking['booked_at']
                        : null,
                    'status' => match (true) {
                        $examResult === 'PASSED' => 'done',
                        $examResult === 'FAILED' => 'rejected',
                        $examBooking['booking_status'] === 'attended' => 'active',
                        $examBooking['booking_status'] === 'booked' => 'active',
                        default => 'pending',
                    },
                    'remarks' => $examBooking['result_remarks'] ?? null,
                ];
            } else {
                $steps[] = [
                    'title' => 'Entrance Examination',
                    'subtitle' => 'Please book your entrance examination schedule once available.',
                    'date' => null,
                    'status' => 'pending',
                    'remarks' => null,
                ];
            }
        }

        $steps[] = [
            'title' => $isRejected
                ? 'Application Declined'
                : 'Admission Decision',
            'subtitle' => $isApproved
                ? 'Your application has been approved.'
                : ($isRejected
                    ? 'Your application was not approved at this time.'
                    : 'Final review; decision released via email.'),
            'date' => $decisionMade
                ? ($applicant['application_date'] ?? null)
                : null,
            'status' => $decisionMade
                ? ($isRejected ? 'rejected' : 'done')
                : 'pending',
            'remarks' => $decisionMade
                ? $decisionRemarks
                : null,
        ];

        $steps[] = [
            'title' => 'Enrollment & Payment',
            'subtitle' => 'Accept offer; complete enrollment.',
            'date' => $isEnrolled
                ? ($applicant['application_date'] ?? null)
                : null,
            'status' => $isEnrolled ? 'done' : 'pending',
            'remarks' => null,
        ];

        return [
            'steps'              => $steps,
            'app_status'         => $applicantStatus,
            'form_status'        => $appStatus,
            'decision_remarks'   => $decisionRemarks,
            'revision_requested' => $revisionRequested,
            'revision_note'      => $revisionNote,
            'revision_pages'     => $revisionPageTitles,
            'form_complete'      => $formComplete,
            'form_submitted'     => $formSubmitted,
            'docs_complete'      => $docsComplete,
            'exam_enabled'       => $examEnabled,
            'exam_booking'       => $examBooking,
            'is_fast_track'      => $isFastTrack,
            'pending_fast_track_pages' => $pendingFastTrackPages,
        ];
    }
}