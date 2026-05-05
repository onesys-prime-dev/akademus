<?php
// Requirements.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Services\RequirementService;
use App\Services\ApplicantDocumentService;
use App\Services\NotificationService;

use App\Models\ThemeModel;

class RequirementsController extends BaseController
{
    protected $requirementService;
    protected $documentService;
    protected $notificationService;

    public function __construct() {
        $this->requirementService = new RequirementService();
        $this->documentService = new ApplicantDocumentService();
        $this->notificationService = new NotificationService();
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

        $themeModel = new ThemeModel();
        $theme = $themeModel->getAllSettings();

        // Compute primary_soft (lightened tint) from primary_color
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);

        // Prefer the program confirmed on the application form (step 1) when available
        $program_id = session()->get('selected_program_id') ?? session()->get('program_id');

        $categories = $this->requirementService->getCategories($program_id);
        $maxSteps = count($categories) + 1;

        // Application status for "passed" modal
        $applicant_id = session()->get('applicant_id');
        $db = \Config\Database::connect();
        $application = $db->table('applications')->where('applicant_id', $applicant_id)->get()->getRowArray();
        $application_status = $application['application_status'] ?? '';

        $requiredDocsVerified = $program_id && $applicant_id
            ? $this->documentService->allRequiredVerified($applicant_id, $program_id)
            : false;

        return view('applicant/main', [
            'title'              => 'Requirements',
            'page_content'       => 'applicant/requirements/requirements',
            'initial_step'       => $step,
            'max_steps'          => $maxSteps,
            'categories'         => $categories,
            'theme'              => $theme,
            'application_status' => $application_status,
            'requiredDocsVerified' => $requiredDocsVerified,
        ]);
    }

    private function lightenHex(string $hex, float $amount = 0.88): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = (int) round($r + (255 - $r) * $amount);
        $g = (int) round($g + (255 - $g) * $amount);
        $b = (int) round($b + (255 - $b) * $amount);

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public function getStep($step)
    {
        $step = (int) $step;
        
        $program_id = session()->get('selected_program_id') ?? session()->get('program_id');

        $categories = $this->requirementService->getCategories($program_id);
        $maxSteps = count($categories) + 1;

        if ($step < 1 || $step > $maxSteps) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

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

        // Compute upload counts for the counter shown in the step header
        $totalRequired   = count(array_filter($requirements, fn($r) => $r['is_required']));
        $uploadedRequired = count(array_filter($requirements, fn($r) => $r['is_required'] && isset($formData[$r['program_requirement_id']])));
        $totalUploaded   = count($formData);

        // Check ALL requirements across every step (not just the current one)
        $allReqs          = $this->requirementService->getRequirements($program_id);
        $allUploaded      = $this->documentService->getStepDocuments($applicant_id, $allReqs);
        $allTotalRequired = count(array_filter($allReqs, fn($r) => $r['is_required']));
        $allUploadedReq   = count(array_filter($allReqs, fn($r) => $r['is_required'] && isset($allUploaded[$r['program_requirement_id']])));

        if ($this->request->isAJAX()) {

            return $this->response->setJSON([
                'title'              => 'Requirements',
                'step_num'           => $step,
                'form'               => view("applicant/requirements/steps/step", [
                    'step_num'             => $step,
                    'requirements'         => $requirements,
                    'category_name'        => $category_name,
                    'category_description' => $category_description,
                    'uploaded_required'    => $uploadedRequired,
                    'total_required'       => $totalRequired,
                    'total_uploaded'       => $totalUploaded,
                ]),
                'formData'            => $formData,
                'allRequiredComplete' => $allTotalRequired > 0 && $allUploadedReq >= $allTotalRequired,
            ]);
        }

        return $this->renderLayout($step);
    }

    public function updateDocument($step) {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $applicant_id = session()->get('applicant_id');

        $files = $this->request->getFiles();

        $uploaded = $this->documentService->insertDocuments($applicant_id, $files);

        // 🔥 trigger per document
        foreach ($uploaded as $doc) {
            $this->notificationService->trigger(
                'requirements.uploaded',
                $applicant_id,
                [
                    'requirement_name' => $doc['requirement_name']
                ]
            );
        }

        // 🔥 NEW: check completion
        $program_id = session()->get('selected_program_id') ?? session()->get('program_id');

        if ($this->documentService->allRequiredCompleted($applicant_id, $program_id)) {

            $key = "requirements_completed_{$applicant_id}";

            if (! $this->alreadyQueued($key)) {

                $this->notificationService->trigger(
                    'requirements.completed',
                    $applicant_id,
                    [],
                    $key
                );
            }
        }

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

    public function viewDocument($requirementId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        $applicant_id = session()->get('applicant_id');
        $doc = $this->documentService->getDocument($applicant_id, (int) $requirementId);

        if (!$doc) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . "uploads/applicants/{$applicant_id}/" . basename($doc['document_name']);

        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $doc['document_name'] . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function submitApplication()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $applicant_id = session()->get('applicant_id');
        $db = \Config\Database::connect();

        $db->table('applications')
            ->where('applicant_id', $applicant_id)
            ->update(['application_status' => 'pending']);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function alreadyQueued($key)
    {
        return db_connect()->table('email_queue')
            ->where('notification_key', $key)
            ->get()
            ->getRowArray() !== null;
    }
}