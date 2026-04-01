<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class BaseAdminController extends Controller
{
    protected $helpers = ['form'];
    protected $pageTitle = '';
    protected $breadcrumbs = [];

    public function __construct()
    {
        // Check admin authentication here
        // if (!auth()->loggedIn() || !auth()->user()->is_admin) {
        //     return redirect()->to('/auth/login');
        // }
    }

    /**
     * Render admin view with sidebar
     */
    protected function renderAdmin($view, $data = [])
    {
        $data['pageTitle'] = $this->pageTitle;
        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['sidebarActive'] = $data['sidebarActive'] ?? '';

        // Make sure content view exists and render it
        if (!view($view, $data)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("View not found: {$view}");
        }

        return view('admin/layout', [
            'content' => view($view, $data),
            'pageTitle' => $this->pageTitle,
            'breadcrumbs' => $this->breadcrumbs,
            'sidebarActive' => $data['sidebarActive'] ?? '',
        ]);
    }

    /**
     * Set page breadcrumbs
     */
    protected function setBreadcrumb($label, $url = null)
    {
        $this->breadcrumbs[] = ['label' => $label, 'url' => $url];
    }
}
