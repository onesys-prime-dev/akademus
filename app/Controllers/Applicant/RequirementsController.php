<?php
// Requirements.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Services\RequirementService;
use App\Services\ApplicantDocumentService;

class RequirementsController extends BaseController
{
    protected $requirementService;
    protected $documentService;


    public function __construct() {
        $this->requirementService = new RequirementService();
        $this->documentService = new ApplicantDocumentService();
    }

    public function index() {
        // Default page load goes to step 1
        return $this->renderLayout(1);
    }

    private function renderLayout(int $step) {
        // Check user if logged in
        if (!session()->get('logged_in')) { 
            return redirect()->to('/sign-in');
        }

        $categories = $this->requirementService->getCategories();
        $maxSteps = count($categories) + 1;

        return view('applicant/main', [
            'title' => 'Requirements',
            'page_content' => 'applicant/requirements/requirements',
            'initial_step' => $step,  // Pass to JS to load correct step
            'max_steps' => $maxSteps,
            'categories' => $categories
        ]);
    }

    public function getStep($step)
    {
        $step = (int) $step;

        $categories = $this->requirementService->getCategories();
        $maxSteps = count($categories) + 1;

        if ($step < 1 || $step > $maxSteps) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $program_id = session()->get('program_id');

        if ($step == $maxSteps) {

            $requirements = $this->requirementService->getRequirements($program_id);

            $category_name = 'Review';
            $category_description = 'Review all your uploaded documents';

        } else {

            $category = $categories[$step - 1];

            $requirements = $this->requirementService->getStepRequirements($program_id, $step);

            $category_name = $category['category_name'];
            $category_description = $category['description'];
        }

        $applicant_id = session()->get('applicant_id');
        
        $formData = $this->documentService->getStepDocuments($applicant_id, $requirements);

        if ($this->request->isAJAX()) {

            return $this->response->setJSON([
                'title' => 'Requirements',
                'step_num' => $step,
                'form' => view("applicant/requirements/steps/step", [
                    'step_num' => $step,
                    'requirements' => $requirements,
                    'category_name' => $category_name,
                    'category_description' => $category_description
                ]),
                'formData' => $formData
            ]);
        }

        return $this->renderLayout($step);
    }

    public function updateDocument($step) {
        $applicant_id = session()->get('applicant_id');

        $files = $this->request->getFiles();

        $this->documentService->insertDocuments($applicant_id, $files);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
}