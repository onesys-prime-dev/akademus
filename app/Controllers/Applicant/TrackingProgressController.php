<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;
use App\Models\Applicant\ApplicantModel;
use App\Models\Application\ApplicationModel;
use App\Models\Application\PersonalInformationModel;
use App\Models\Application\FamilyBackgroundModel;
use App\Models\Application\EducationalBackgroundModel;
use App\Models\FormPageModel;
use App\Models\ThemeModel;
use App\Services\RequirementService;
use App\Services\ApplicantDocumentService;

class TrackingProgressController extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        $applicantModel = new ApplicantModel();
        $themeModel     = new ThemeModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$applicant) {
            return redirect()->to('/sign-in');
        }

        $db          = \Config\Database::connect();
        $application = $db->table('applications')
            ->where('applicant_id', $applicant['applicant_id'])
            ->orderBy('application_date', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        // Officer decision status lives on the applicants row
        $applicantStatus = $applicant['application_status'] ?? 'pending';
        $decisionRemarks = $applicant['decision_remarks']   ?? null;

        // Form-level status (draft / pending) lives on the applications row
        $appStatus = $application['application_status'] ?? '';

        // ── Program ID ───────────────────────────────────────────────────
        $programId = $application['program_id']
            ?? $applicant['program_id']
            ?? session()->get('selected_program_id');

        // ── Form Completion ──────────────────────────────────────────────
        if ($programId) {
            $presetSvc = new \App\Services\PresetService();
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
            $pi    = (new PersonalInformationModel())->where('application_id', $appId)->first();
            $fb    = (new FamilyBackgroundModel())->where('application_id', $appId)->first();
            $eb    = (new EducationalBackgroundModel())->where('application_id', $appId)->first();

            foreach ($formPages as $page) {
                $pageId = (int) $page['id'];
                $formCompletion[$pageId] = match($pageId) {
                    1 => !empty($application['applicant_type']),
                    2 => !empty($pi['firstname']),
                    3 => !empty($fb['father_lastname']) || !empty($fb['guardian_name']),
                    4 => !empty($eb['school_name']),
                    default => false,
                };
            }
        }

        $formPagesCompleted = count(array_filter($formCompletion));
        $formComplete       = $formPageCount > 0 && $formPagesCompleted >= $formPageCount;

        // ── Document Stats ───────────────────────────────────────────────
        $docStats = [
            'total_required'    => 0,
            'uploaded_required' => 0,
        ];

        if ($programId) {
            $reqService = new RequirementService();
            $allReqs    = $reqService->getRequirements($programId);

            if (!empty($allReqs)) {
                $docService      = new ApplicantDocumentService();
                $uploadedDocs    = $docService->getStepDocuments($applicant['applicant_id'], $allReqs);

                $docStats['total_required']    = count(array_filter($allReqs, fn($r) => $r['is_required']));
                $docStats['uploaded_required'] = count(array_filter($allReqs, fn($r) => $r['is_required'] && isset($uploadedDocs[$r['program_requirement_id']])));
            }
        }

        $docsComplete = $docStats['total_required'] > 0
            && $docStats['uploaded_required'] >= $docStats['total_required'];

        // ── Step Statuses ────────────────────────────────────────────────
        // done | active | pending
        $formSubmitted = $application && !in_array($appStatus, ['draft', '']);

        $decisionMade = in_array($applicantStatus, ['approved', 'rejected', 'enrolled']);
        $isApproved   = in_array($applicantStatus, ['approved', 'enrolled']);
        $isRejected   = $applicantStatus === 'rejected';
        $isEnrolled   = $applicantStatus === 'enrolled';

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
                'date'     => $formSubmitted ? ($application['application_date'] ?? null) : null,
                'status'   => $formComplete ? 'done' : ($formPagesCompleted > 0 ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => 'Documents Submitted',
                'subtitle' => 'All required files submitted.',
                'date'     => $docsComplete ? ($application['updated_at'] ?? null) : null,
                'status'   => $docsComplete ? 'done'
                    : ($docStats['uploaded_required'] > 0 ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => 'Document Verification',
                'subtitle' => 'Admissions staff reviewing your documents.',
                'date'     => $decisionMade ? ($applicant['application_date'] ?? null) : null,
                'status'   => $decisionMade ? 'done'
                    : ($docsComplete ? 'active' : 'pending'),
                'remarks'  => null,
            ],
            [
                'title'    => $isRejected ? 'Application Declined' : 'Admission Decision',
                'subtitle' => $isApproved
                    ? 'Your application has been approved.'
                    : ($isRejected ? 'Your application was not approved at this time.' : 'Final review; decision released via email.'),
                'date'     => $decisionMade ? ($applicant['application_date'] ?? null) : null,
                'status'   => $decisionMade ? ($isRejected ? 'rejected' : 'done') : 'pending',
                'remarks'  => $decisionMade ? $decisionRemarks : null,
            ],
            [
                'title'    => 'Enrollment & Payment',
                'subtitle' => 'Accept offer; complete enrollment.',
                'date'     => $isEnrolled ? ($applicant['application_date'] ?? null) : null,
                'status'   => $isEnrolled ? 'done' : 'pending',
                'remarks'  => null,
            ],
        ];

        // ── Status Header ────────────────────────────────────────────────
        $data = [
            'title'            => 'Track Submission Progress',
            'page_content'     => 'applicant/tracking-progress/tracking_progress',
            'theme'            => $themeModel->getAllSettings(),
            'applicant_number' => $applicant['applicant_number'] ?? 'N/A',
            'app_status'       => $applicantStatus,
            'steps'            => $steps,
            'form_complete'    => $formComplete,
            'form_submitted'   => $formSubmitted,
            'docs_complete'    => $docsComplete,
            'decision_remarks' => $decisionRemarks,
        ];

        return view('applicant/main', $data);
    }
}
