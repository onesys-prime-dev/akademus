<?php

namespace App\Controllers\Admin;

use App\Services\PresetService;
use App\Models\PresetModel;
use App\Models\PresetRequirementCategoryModel;
use App\Models\RequirementCategoryModel;
use App\Models\FormPageModel;
use App\Models\ThemeModel;

class PresetsController extends BaseAdminController
{
    protected $presetService;
    protected $presetModel;
    protected $prcModel;
    protected $reqCatModel;
    protected $formPageModel;
    protected $themeModel;

    public function __construct()
    {
        $this->presetService = new PresetService();
        $this->presetModel   = new PresetModel();
        $this->prcModel      = new PresetRequirementCategoryModel();
        $this->reqCatModel   = new RequirementCategoryModel();
        $this->formPageModel = new FormPageModel();
        $this->themeModel    = new ThemeModel();
    }

    private function theme(): array
    {
        return $this->themeModel->getAllSettings() ?? [];
    }

    public function index()
    {
        return view('admin/presets/index', [
            'presets' => $this->presetModel->getActivePresets(),
            'theme'   => $this->theme(),
            'title'   => 'Applicant Forms',
        ]);
    }

    public function create()
    {
        return view('admin/presets/form', [
            'formPages'            => $this->formPageModel->getActivePages(),
            'requirementCategories' => $this->reqCatModel->getActiveCategories(),
            'theme'                => $this->theme(),
            'action'               => 'create',
            'title'                => 'Create Applicant Form',
        ]);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'is_global'   => 'permit_empty|in_list[0,1]',
            'form_pages'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return view('admin/presets/form', [
                'formPages'             => $this->formPageModel->getActivePages(),
                'requirementCategories' => $this->reqCatModel->getActiveCategories(),
                'theme'                 => $this->theme(),
                'action'                => 'create',
                'title'                 => 'Create Applicant Form',
                'errors'                => $this->validator->getErrors(),
                'oldInput'              => $this->request->getPost(),
            ]);
        }

        $isGlobal    = (bool) $this->request->getPost('is_global');
        $formPageIds = (array) $this->request->getPost('form_pages');

        if ($isGlobal && $this->presetModel->where('is_global', 1)->where('is_active', 1)->first()) {
            session()->setFlashdata('error', 'A global preset already exists. Deactivate it first.');
            return redirect()->back()->withInput();
        }

        $categoryIds = (array) ($this->request->getPost('requirement_categories') ?? []);

        $this->presetService->createPreset(
            $this->request->getPost('name'),
            $this->request->getPost('description'),
            $isGlobal,
            $formPageIds,
            $categoryIds
        );

        session()->setFlashdata('success', 'Preset created successfully!');
        return redirect()->to(base_url('admin/presets'));
    }

    public function edit($presetId)
    {
        $preset = $this->presetModel->getPresetWithSections($presetId);
        if (!$preset) {
            session()->setFlashdata('error', 'Preset not found.');
            return redirect()->to(base_url('admin/presets'));
        }

        $selectedPageIds     = array_map(fn($s) => $s['form_page_id'], $preset['sections']);
        $selectedCategoryIds = $this->prcModel->getCategoryIdsForPreset((int)$presetId);

        return view('admin/presets/form', [
            'preset'                => $preset,
            'formPages'             => $this->formPageModel->getActivePages(),
            'requirementCategories' => $this->reqCatModel->getActiveCategories(),
            'selectedIds'           => $selectedPageIds,
            'selectedCategoryIds'   => $selectedCategoryIds,
            'theme'                 => $this->theme(),
            'action'                => 'edit',
            'title'                 => 'Edit Form: ' . $preset['name'],
        ]);
    }

    public function update($presetId)
    {
        $preset = $this->presetModel->find($presetId);
        if (!$preset) {
            session()->setFlashdata('error', 'Preset not found.');
            return redirect()->to(base_url('admin/presets'));
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'is_global'   => 'permit_empty|in_list[0,1]',
            'form_pages'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return view('admin/presets/form', [
                'preset'                => $preset,
                'formPages'             => $this->formPageModel->getActivePages(),
                'requirementCategories' => $this->reqCatModel->getActiveCategories(),
                'selectedCategoryIds'   => $this->prcModel->getCategoryIdsForPreset((int)$presetId),
                'theme'                 => $this->theme(),
                'action'                => 'edit',
                'title'                 => 'Edit Form: ' . $preset['name'],
                'errors'                => $this->validator->getErrors(),
                'oldInput'              => $this->request->getPost(),
            ]);
        }

        $isGlobal    = (bool) $this->request->getPost('is_global');
        $formPageIds = (array) $this->request->getPost('form_pages');

        if ($isGlobal && !$preset['is_global'] && $this->presetModel->where('is_global', 1)->where('is_active', 1)->first()) {
            session()->setFlashdata('error', 'A global preset already exists. Deactivate it first.');
            return redirect()->back()->withInput();
        }

        $categoryIds = (array) ($this->request->getPost('requirement_categories') ?? []);

        $this->presetService->updatePreset(
            $presetId,
            $this->request->getPost('name'),
            $this->request->getPost('description'),
            $isGlobal,
            $formPageIds,
            $categoryIds
        );

        session()->setFlashdata('success', 'Preset updated successfully!');
        return redirect()->to(base_url('admin/presets'));
    }

    public function deactivate($presetId)
    {
        $this->presetService->deactivatePreset($presetId);
        session()->setFlashdata('success', 'Preset deactivated successfully!');
        return redirect()->to(base_url('admin/presets'));
    }

    public function delete($presetId)
    {
        $this->presetService->deletePreset($presetId);
        session()->setFlashdata('success', 'Preset deleted successfully!');
        return redirect()->to(base_url('admin/presets'));
    }

    public function getPresetJson($presetId)
    {
        $preset = $this->presetModel->getPresetWithSections($presetId);
        if (!$preset) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Preset not found']);
        }
        $preset['requirement_categories'] = $this->prcModel->getCategoriesForPreset((int)$presetId);
        return $this->response->setJSON($preset);
    }

    public function getFormPagesForApplicant($programId)
    {
        $pages      = $this->presetService->getFormPagesForApplicant((int)$programId);
        $categories = $this->presetService->getPresetRequirementCategories((int)$programId);
        return $this->response->setJSON(['success' => true, 'pages' => $pages, 'requirement_categories' => $categories]);
    }
}
