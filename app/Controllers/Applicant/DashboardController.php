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

class DashboardController extends BaseController
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

        // ── Document Stats ───────────────────────────────────────────────
        $programId = $application['program_id']
            ?? $applicant['program_id']
            ?? session()->get('selected_program_id');

        // ── Form Completion ──────────────────────────────────────────────
        // Use the same page-fetching logic as the applicant form itself so
        // the dashboard always reflects the preset-filtered page list.
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

        // Completion is checked by the actual page ID (1=AppType, 2=Personal,
        // 3=Family, 4=Education) — consistent with ApplicantFormController.
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
        $formProgress = $formPageCount > 0
            ? (int) round(($formPagesCompleted / $formPageCount) * 100)
            : 0;

        $docStats = [
            'total_required'    => 0,
            'uploaded_required' => 0,
            'missing_required'  => 0,
            'total_optional'    => 0,
            'uploaded_optional' => 0,
            'all_requirements'  => [],
            'uploaded_docs'     => [],
        ];

        if ($programId) {
            $reqService = new RequirementService();
            $allReqs    = $reqService->getRequirements($programId);

            if (!empty($allReqs)) {
                $docService      = new ApplicantDocumentService();
                $uploadedDocs    = $docService->getStepDocuments($applicant['applicant_id'], $allReqs);

                $totalRequired    = count(array_filter($allReqs, fn($r) => $r['is_required']));
                $uploadedRequired = count(array_filter($allReqs, fn($r) => $r['is_required'] && isset($uploadedDocs[$r['program_requirement_id']])));
                $totalOptional    = count(array_filter($allReqs, fn($r) => !$r['is_required']));
                $uploadedOptional = count(array_filter($allReqs, fn($r) => !$r['is_required'] && isset($uploadedDocs[$r['program_requirement_id']])));

                $docStats = [
                    'total_required'    => $totalRequired,
                    'uploaded_required' => $uploadedRequired,
                    'missing_required'  => $totalRequired - $uploadedRequired,
                    'total_optional'    => $totalOptional,
                    'uploaded_optional' => $uploadedOptional,
                    'all_requirements'  => $allReqs,
                    'uploaded_docs'     => $uploadedDocs,
                ];
            }
        }

        $docProgress = $docStats['total_required'] > 0
            ? (int) round(($docStats['uploaded_required'] / $docStats['total_required']) * 100)
            : 0;

        // ── Overall Step ─────────────────────────────────────────────────
        $step = 1;
        if ($application) {
            $step = 2;
            if ($appStatus === 'approved') $step = 4;
            if ($appStatus === 'enrolled') $step = 5;
        }

        // Per-step status: 'done', 'inprogress', or '' (not started)
        $stepStatuses = [
            1 => 'done',
            2 => match(true) {
                $formPagesCompleted <= 0              => '',
                $formPagesCompleted >= $formPageCount => 'done',
                default                               => 'inprogress',
            },
            3 => match(true) {
                $docStats['total_required'] === 0                                   => '',
                $docStats['uploaded_required'] <= 0                                 => '',
                $docStats['uploaded_required'] >= $docStats['total_required']       => 'done',
                default                                                             => 'inprogress',
            },
            4 => match(true) {
                in_array($appStatus, ['approved', 'enrolled'])
                    => 'done',
                $docStats['total_required'] > 0
                    && $docStats['uploaded_required'] >= $docStats['total_required']
                    => 'inprogress',
                default => '',
            },
            5 => $appStatus === 'enrolled' ? 'done' : '',
        ];

        $overallProgress = (int) round(
            (count(array_filter($stepStatuses, fn($s) => $s === 'done')) / 5) * 100
        );

        // ── Banner Message ────────────────────────────────────────────────
        if ($appStatus === 'enrolled') {
            $bannerMessage = 'You are now officially enrolled. Welcome to the family!';
        } elseif ($appStatus === 'approved') {
            $bannerMessage = 'Congratulations! Your application has been approved. Please settle your reservation fee.';
        } elseif ($docProgress === 100) {
            $bannerMessage = 'All documents uploaded! Your application is under review.';
        } elseif ($docProgress > 0) {
            $bannerMessage = 'Your application is in progress. Upload the remaining documents to move to the verification stage.';
        } elseif ($formProgress === 100) {
            $bannerMessage = 'Your form is complete! Proceed to upload the required documents.';
        } elseif ($formProgress > 0) {
            $bannerMessage = 'Your application form is in progress. Continue where you left off.';
        } else {
            $bannerMessage = 'Welcome! Start your application by filling out the applicant form below.';
        }

        $data = [
            'title'            => 'Dashboard',
            'page_content'     => 'applicant/dashboard/dashboard',
            'applicant_number' => $applicant['applicant_number'] ?? 'N/A',
            'firstname'        => session()->get('firstname'),
            'theme'            => $themeModel->getAllSettings(),
            'current_step'     => $step,
            'progress'         => $overallProgress,
            'step_statuses'    => $stepStatuses,
            'banner_message'   => $bannerMessage,
            // Form
            'form_pages'       => $formPages,
            'form_completion'  => $formCompletion,
            'form_completed'   => $formPagesCompleted,
            'form_total'       => $formPageCount,
            'form_progress'    => $formProgress,
            // Documents
            'doc_stats'        => $docStats,
            'doc_progress'     => $docProgress,
        ];

        return view('applicant/main', $data);
    }
}
