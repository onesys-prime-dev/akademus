<?php
// ApplicantForm.php

namespace App\Controllers;

use App\Models\ApplicationTypeModel;
use App\Models\PersonalInformationModel;
use App\Models\FamilyBackgroundModel;
use App\Models\EducationalBackgroundModel;

class ApplicantForm extends BaseController
{
    protected $maxSteps = 4;

    public function index() {
        // Default page load goes to step 1
        return $this->renderLayout(1);
    }

    public function getStep($step) {
        $step = (int) $step;

        if ($step < 1 || $step > $this->maxSteps) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if AJAX request
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title' => 'Applicant Information Form',
                'step_num' => $step,
                'form' => view("dashboard/menu/applicant_form_steps/step_{$step}", [
                    'step_num' => $step
                ])
            ]);
        }

        // Non-AJAX request → full layout
        return $this->renderLayout($step);
    }

    private function renderLayout(int $step) {
        return view('dashboard/main', [
            'title' => 'Applicant Information Form',
            'page_content' => 'dashboard/menu/applicant_form',
            'initial_step' => $step,  // Pass to JS to load correct step
            'max_steps' => $this->maxSteps
        ]);
    }

    public function updateForm($step) {
        $data = $this->request->getPost(); // collect POSTed form data
        $action = $data['action'] ?? 'draft';

        // You can validate user/session here
        // if (!$this->session->has('applicant_id')) { ... }

        switch ($step) {
            case 1:
                $result = $this->insertApplicationType($data);
                break;

            case 2:
                $result = $this->insertPersonalInformation($data);
                break;

            case 3:
                $result = $this->insertFamilyBackground($data);
                break;

            case 4:
                $result = $this->insertEducationalBackground($data);
                break;

            default:
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid step'
                ]);
        }

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Step saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save step'
            ]);
        }
    }
    
    private function insertApplicationType($data) {
        $model = new ApplicationTypeModel();

        // Example: validation
        if (empty($data['education_level']) || empty($data['program'])) {
            return false;
        }

        return $model->insert([
            'education_level' => $data['education_level'],
            'program' => $data['program'],
            'sy_semester' => $data['sy_semester'] ?? null,
            'status' => $data['action'] === 'submit' ? 'submitted' : 'draft'
        ]);
    }

    private function insertPersonalInformation($data) {
        $model = new PersonalInformationModel();

        // Example validation
        if (empty($data['first_name']) || empty($data['last_name'])) return false;

        return $model->insert([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'] ?? null,
            'gender' => $data['gender'],
            'birth_date' => $data['birth_date'] ?? null,
            'status' => $data['action'] === 'submit' ? 'submitted' : 'draft'
        ]);
    }

    private function insertFamilyBackground($data) {
        $model = new FamilyBackgroundModel();

        // Example validation: at least one parent
        if (empty($data['father_name']) && empty($data['mother_name'])) return false;

        return $model->insert([
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'status' => $data['action'] === 'submit' ? 'submitted' : 'draft'
        ]);
    }

    private function insertEducationalBackground($data) {
        $model = new EducationalBackgroundModel();

        // Example validation: at least one school attended
        if (empty($data['last_school'])) return false;

        return $model->insert([
            'last_school' => $data['last_school'],
            'last_school_address' => $data['last_school_address'] ?? null,
            'gpa' => $data['gpa'] ?? null,
            'status' => $data['action'] === 'submit' ? 'submitted' : 'draft'
        ]);
    }
}