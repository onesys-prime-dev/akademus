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
use App\Services\NotificationService;

class ApplicantFormController extends BaseController
{
    protected NotificationService $notificationService;
    private ?string $stepValidationError = null;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }
    
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

    private function requireLogin(): void
    {
        if (!session()->get('logged_in')) {
            redirect()->to('sign-in')->send();
            exit;
        }
    }

    public function index()
    {
        $this->requireLogin();
        $applicantId = session()->get('applicant_id');

        $application = $applicantId
            ? (new ApplicationModel())->where('applicant_id', $applicantId)->first()
            : null;

        // Restore program from any existing application (draft or submitted)
        if ($application && !session()->get('selected_program_id') && !empty($application['program_id'])) {
            session()->set('selected_program_id', (int)$application['program_id']);
        }

        // Brand-new applicant, hasn't picked a path yet — send them to the chooser
        // before they ever see the step wizard.
        if (!$application && session()->get('fast_track_choice') === null) {
            return redirect()->to('applicant-form/choose-path');
        }

        return $this->renderLayout(1);
    }

    public function choosePath()
    {
        $this->requireLogin();
        $applicantId = session()->get('applicant_id');

        // Already has an application — nothing left to choose, skip straight into the wizard.
        $hasApplication = $applicantId
            && (new ApplicationModel())->where('applicant_id', $applicantId)->first();

        if ($hasApplication) {
            return redirect()->to('applicant-form');
        }

        $themeModel = new ThemeModel();
        $theme      = $themeModel->getAllSettings();
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);

        $fastTrackFee = (new \App\Models\Payment\PaymentFeeTypeModel())
            ->where('code', 'FAST_TRACK_FEE')
            ->first();

        return view('applicant/main', [
            'title'                 => 'Choose Your Application Path',
            'page_content'          => 'applicant/applicant-form/choose-path',
            'theme'                 => $theme,
            'fast_track_fee_amount' => $fastTrackFee['amount'] ?? 1000,
        ]);
    }

    public function submitChoosePath()
    {
        $this->requireLogin();

        $path = $this->request->getPost('path');
        session()->set('fast_track_choice', $path === 'fast_track');

        return redirect()->to('applicant-form');
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
        $this->requireLogin();
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

        $revisionPageIds = [];
        if ($application && $application['application_status'] === 'needs_revision') {
            $revisionPageIds = json_decode($application['revision_pages'] ?? '[]', true) ?: [];
        }

        // Family Background (3) and Educational Background (4) stay editable indefinitely
        // for a Fast Track applicant — they were never required to reach 'pending' in the
        // first place, so there's nothing to "lock" once the initial pages are submitted.
        $isDeferredPage = $application
            && !empty($application['is_fast_track'])
            && in_array($pageId, [3, 4], true);

        $isSubmitted = $application
            && !in_array($application['application_status'], ['draft'])
            && !($application['application_status'] === 'needs_revision' && in_array($pageId, $revisionPageIds, true))
            && !$isDeferredPage;

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

        $viewData = [
            'step_num' => $step,      // position: 1,2,3,4 — used for form id
            'page_id'  => $pageId,    // actual DB id — used for field configs
            'data'     => $formData,  // pre-populate fields server-side
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title'        => 'Applicant Information Form',
                'step_num'     => $step,
                'page_id'      => $pageId,
                'is_submitted' => $isSubmitted,
                'is_fast_track' => !empty($application['is_fast_track']),
                'form'         => view($viewFile, $viewData),
                'formData'     => $formData,
            ]);
        }

        return $this->renderLayout($step);
    }

    public function updateForm(int $step)
    {
        $this->requireLogin();
        $data   = $this->request->getPost();
        $action = $data['action'] ?? 'draft';

        $pages  = $this->getEnabledPages();
        $page   = $pages[$step - 1] ?? null;
        $pageId = $page ? (int)$page['id'] : null;

        $applicantId      = session()->get('applicant_id');
        $applicationModel = new ApplicationModel();

        $existingApplication = $applicationModel
            ->where('applicant_id', $applicantId)
            ->first();

        $isDraft = $existingApplication && $existingApplication['application_status'] === 'draft';

        $revisionPageIds = [];
        if ($existingApplication && $existingApplication['application_status'] === 'needs_revision') {
            $revisionPageIds = json_decode($existingApplication['revision_pages'] ?? '[]', true) ?: [];
        }
        $isRevisableStep = $existingApplication
            && $existingApplication['application_status'] === 'needs_revision'
            && in_array($pageId, $revisionPageIds, true);

        // Family/Educational Background stay editable indefinitely for a Fast Track
        // applicant — they were deferred, not "submitted then locked".
        $isDeferredStep = $existingApplication
            && !empty($existingApplication['is_fast_track'])
            && in_array($pageId, [3, 4], true);

        // Block edits on already-submitted applications, except for steps flagged for
        // revision or deferred Fast Track steps.
        if ($existingApplication && !$isDraft && !$isRevisableStep && !$isDeferredStep) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Your application has already been submitted and cannot be edited.',
            ]);
        }

        if (!$existingApplication) {
            $applicationId = $applicationModel->insert([
                'applicant_id'       => $applicantId,
                'applicant_type'     => $data['applicant_type'] ?? null,
                'program_id'         => $data['program_id']     ?? null,
                'sy_semester'        => $data['sy_semester']    ?? null,
                // Decided on the Choose Your Path screen before the wizard was ever
                // reached (ApplicantFormController::submitChoosePath()), not from this POST.
                'is_fast_track'      => session()->get('fast_track_choice') ? 1 : 0,
                'application_status' => $action === 'submit' ? 'pending' : 'draft',
            ]);

            (new PersonalInformationModel())->insert(['application_id' => $applicationId]);
            (new FamilyBackgroundModel())->insert(['application_id'    => $applicationId]);
            (new EducationalBackgroundModel())->insert(['application_id'=> $applicationId]);
        } else {
            $applicationId = $existingApplication['application_id'];
        }

        // Route to correct model using page_id, NOT step position
        $result = match($pageId) {
            1 => $this->updateApplication($data, $applicationId, $action, $isRevisableStep), // Application Type
            2 => $this->updatePersonalInformation($data, $applicationId),            // Personal Information
            3 => $this->updateFamilyBackground($data, $applicationId),               // Family Background
            4 => $this->updateEducationalBackground($data, $applicationId, $action), // Educational Background
            default => true,
        };

        $isFastTrack = $existingApplication
            ? !empty($existingApplication['is_fast_track'])
            : (bool) session()->get('fast_track_choice');

        // A Fast Track applicant finalizes right after Personal Information (page 2) —
        // Family/Educational Background are deferred, not required to reach "pending".
        // A revision-mode step is only ever autosaved (action stays 'draft' client-side),
        // but guard defensively against finalizing submission mid-revision anyway.
        $isFinalizingStep = $isFastTrack ? ($pageId === 2) : ($step === count($pages));
        if ($isFinalizingStep && $action === 'submit' && !$isRevisableStep) {
            $applicationModel->update($applicationId, ['application_status' => 'pending']);

            $this->notificationService->trigger('application.submitted', $applicantId, [
                'application_id' => $applicationId,
            ]);
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
            'message' => $result ? 'Step saved successfully' : ($this->stepValidationError ?? 'Failed to save step'),
            'reload'  => $reload,
        ]);
    }

    public function resubmit()
    {
        $this->requireLogin();
        $applicantId = session()->get('applicant_id');
        $pageId      = $this->request->getPost('page_id');

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->where('applicant_id', $applicantId)->first();

        if (!$application || $application['application_status'] !== 'needs_revision') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'There is no pending revision to resubmit.',
            ]);
        }

        // A per-step "Submit" only resolves the step it was clicked from — clear that
        // one page out of the flagged list and only close out the whole revision once
        // none remain. The page-less call (tracking page's "Resubmit Application") is a
        // deliberate "I've addressed everything" action and always closes it out fully.
        if ($pageId !== null) {
            $revisionPageIds = json_decode($application['revision_pages'] ?? '[]', true) ?: [];
            $revisionPageIds = array_values(array_diff($revisionPageIds, [(int) $pageId]));

            if ($revisionPageIds !== []) {
                $applicationModel->update($application['application_id'], [
                    'revision_pages' => json_encode($revisionPageIds),
                ]);

                return $this->response->setJSON(['status' => 'success']);
            }
        }

        $applicationModel->update($application['application_id'], [
            'application_status' => 'pending',
            'revision_note'      => null,
            'revision_pages'     => null,
        ]);

        $this->notificationService->trigger('application.submitted', $applicantId, [
            'application_id' => $application['application_id'],
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function updateApplication($data, $applicationId, $action, bool $preserveStatus = false)
    {
        $fields = [
            'applicant_type' => $data['applicant_type'] ?? null,
            'program_id'     => $data['program_id']     ?? null,
            'school_year'    => $data['school_year']    ?? null,
            'semester'       => $data['semester']       ?? null,
            // is_fast_track is decided once on the Choose Your Path screen and set at
            // insert time (see updateForm()) — not resubmitted from this form anymore.
        ];

        // While a revision is in progress, autosaving page 1 must not stomp
        // 'needs_revision' back to 'draft'/'pending'.
        if (!$preserveStatus) {
            $fields['application_status'] = $action === 'submit' ? 'pending' : 'draft';
        }

        return (new ApplicationModel())->update($applicationId, $fields);
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
        $yearError = $this->validateSchoolYears($data['school_year_start'] ?? '', $data['school_year_end'] ?? '');
        if ($yearError !== null) {
            $this->stepValidationError = $yearError;
            return false;
        }

        $gwaError = $this->validateGwa($data['gwa'] ?? '');
        if ($gwaError !== null) {
            $this->stepValidationError = $gwaError;
            return false;
        }

        return (new EducationalBackgroundModel())
            ->where('application_id', $applicationId)
            ->set([
                'school_name'       => $data['school_name']     ?? null,
                'school_location'   => $data['school_location'] ?? null,
                'strand'            => $data['strand']          ?? null,
                'school_year_start' => $data['school_year_start'] ?? null,
                'school_year_end'   => $data['school_year_end']   ?? null,
                'gwa'               => $data['gwa']             ?? null,
                'school_type'       => $data['school_type']     ?? null,
            ])->update();
    }

    /**
     * Returns an error message if the given school year start/end are invalid,
     * or null if they're valid (or left blank, e.g. an in-progress draft).
     */
    private function validateSchoolYears($start, $end): ?string
    {
        if ($start === '' && $end === '') {
            return null;
        }

        $currentYear = (int) date('Y');

        foreach ([$start, $end] as $value) {
            if ($value === '') {
                continue;
            }
            if (!ctype_digit((string) $value) || (int) $value < 2000 || (int) $value > $currentYear) {
                return 'School year must be between 2000 and ' . $currentYear . '.';
            }
        }

        if ($start !== '' && $end !== '' && (int) $end <= (int) $start) {
            return 'School year end must be after the start year.';
        }

        return null;
    }

    /**
     * Returns an error message if the given GWA is invalid, or null if it's
     * valid (or left blank, e.g. an in-progress draft).
     */
    private function validateGwa($gwa): ?string
    {
        if ($gwa === '') {
            return null;
        }

        if (!is_numeric($gwa) || (float) $gwa < 60 || (float) $gwa > 100) {
            return 'General average (GWA) must be between 60 and 100.';
        }

        return null;
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