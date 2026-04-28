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

        // ── Document Completion ───────────────────
        $docStats = [
            'total_required'    => 0,
            'uploaded_required' => 0,
        ];

        if ($programId) {
            $allReqs = (new RequirementService())->getRequirements($programId);

            if (!empty($allReqs)) {
                $uploadedDocs = (new ApplicantDocumentService())
                    ->getStepDocuments($applicant['applicant_id'], $allReqs);

                $docStats['total_required'] = count(
                    array_filter($allReqs, fn($r) => $r['is_required'])
                );

                $docStats['uploaded_required'] = count(
                    array_filter(
                        $allReqs,
                        fn($r) =>
                            $r['is_required']
                            && isset($uploadedDocs[$r['program_requirement_id']])
                    )
                );
            }
        }

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
                'title'    => 'Applicant Form Submitted',
                'subtitle' => 'All basic details successfully submitted.',
                'date'     => $formSubmitted
                    ? ($application['application_date'] ?? null)
                    : null,
                'status'   => $formComplete
                    ? 'done'
                    : ($formPagesCompleted > 0 ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => 'Documents Submitted',
                'subtitle' => 'All required files submitted.',
                'date'     => $docsComplete
                    ? ($application['updated_at'] ?? null)
                    : null,
                'status'   => $docsComplete
                    ? 'done'
                    : ($docStats['uploaded_required'] > 0 ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => 'Document Verification',
                'subtitle' => 'Admissions staff reviewing your documents.',
                'date'     => $decisionMade
                    ? ($applicant['application_date'] ?? null)
                    : null,
                'status'   => $decisionMade
                    ? 'done'
                    : ($docsComplete ? 'active' : 'pending'),
                'remarks'  => null,
            ],
        ];

        if ($examEnabled) {
            if ($examBooking) {
                $examDate = date('M j, Y', strtotime($examBooking['exam_date']));
                $examTime = $examBooking['start_time']
                    ? date('g:i A', strtotime($examBooking['start_time']))
                    : '';

                $steps[] = [
                    'title' => $examDate . ($examTime ? ' at ' . $examTime : ''),
                    'subtitle' => 'Venue: ' . ($examBooking['location'] ?? 'TBA'),
                    'date' => $examBooking['booking_status'] === 'attended'
                        ? $examBooking['booked_at']
                        : null,
                    'status' => $examBooking['booking_status'] === 'attended'
                        ? 'done'
                        : 'active',
                    'remarks' => null,
                ];
            } else {
                $steps[] = [
                    'title' => 'Entrance Examination',
                    'subtitle' => 'Your exam schedule will be assigned by the admissions office.',
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
            'steps'            => $steps,
            'app_status'       => $applicantStatus,
            'decision_remarks' => $decisionRemarks,
            'form_complete'    => $formComplete,
            'form_submitted'   => $formSubmitted,
            'docs_complete'    => $docsComplete,
            'exam_enabled'     => $examEnabled,
            'exam_booking'     => $examBooking,
        ];
    }
}