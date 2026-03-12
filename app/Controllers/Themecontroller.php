<?php

namespace App\Controllers;

use App\Models\ThemeModel;
use App\Models\UserModel;

class ThemeController extends BaseController
{
    protected ThemeModel $themeModel;

    public function __construct()
    {
        $this->themeModel = new ThemeModel();
    }

    // ── Admin Login Page ──────────────────────────────────────
    public function adminLogin()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to('admin/theme');
        }
        return view('admin/login');
    }

    public function adminLoginPost()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $db   = \Config\Database::connect();
        $user = $db->table('users u')
            ->select('u.*, ac.password as admin_password')
            ->join('admin_credentials ac', 'ac.user_id = u.user_id')
            ->where('u.email', $email)
            ->where('u.role', 'admin')
            ->get()->getRowArray();

        if (!$user || !password_verify($password, $user['admin_password'])) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $user['user_id'],
            'admin_name'      => $user['firstname'] . ' ' . $user['lastname'],
            'admin_email'     => $user['email'],
        ]);

        return redirect()->to('admin/theme');
    }

    public function adminLogout()
    {
        session()->remove(['admin_logged_in', 'admin_id', 'admin_name', 'admin_email']);
        return redirect()->to('admin/login');
    }

    // ── Theme Editor ─────────────────────────────────────────
    public function index()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $data['theme']    = $this->themeModel->getAllSettings();
        $data['title']    = 'Theme Customizer';
        $data['saved']    = session()->getFlashdata('saved');

        return view('admin/theme', $data);
    }

    public function save()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $allowed = [
            'primary_color', 'primary_dark', 'primary_light',
            'accent_color', 'accent_text', 'sidebar_color',
            'font_family', 'border_radius',
            'app_name', 'logo_text', 'logo_url',
            'school_year', 'admission_status', 'preset',
        ];

        $data = [];
        foreach ($allowed as $key) {
            $val = $this->request->getPost($key);
            if ($val !== null) {
                $data[$key] = $val;
            }
        }

        // Handle logo upload
        $logo = $this->request->getFile('logo_file');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'assets/images/logos', $newName);
            $data['logo_url'] = 'assets/images/logos/' . $newName;
        }

        $this->themeModel->saveSettings($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Theme saved!']);
        }

        return redirect()->to('admin/theme')->with('saved', true);
    }

    /**
     * API endpoint — returns current theme as JSON (used by portal views)
     */
    public function getThemeJson()
    {
        $theme = $this->themeModel->getAllSettings();
        return $this->response->setJSON($theme);
    }
}