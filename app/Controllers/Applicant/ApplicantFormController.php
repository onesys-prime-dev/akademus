<?php
// app/Controllers/ApplicantForm.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\Application\ApplicationModel;
use App\Models\Application\PersonalInformationModel;
use App\Models\Application\FamilyBackgroundModel;
use App\Models\Application\EducationalBackgroundModel;
use App\Models\FormPageModel;
use App\Models\ThemeModel;

class ApplicantFormController extends BaseController
{
    private function getEnabledPages(): array
    {
        $programId = session()->get('selected_program_id');
        if ($programId) {
            $pages = (new \App\Services\PresetService())->getFormPagesForApplicant($programId);
            if (!empty($pages)) {
                return array_values($pages);
            }
        }
        return (new FormPageModel())->getEnabledOrdered();
    }

    private function getFormDataForPage(int $pageId, $applicationId): array
    {
        $application = (new ApplicationModel())->find($applicationId);
        $personal    = (new PersonalInformationModel())->where('application_id', $applicationId)->first();
        $family      = (new FamilyBackgroundModel())->where('application_id', $applicationId)->first();
        $education   = (new EducationalBackgroundModel())->where('application_id', $applicationId)->first();

        // ── Keyed by ACTUAL page_id from your form_pages table ──
        $map = [
            1 => array_merge($application ?? [], [  // Application Type
                'sy_semester' => ($application['school_year'] ?? '') . ', ' . ($application['semester'] ?? '')
            ]),
            2 => $personal ?? [],                   // Personal Information
            3 => $family   ?? [],                   // Family Background
            4 => array_merge($education ?? [], [    // Educational Background
                'school_year' => ($education['school_year_start'] ?? '') . '-' . ($education['school_year_end'] ?? '')
            ]),
        ];

        return $map[$pageId] ?? [];
    }

    public function index()
    {
        $applicantId = session()->get('applicant_id');

        // Restore program from any existing application (draft or submitted)
        if ($applicantId && !session()->get('selected_program_id')) {
            $application = (new ApplicationModel())
                ->where('applicant_id', $applicantId)
                ->first();
            if ($application && !empty($application['program_id'])) {
                session()->set('selected_program_id', (int)$application['program_id']);
            }
        }

        return $this->renderLayout(1);
    }

    private function isSubmitted(): bool
    {
        $applicantId = session()->get('applicant_id');
        if (!$applicantId) return false;

        $application = (new ApplicationModel())
            ->where('applicant_id', $applicantId)
            ->first();

        // Any status other than 'draft' (or no application) means not yet submitted
        if (!$application) return false;

        return !in_array($application['application_status'], ['draft']);
    }

    private function renderLayout(int $step)
    {
        $themeModel = new \App\Models\ThemeModel();
        $theme      = $themeModel->getAllSettings();
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);

        $pages    = $this->getEnabledPages();
        $maxSteps = count($pages);

