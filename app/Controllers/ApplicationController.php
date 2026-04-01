<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ApplicationController extends Controller
{
    protected $formFieldService;
    protected $programModel;
    protected $applicationModel;
    protected $db;

    public function __construct()
    {
        $this->formFieldService = service('FormFieldService');
        $this->programModel = model('ProgramModel');
        $this->applicationModel = model('Application');
        $this->db = \Config\Database::connect();
    }

    /**
     * List available programs to apply to
     */
    public function programSelection()
    {
        $data = [
            'title' => 'Select a Program',
            'programs' => $this->programModel->getAllPrograms(),
        ];

        return view('applicant/program-selection', $data);
    }

    /**
     * Apply for a program - show the form
     */
    public function apply($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            return redirect()->to('/apply')
                ->with('error', 'Program not found');
        }

        // Get the merged form fields for this program (with overrides)
        $formFields = $this->formFieldService->getMergedFormFields($programId);

        $data = [
            'title' => "Apply for {$program['program_name']}",
            'program' => $program,
            'formFields' => $formFields,
        ];

        return view('applicant/application-form', $data);
    }

    /**
     * Submit application form
     */
    public function submitApplication($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Program not found']);
        }

        // Validate submission
        $validation = $this->formFieldService->validateSubmission(
            $programId,
            $this->request->getPost()
        );

        if ($validation !== true) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $validation,
            ]);
        }

        // Get applicant ID from session/auth
        // Your existing applicant system uses applicants.applicant_id and users.user_id
        $applicantId = session()->get('applicant_id') ?? null;
        if (!$applicantId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);
        }

        // Create/update application record in your existing applications table
        $applicationData = [
            'applicant_id' => $applicantId,
            'program_id' => $programId,
            'application_status' => 'pending',
            'application_date' => date('Y-m-d H:i:s'),
        ];

        // Check if application already exists
        $existingApp = $this->db->table('applications')
            ->where('applicant_id', $applicantId)
            ->where('program_id', $programId)
            ->first();

        if ($existingApp) {
            $applicationId = $existingApp->application_id;
            $this->db->table('applications')->update(
                ['application_id' => $applicationId],
                $applicationData
            );
        } else {
            $this->db->table('applications')->insert($applicationData);
            $applicationId = $this->db->insertID();
        }

        // TODO: Store form answers in your existing personal_informations, 
        // educational_backgrounds, family_backgrounds tables
        // This depends on your existing form step structure

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Application submitted successfully',
            'applicationId' => $applicationId,
        ]);
    }

    /**
     * Get form fields as JSON (for AJAX)
     */
    public function getFormFields($programId)
    {
        $fields = $this->formFieldService->getMergedFormFields($programId);

        return $this->response->setJSON([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    /**
     * View submitted application
     */
    public function viewApplication($applicationId)
    {
        $application = $this->db->table('applications')
            ->where('application_id', $applicationId)
            ->first();

        if (!$application) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        // Check authorization (ensure applicant owns this application)
        $applicantId = session()->get('applicant_id');
        if ($application->applicant_id != $applicantId) {
            return redirect()->to('/')->with('error', 'Unauthorized');
        }

        $program = $this->programModel->find($application->program_id);
        $fields = $this->formFieldService->getMergedFormFields($application->program_id);

        // TODO: Fetch stored answers from your existing tables
        $answers = [];

        $data = [
            'title' => 'Your Application',
            'application' => (array)$application,
            'program' => $program,
            'fields' => $fields,
            'answers' => $answers,
        ];

        return view('applicant/application-view', $data);
    }
}
