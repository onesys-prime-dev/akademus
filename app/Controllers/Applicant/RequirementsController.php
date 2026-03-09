<?php
// ApplicantForm.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

class RequirementsController extends BaseController
{
    protected $maxSteps = 5;

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
                'title' => 'Requirements',
                'step_num' => $step,
                'form' => view("applicant/requirements/steps/step-{$step}", [
                    'step_num' => $step
                ])
            ]);
        }

        // Non-AJAX request → full layout
        return $this->renderLayout($step);
    }

    private function renderLayout(int $step) {
        return view('applicant/main', [
            'title' => 'Requirements',
            'page_content' => 'applicant/requirements/requirements',
            'initial_step' => $step,  // Pass to JS to load correct step
            'max_steps' => $this->maxSteps
        ]);
    }
}