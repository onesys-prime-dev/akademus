<?php

namespace App\Controllers\Admin;

use App\Models\RequirementCategoryModel;
use App\Models\RequirementDocumentModel;
use App\Models\ThemeModel;
use App\Services\ProgramRequirementSyncService;

class RequirementCategoriesController extends BaseAdminController
{
    protected $categoryModel;
    protected $docModel;
    protected $themeModel;
    protected $syncService;

    public function __construct()
    {
        $this->categoryModel = new RequirementCategoryModel();
        $this->docModel      = new RequirementDocumentModel();
        $this->themeModel    = new ThemeModel();
        $this->syncService   = new ProgramRequirementSyncService();
    }

    private function theme(): array
    {
        return $this->themeModel->getAllSettings() ?? [];
    }

    public function index()
    {
        return view('admin/requirement_categories/index', [
            'categories' => $this->categoryModel->getCategoriesWithDocumentCount(),
            'theme'      => $this->theme(),
            'title'      => 'Requirement Categories',
        ]);
    }

    public function create()
    {
        return view('admin/requirement_categories/form', [
            'theme'     => $this->theme(),
            'action'    => 'create',
            'title'     => 'Create Requirement Category',
            'category'  => [],
            'documents' => [],
        ]);
    }

    public function store()
    {
        $rules = [
            'category_name' => 'required|min_length[2]|max_length[150]',
            'description'   => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return view('admin/requirement_categories/form', [
                'theme'    => $this->theme(),
                'action'   => 'create',
                'title'    => 'Create Requirement Category',
                'category' => [],
                'documents' => [],
                'errors'   => $this->validator->getErrors(),
                'oldInput' => $this->request->getPost(),
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Get next display_order
        $maxOrder = $db->table('requirement_categories')->selectMax('display_order')->get()->getRowArray();
        $displayOrder = ($maxOrder['display_order'] ?? 0) + 10;

        $this->categoryModel->insert([
            'category_name' => $this->request->getPost('category_name'),
            'description'   => $this->request->getPost('description'),
            'display_order' => $displayOrder,
            'is_active'     => 1,
        ]);
        $categoryId = $this->categoryModel->getInsertID();

        $this->saveDocuments($categoryId, $this->request->getPost('documents') ?? []);

        $db->transComplete();

        session()->setFlashdata('success', 'Requirement category created successfully!');
        return redirect()->to(base_url('admin/requirement-categories'));
    }

    public function edit($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            session()->setFlashdata('error', 'Category not found.');
            return redirect()->to(base_url('admin/requirement-categories'));
        }

        return view('admin/requirement_categories/form', [
            'theme'     => $this->theme(),
            'action'    => 'edit',
            'title'     => 'Edit Category: ' . $category['category_name'],
            'category'  => $category,
            'documents' => $this->docModel->getDocumentsForCategory((int)$categoryId),
        ]);
    }

    public function update($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            session()->setFlashdata('error', 'Category not found.');
            return redirect()->to(base_url('admin/requirement-categories'));
        }

        $rules = [
            'category_name' => 'required|min_length[2]|max_length[150]',
            'description'   => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return view('admin/requirement_categories/form', [
                'theme'     => $this->theme(),
                'action'    => 'edit',
                'title'     => 'Edit Category: ' . $category['category_name'],
                'category'  => $category,
                'documents' => $this->docModel->getDocumentsForCategory((int)$categoryId),
                'errors'    => $this->validator->getErrors(),
                'oldInput'  => $this->request->getPost(),
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->categoryModel->update($categoryId, [
            'category_name' => $this->request->getPost('category_name'),
            'description'   => $this->request->getPost('description'),
        ]);

        // Replace documents: delete old, insert new
        $this->saveDocuments($categoryId, $this->request->getPost('documents') ?? []);

        $db->transComplete();

        session()->setFlashdata('success', 'Category updated successfully!');
        return redirect()->to(base_url('admin/requirement-categories'));
    }

    public function delete($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            session()->setFlashdata('error', 'Category not found.');
            return redirect()->to(base_url('admin/requirement-categories'));
        }

        $db = \Config\Database::connect();
        $db->table('requirements')->where('category_id', (int)$categoryId)->delete();
        $this->categoryModel->delete($categoryId);

        session()->setFlashdata('success', 'Category deleted successfully!');
        return redirect()->to(base_url('admin/requirement-categories'));
    }

    private function saveDocuments(int $categoryId, array $documents): void
    {
        log_message('debug', 'saveDocuments payload: ' . json_encode($documents));
        
        $seenIds = [];

        foreach ($documents as $order => $doc) {
            if (empty($doc['requirement_name'])) {
                continue;
            }

            $data = [
                'category_id'      => $categoryId,
                'requirement_name' => trim($doc['requirement_name']),
                'description'      => $doc['description'] ?? null,
                'is_required'      => !empty($doc['is_required']) ? 1 : 0,
                'display_order'    => (int) $order,
                'is_active'        => 1,
            ];

            $requirementId = $doc['requirement_id'] ?? null;

            if ($requirementId) {
                $requirementId = (int) $requirementId;

                $this->docModel->update($requirementId, $data);
                $seenIds[] = $requirementId;

                // sync is_required -> is_mandatory
                $this->syncService->syncRequirement($requirementId, $categoryId);
            } else {
                $this->docModel->insert($data);
                $newId = $this->docModel->getInsertID();

                $seenIds[] = $newId;

                $this->syncService->syncRequirement($newId, $categoryId);
            }
        }

        $existing = $this->docModel
            ->where('category_id', $categoryId)
            ->findAll();

        foreach ($existing as $row) {
            if (! in_array($row['requirement_id'], $seenIds)) {
                $this->docModel->update($row['requirement_id'], [
                    'is_active' => 0,
                ]);

                $this->syncService->deactivateRequirement(
                    (int) $row['requirement_id']
                );
            }
        }
    }
}
