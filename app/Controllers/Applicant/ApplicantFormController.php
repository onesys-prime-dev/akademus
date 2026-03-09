<?php
// ApplicantForm.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\Application\ApplicationModel;
use App\Models\Application\PersonalInformationModel;
use App\Models\Application\FamilyBackgroundModel;
use App\Models\Application\EducationalBackgroundModel;

class ApplicantFormController extends BaseController
{
    protected $maxSteps = 4;

    public function index() {
        // Default page load goes to step 1
        return $this->renderLayout(1);
    }

    private function renderLayout(int $step) {
        // Check user if logged in
        if (!session()->get('logged_in')) { 
            return redirect()->to('/sign-in');
        }

        return view('applicant/main', [
            'title' => 'Applicant Information Form',
            'page_content' => 'applicant/applicant-form/applicant-form',
            'initial_step' => $step,  // Pass to JS to load correct step
            'max_steps' => $this->maxSteps
        ]);
    }

    public function getStep($step)
    {
        $step = (int) $step;

        if ($step < 1 || $step > $this->maxSteps) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $applicant_id = session()->get('applicant_id'); // replace later with session

        $applicationModel = new ApplicationModel();
        $personalModel    = new PersonalInformationModel();
        $familyModel      = new FamilyBackgroundModel();
        $educationModel   = new EducationalBackgroundModel();

        $application = $applicationModel
            ->where('applicant_id', $applicant_id)
            ->where('application_status !=', 'pending')
            ->first();

        $formData = [];

        if ($application) {

            $application_id = $application['application_id'];

            switch ($step) {
                case 1:
                    $formData = $application;

                    $formData['sy_semester'] = $formData['school_year'] . ', ' . $formData['semester'];
                    break;

                case 2:
                    $formData = $personalModel
                        ->where('application_id', $application_id)
                        ->first();
                    break;

                case 3:
                    $formData = $familyModel
                        ->where('application_id', $application_id)
                        ->first();
                    break;

                case 4:
                    $formData = $educationModel
                        ->where('application_id', $application_id)
                        ->first();
                        
                    $formData['school_year'] = $formData['school_year_start'] . '-' . $formData['school_year_end'];

                    if ($formData['school_year_start'] <= 0 || $formData['school_year_end'] <= 0) {
                        $formData['school_year'] = null;
                    }

                    if ($formData['gwa'] <= 0) {
                        $formData['gwa'] = null;
                    }
                    break;
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title' => 'Applicant Information Form',
                'step_num' => $step,
                'form' => view("applicant/applicant-form/steps/step-{$step}", [
                    'step_num' => $step
                ]),
                'formData' => $formData
            ]);
        }

        return $this->renderLayout($step);
    }

    public function updateForm($step)
    {
        $data = $this->request->getPost();
        $action = $data['action'] ?? 'draft';

        // SAMPLE applicant_id
        $applicant_id = session()->get('applicant_id');

        $applicationModel = new ApplicationModel();

        // Check if draft application already exists
        $application = $applicationModel
            ->where('applicant_id', $applicant_id)
            ->where('application_status !=', 'submitted')
            ->first();

        // If no application yet → create it (only once)
        if (!$application) {

            $application_id = $applicationModel->insert([
                'applicant_id' => $applicant_id,
                'applicant_type' => $data['applicant_type'] ?? null,
                'program_id' => $data['program_id'] ?? null,
                'sy_semester' => $data['sy_semester'] ?? null,
                'application_status' => $action === 'submit' ? 'pending' : 'draft'
            ]);

            // Insert child rows immediately
            (new PersonalInformationModel())->insert([
                'application_id' => $application_id
            ]);

            (new FamilyBackgroundModel())->insert([
                'application_id' => $application_id
            ]);

            (new EducationalBackgroundModel())->insert([
                'application_id' => $application_id
            ]);

        } else {
            $application_id = $application['application_id'];
        }

        // Now ALL steps are updates
        switch ($step) {
            case 1:
                $result = $this->updateApplication($data, $application_id, $action);
                break;

            case 2:
                $result = $this->updatePersonalInformation($data, $application_id);
                break;

            case 3:
                $result = $this->updateFamilyBackground($data, $application_id);
                break;

            case 4:
                $result = $this->updateEducationalBackground($data, $application_id, $action);
                break;

            default:
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid step'
                ]);
        }

        return $this->response->setJSON([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Step saved successfully' : 'Failed to save step'
        ]);
    }
    
    private function updateApplication($data, $application_id, $action)
    {
        $model = new ApplicationModel();

        $sy_and_semester = explode(',', $data['sy_semester'] ?? '', 2);

        $school_year = trim($sy_and_semester[0] ?? null);
        $semester    = trim($sy_and_semester[1] ?? null);

        return $model->update($application_id, [
            'applicant_type' => $data['applicant_type'] ?? null,
            'program_id' => $data['program_id'] ?? null,
            'school_year' => $school_year,
            'semester' => $semester,
            'application_status' => $action === 'submit' ? 'pending' : 'draft'
        ]);
    }

    private function updatePersonalInformation($data, $application_id)
    {
        $model = new PersonalInformationModel();

        return $model->where('application_id', $application_id)
            ->set([
                'firstname' => $data['firstname'] ?? null,
                'middlename' => $data['middlename'] ?? null,
                'lastname' => $data['lastname'] ?? null,
                'suffix' => $data['suffix'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'citizenship' => $data['citizenship'] ?? null,
                'home_address' => $data['home_address'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'email_address' => $data['email_address'] ?? null
            ])
            ->update();
    }

    private function updateFamilyBackground($data, $application_id)
    {
        $model = new FamilyBackgroundModel();

        return $model->where('application_id', $application_id)
            ->set([
                // Father Details
                'father_firstname' => $data['father_firstname'] ?? null,
                'father_middlename' => $data['father_middlename'] ?? null,
                'father_lastname' => $data['father_lastname'] ?? null,
                'father_occupation' => $data['father_occupation'] ?? null,
                'father_contact_number' => $data['father_contact_number'] ?? null,

                // Mother Details
                'mother_firstname' => $data['mother_firstname'] ?? null,
                'mother_middlename' => $data['mother_middlename'] ?? null,
                'mother_lastname' => $data['mother_lastname'] ?? null,
                'mother_occupation' => $data['mother_occupation'] ?? null,
                'mother_contact_number' => $data['mother_contact_number'] ?? null,

                // Guardian Details
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_relationship' => $data['guardian_relationship'] ?? null,
                'guardian_contact_number' => $data['guardian_contact_number'] ?? null,

                // Family Income
                'annual_family_income' => $data['annual_family_income'] ?? null
            ])
            ->update();
    }

    private function updateEducationalBackground($data, $application_id, $action)
    {
        $model = new EducationalBackgroundModel();

        $schoolYear = preg_split('/\s*-\s*/', $data['school_year'] ?? '');

        $educationUpdated = $model->where('application_id', $application_id)
            ->set([
                'school_name' => $data['school_name'] ?? null,
                'school_location' => $data['school_location'] ?? null,
                'strand' => $data['strand'] ?? null,
                'school_year_start' => $schoolYear[0] ?? null,
                'school_year_end' => $schoolYear[1] ?? null,
                'gwa' => $data['gwa'] ?? null,
                'school_type' => $data['school_type'] ?? null
            ])
            ->update();

        if ($action === 'submit' && $educationUpdated) {
            (new ApplicationModel())->update($application_id, [
                'application_status' => 'pending'
            ]);
        }

        return $educationUpdated;
    }
}