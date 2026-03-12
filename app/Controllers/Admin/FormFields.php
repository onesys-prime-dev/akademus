<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FormFieldConfigModel;
use App\Models\FormPageModel;
use App\Models\ThemeModel;

class FormFields extends BaseController
{
    public function index()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $themeModel = new ThemeModel();
        $ffModel    = new FormFieldConfigModel();
        $pageModel  = new FormPageModel();

        return view('admin/form_fields', [
            'theme'   => $themeModel->getAllSettings(),
            'title'   => 'Form Field Manager',
            'pages'   => $pageModel->getAllOrdered(),
            'grouped' => $ffModel->getGroupedByPage(),
        ]);
    }

    public function save()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $body   = $this->request->getJSON(true);
        $fields = $body['fields'] ?? [];
        if (empty($fields)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No fields received']);
        }
        (new FormFieldConfigModel())->bulkSave($fields);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function reset()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        (new FormFieldConfigModel())->resetToDefaults();
        return $this->response->setJSON(['status' => 'success']);
    }

    public function pageCreate()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $body  = $this->request->getJSON(true);
        $title = trim($body['title'] ?? '');
        $desc  = trim($body['description'] ?? '');

        if (!$title) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Title is required']);
        }

        $pageModel = new FormPageModel();
        $id = $pageModel->insert([
            'title'       => $title,
            'description' => $desc,
            'is_enabled'  => 1,
            'is_system'   => 0,
            'sort_order'  => $pageModel->nextSortOrder(),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'page'   => $pageModel->find($id),
        ]);
    }

    public function pageToggle(int $id)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $result = (new FormPageModel())->toggle($id);
        return $this->response->setJSON($result);
    }

    public function pageRename(int $id)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $body  = $this->request->getJSON(true);
        $title = trim($body['title'] ?? '');
        $desc  = trim($body['description'] ?? '');

        if (!$title) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Title is required']);
        }

        $pageModel = new FormPageModel();
        $page = $pageModel->find($id);
        if (!$page) return $this->response->setJSON(['status' => 'error', 'message' => 'Page not found']);

        $pageModel->update($id, ['title' => $title, 'description' => $desc]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function pageReorder()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $body  = $this->request->getJSON(true);
        $order = $body['order'] ?? [];
        if (empty($order)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No order received']);
        }
        (new FormPageModel())->reorder($order);
        return $this->response->setJSON(['status' => 'success']);
    }

    // ── PAGE: DELETE ──────────────────────────────────────────
    public function deletePage($id)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $result = (new FormPageModel())->deletePage($id);
        return $this->response->setJSON($result);
    }

    // ── PAGE: GET FIELDS HTML (AJAX) ──────────────────────────
   public function pageData()
{
    if (!session()->get('admin_logged_in')) {
        return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
    }

    $pageId = (int)$this->request->getGet('page_id');
    
    // Step 1: does the page exist?
    $page = (new FormPageModel())->find($pageId);
    if (!$page) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Page not found', 'page_id' => $pageId]);
    }

    // Step 2: does getGroupedByPage() work?
    $grouped  = (new FormFieldConfigModel())->getGroupedByPage();
    $pGrouped = $grouped[$pageId] ?? [];

    // Step 3: does the view exist?
    if (!file_exists(APPPATH . 'Views/admin/form_fields_panel.php')) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'View file missing']);
    }

    $html = view('admin/form_fields_panel', [
        'page'    => $page,
        'grouped' => $pGrouped,
    ]);

    return $this->response->setJSON(['status' => 'success', 'html' => $html]);
}
}