        return view('applicant/main', [
            'title'        => 'Applicant Information Form',
            'page_content' => 'applicant/applicant-form/applicant-form',
            'initial_step' => $step,
            'max_steps'    => $maxSteps,
            'is_submitted' => $this->isSubmitted(),
            'theme'        => $theme,
        ]);
    }

    public function getStep(int $step)
    {
        $pages    = $this->getEnabledPages();
        $maxSteps = count($pages);

        if ($step < 1 || $step > $maxSteps) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = $pages[$step - 1];
        $pageId = (int) $page['id'];

        $applicantId = session()->get('applicant_id');
        $formData    = [];

        // Fetch any application (including submitted ones) so the form data
        // can be populated for read-only viewing after submission.
        $application = (new ApplicationModel())
            ->where('applicant_id', $applicantId)
            ->first();

        $isSubmitted = $application && !in_array($application['application_status'], ['draft']);

        if ($application) {
            $formData = $this->getFormDataForPage($pageId, $application['application_id']);
        }

        // ✅ ADD THIS BLOCK
        if (empty($formData['program_id'])) {
            $formData['program_id'] = session()->get('program_id');
        }

        // Load view by page_id — step_1.php = Application Type, step_4.php = Educational Background, etc.
        $viewFile = "applicant/applicant-form/steps/step-{$pageId}";
        if (!file_exists(APPPATH . "Views/{$viewFile}.php")) {
            $viewFile = 'applicant/applicant-form/steps/step-generic';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title'        => 'Applicant Information Form',
                'step_num'     => $step,
                'page_id'      => $pageId,
                'is_submitted' => $isSubmitted,
                'form'         => view($viewFile, [
                    'step_num' => $step,      // position: 1,2,3,4 — used for form id
                    'page_id'  => $pageId,    // actual DB id — used for field configs
                    'data'     => $formData,  // pre-populate fields server-side
                ]),
                'formData'     => $formData,
            ]);
        }

        return $this->renderLayout($step);
    }

    public function updateForm(int $step)
    {
        $data   = $this->request->getPost();
        $action = $data['action'] ?? 'draft';

        $pages  = $this->getEnabledPages();
        $page   = $pages[$step - 1] ?? null;
        $pageId = $page ? (int)$page['id'] : null;

        $applicantId      = session()->get('applicant_id');
        $applicationModel = new ApplicationModel();

        // Block edits on already-submitted applications
        $application = $applicationModel
            ->where('applicant_id', $applicantId)
            ->where('application_status', 'draft')
            ->first();

        if (!$application && $applicationModel->where('applicant_id', $applicantId)->first()) {
            // Application exists but is no longer a draft — reject the save
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Your application has already been submitted and cannot be edited.',
            ]);
        }

        if (!$application) {
            $applicationId = $applicationModel->insert([
                'applicant_id'       => $applicantId,
                'applicant_type'     => $data['applicant_type'] ?? null,
                'program_id'         => $data['program_id']     ?? null,
                'sy_semester'        => $data['sy_semester']    ?? null,
                'application_status' => $action === 'submit' ? 'pending' : 'draft',
            ]);

            (new PersonalInformationModel())->insert(['application_id' => $applicationId]);
            (new FamilyBackgroundModel())->insert(['application_id'    => $applicationId]);
            (new EducationalBackgroundModel())->insert(['application_id'=> $applicationId]);
        } else {
            $applicationId = $application['application_id'];
        }

        // Route to correct model using page_id, NOT step position
        $result = match($pageId) {
            1 => $this->updateApplication($data, $applicationId, $action),           // Application Type
            2 => $this->updatePersonalInformation($data, $applicationId),            // Personal Information
            3 => $this->updateFamilyBackground($data, $applicationId),               // Family Background
            4 => $this->updateEducationalBackground($data, $applicationId, $action), // Educational Background
            default => true,
        };

        $isLastStep = ($step === count($pages));
        if ($isLastStep && $action === 'submit') {
            $applicationModel->update($applicationId, ['application_status' => 'pending']);
        }

        // When program is selected on step 1, store it in session so presets load correctly
        $reload = false;
        if ($pageId === 1 && $action !== 'draft') {
            $programId = $this->request->getPost('program_id');
            if ($programId && (int)$programId !== (int)session()->get('selected_program_id')) {
                session()->set('selected_program_id', (int)$programId);
                $reload = true;
            }
        }

        return $this->response->setJSON([
            'status'  => $result ? 'success' : 'error',
            'message' => $result ? 'Step saved successfully' : 'Failed to save step',
            'reload'  => $reload,
        ]);
    }

    private function updateApplication($data, $applicationId, $action)
    {
        return (new ApplicationModel())->update($applicationId, [
            'applicant_type'     => $data['applicant_type'] ?? null,
            'program_id'         => $data['program_id']     ?? null,
            'school_year'        => $data['school_year']    ?? null,
            'semester'           => $data['semester']       ?? null,
            'application_status' => $action === 'submit' ? 'pending' : 'draft',
        ]);
    }

    private function updatePersonalInformation($data, $applicationId)
    {
        return (new PersonalInformationModel())
            ->where('application_id', $applicationId)
            ->set([
                'firstname'      => $data['firstname']      ?? null,
                'middlename'     => $data['middlename']     ?? null,
                'lastname'       => $data['lastname']       ?? null,
                'suffix'         => $data['suffix']         ?? null,
                'gender'         => $data['gender']         ?? null,
                'date_of_birth'  => $data['date_of_birth']  ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'citizenship'    => $data['citizenship']    ?? null,
                'home_address'   => $data['home_address']   ?? null,
                'phone_code'     => $data['phone_code']     ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'email_address'  => $data['email_address']  ?? null,
            ])->update();
    }

    private function updateFamilyBackground($data, $applicationId)
    {
        return (new FamilyBackgroundModel())
            ->where('application_id', $applicationId)
            ->set([
                'father_firstname'        => $data['father_firstname']        ?? null,
                'father_middlename'       => $data['father_middlename']       ?? null,
                'father_lastname'         => $data['father_lastname']         ?? null,
                'father_occupation'       => $data['father_occupation']       ?? null,
                'father_contact_number'   => $data['father_contact_number']   ?? null,
                'mother_firstname'        => $data['mother_firstname']        ?? null,
                'mother_middlename'       => $data['mother_middlename']       ?? null,
                'mother_lastname'         => $data['mother_lastname']         ?? null,
                'mother_occupation'       => $data['mother_occupation']       ?? null,
                'mother_contact_number'   => $data['mother_contact_number']   ?? null,
                'guardian_name'           => $data['guardian_name']           ?? null,
                'guardian_relationship'   => $data['guardian_relationship']   ?? null,
                'guardian_contact_number' => $data['guardian_contact_number'] ?? null,
                'annual_family_income'    => $data['annual_family_income']    ?? null,
            ])->update();
    }

    private function updateEducationalBackground($data, $applicationId, $action)
    {
        $schoolYear = preg_split('/\s*-\s*/', $data['school_year'] ?? '');

        return (new EducationalBackgroundModel())
            ->where('application_id', $applicationId)
            ->set([
                'school_name'       => $data['school_name']     ?? null,
                'school_location'   => $data['school_location'] ?? null,
                'strand'            => $data['strand']          ?? null,
                'school_year_start' => $schoolYear[0]           ?? null,
                'school_year_end'   => $schoolYear[1]           ?? null,
                'gwa'               => $data['gwa']             ?? null,
                'school_type'       => $data['school_type']     ?? null,
            ])->update();
    }

    private function lightenHex(string $hex, float $amount = 0.88): string
    {
        $hex = ltrim($hex, '#');
        $r   = (int)round(hexdec(substr($hex, 0, 2)) + (255 - hexdec(substr($hex, 0, 2))) * $amount);
        $g   = (int)round(hexdec(substr($hex, 2, 2)) + (255 - hexdec(substr($hex, 2, 2))) * $amount);
        $b   = (int)round(hexdec(substr($hex, 4, 2)) + (255 - hexdec(substr($hex, 4, 2))) * $amount);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}