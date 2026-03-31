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
            ->get()->getRowArray();

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

        $steps = [
            [
                'title'    => 'Account Created',
                'subtitle' => 'Account and Applicant ID issued.',
                'date'     => $applicant['application_date'] ?? null,
                'status'   => 'done',
            ],
            [
                'title'    => 'Applicant Form Submitted',
                'subtitle' => 'All basic details successfully submitted.',
                'date'     => $formSubmitted ? ($application['application_date'] ?? null) : null,
                'status'   => $formComplete ? 'done' : ($formPagesCompleted > 0 ? 'active' : 'pending'),
            ],
            [
                'title'    => 'Documents Submitted',
                'subtitle' => 'All required files submitted.',
                'date'     => $docsComplete ? ($application['updated_at'] ?? null) : null,
                'status'   => $docsComplete ? 'done'
                    : ($docStats['uploaded_required'] > 0 ? 'active' : 'pending'),
            ],
            [
                'title'    => 'Document Verification',
                'subtitle' => 'Admissions staff reviewing documents.',
                'date'     => null,
                'status'   => in_array($appStatus, ['approved', 'enrolled']) ? 'done'
                    : ($docsComplete ? 'active' : 'pending'),
            ],
            [
                'title'    => 'Admission Decision',
                'subtitle' => 'Final review; decision released via email.',
                'date'     => in_array($appStatus, ['approved', 'enrolled']) ? ($application['updated_at'] ?? null) : null,
                'status'   => in_array($appStatus, ['approved', 'enrolled']) ? 'done' : 'pending',
            ],
            [
                'title'    => 'Enrollment & Payment',
                'subtitle' => 'Accept offer; complete enrollment.',
                'date'     => $appStatus === 'enrolled' ? ($application['updated_at'] ?? null) : null,
                'status'   => $appStatus === 'enrolled' ? 'done' : 'pending',
            ],
        ];

        // ── Status Header ────────────────────────────────────────────────
        $data = [
            'title'            => 'Track Submission Progress',
            'page_content'     => 'applicant/tracking-progress/tracking_progress',
            'theme'            => $themeModel->getAllSettings(),
            'applicant_number' => $applicant['applicant_number'] ?? 'N/A',
            'app_status'       => $appStatus,
            'steps'            => $steps,
            'form_complete'    => $formComplete,
            'form_submitted'   => $formSubmitted,
            'docs_complete'    => $docsComplete,
        ];

        return view('applicant/main', $data);
    }
}
