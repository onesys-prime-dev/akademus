<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Exceptions\RedirectException;

class BaseAdminController extends Controller
{
    protected $helpers     = ['form'];
    protected $pageTitle   = '';
    protected $breadcrumbs = [];

    public function __construct()
    {
        if (! session()->get('admin_logged_in')) {
            // The framework turns this into the redirect response (no exit, so it also works under test).
            throw new RedirectException(redirect()->to('admin/login'));
        }
    }

    /**
     * Render admin view with sidebar
     *
     * @param mixed $view
     * @param mixed $data
     */
    protected function renderAdmin($view, $data = [])
    {
        $data['pageTitle']   = $this->pageTitle;
        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['sidebarActive'] ??= '';

        // Make sure content view exists and render it
        if (! view($view, $data)) {
            throw new PageNotFoundException("View not found: {$view}");
        }

        return view('admin/layout', [
            'content'       => view($view, $data),
            'pageTitle'     => $this->pageTitle,
            'breadcrumbs'   => $this->breadcrumbs,
            'sidebarActive' => $data['sidebarActive'] ?? '',
        ]);
    }

    /**
     * Set page breadcrumbs
     *
     * @param mixed      $label
     * @param mixed|null $url
     */
    protected function setBreadcrumb($label, $url = null)
    {
        $this->breadcrumbs[] = ['label' => $label, 'url' => $url];
    }
}
