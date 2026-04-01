<?php

namespace App\Controllers\Admin;

class ProgramFormFieldsController extends BaseAdminController
{
    protected $formFieldService;
    protected $programModel;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->formFieldService = service('FormFieldService');
        $this->programModel = model('ProgramModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * View all base form fields (global/system fields)
     */
    public function baseFields()
    {
        $this->pageTitle = 'Base Form Fields';
        $this->setBreadcrumb('Form Management');
        $this->setBreadcrumb('Base Fields');

        $data = [
            'sidebarActive' => 'forms',
            'fields' => $this->formFieldService->getBaseFormFields(),
            'pages' => $this->formFieldService->getFormPages(),
        ];

        return $this->renderAdmin('admin/forms/base-fields', $data);
    }

    /**
     * Manage program-specific form field overrides
     */
    public function programFields($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $this->pageTitle = "Customize Form for {$program['program_name']}";
        $this->setBreadcrumb('Form Management', '/admin/program-forms/base');
        $this->setBreadcrumb($program['program_name']);

        // Get merged fields (with overrides applied)
        $mergedFields = $this->formFieldService->getMergedFormFields($programId);
        
        // Get base fields
        $baseFields = $this->formFieldService->getBaseFormFields();
        
        // Get existing overrides
        $overrides = $this->formFieldService->getProgramOverrides($programId);
        $overrideMap = array_column($overrides, null, 'field_config_id');

        $data = [
            'sidebarActive' => 'forms',
            'program' => $program,
            'programId' => $programId,
            'mergedFields' => $mergedFields,
            'baseFields' => $baseFields,
            'overrideMap' => $overrideMap,
        ];

        return $this->renderAdmin('admin/forms/program-fields', $data);
    }

    /**
     * Save field override for a program
     */
    public function saveFieldOverride($programId, $fieldConfigId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Program not found']);
        }

        $overrides = [];

        // Only include fields that are actually being overridden
        if ($this->request->getPost('is_required') !== null) {
            $overrides['is_required'] = (bool)$this->request->getPost('is_required');
        }

        if ($this->request->getPost('is_visible') !== null) {
            $overrides['is_visible'] = (bool)$this->request->getPost('is_visible');
        }

        if ($this->request->getPost('sort_order') !== null) {
            $overrides['sort_order'] = (int)$this->request->getPost('sort_order');
        }

        if (empty($overrides)) {
            // Remove override if no customizations
            $this->formFieldService->removeProgramFieldOverride($programId, $fieldConfigId);
        } else {
            // Save override
            $this->formFieldService->saveProgramFieldOverride($programId, $fieldConfigId, $overrides);
        }

        return redirect()->back()->with('success', 'Field override saved');
    }

    /**
     * Remove field override for a program
     */
    public function removeFieldOverride($programId, $fieldConfigId)
    {
        $this->formFieldService->removeProgramFieldOverride($programId, $fieldConfigId);
        return redirect()->back()->with('success', 'Field override removed');
    }

    /**
     * Bulk update field overrides
     */
    public function bulkUpdateOverrides($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Program not found']);
        }

        $overrides = $this->request->getPost('overrides');

        if (!$overrides || !is_array($overrides)) {
            return redirect()->back()->with('error', 'No overrides provided');
        }

        foreach ($overrides as $fieldConfigId => $override) {
            if (!empty($override)) {
                $this->formFieldService->saveProgramFieldOverride($programId, $fieldConfigId, $override);
            } else {
                $this->formFieldService->removeProgramFieldOverride($programId, $fieldConfigId);
            }
        }

        return redirect()->back()->with('success', 'All field overrides updated');
    }
}
