<?php

namespace App\Controllers;

use App\Libraries\LoginThrottle;
use App\Models\ThemeModel;
use App\Models\ThemePresetModel;
use App\Models\UserModel;

class ThemeController extends BaseController
{
    protected ThemeModel $themeModel;
    protected ThemePresetModel $themePresetModel;

    /**
     * The color fields a preset (built-in or custom) carries — also the
     * fields accepted by the custom-preset save endpoint.
     */
    public const PRESET_COLOR_FIELDS = [
        'primary_color', 'primary_dark', 'primary_light',
        'accent_color', 'accent_text',
        'sidebar_color', 'sidebar_active_color',
        'banner_bg_color', 'banner_box_color',
        'button_primary_bg', 'button_primary_text',
        'button_accent_bg', 'button_accent_text',
        'header_text_color', 'progress_active_color',
    ];

    public function __construct()
    {
        $this->themeModel       = new ThemeModel();
        $this->themePresetModel = new ThemePresetModel();
    }

    // ── Admin Login Page ──────────────────────────────────────
    public function adminLogin()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to('admin/theme');
        }
        $data['theme'] = $this->themeModel->getAllSettings();
        return view('admin/login', $data);
    }

    public function adminLoginPost()
    {
        if (LoginThrottle::tooManyAttempts('admin')) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(429)->setJSON([
                    'status'  => 'error',
                    'message' => LoginThrottle::retryMessage(),
                ]);
            }
            return redirect()->back()->with('error', LoginThrottle::retryMessage());
        }

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
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid credentials. Please try again.']);
            }
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        if (password_needs_rehash($user['admin_password'], PASSWORD_DEFAULT)) {
            $db->table('admin_credentials')
                ->where('user_id', $user['user_id'])
                ->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $user['user_id'],
            'admin_name'      => $user['firstname'] . ' ' . $user['lastname'],
            'admin_email'     => $user['email'],
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('admin/theme')]);
        }

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

        $data['theme']         = $this->themeModel->getAllSettings();
        $data['title']         = 'Theme Customizer';
        $data['saved']         = session()->getFlashdata('saved');
        $data['customPresets'] = $this->themePresetModel->getAll();

        return view('admin/theme', $data);
    }

    public function save()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $allowed = array_merge(self::PRESET_COLOR_FIELDS, [
            'font_family', 'border_radius',
            'app_name', 'logo_text', 'logo_url',
            'school_year', 'admission_status', 'preset',
        ]);

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
            $valid = $this->validate([
                'logo_file' => 'is_image[logo_file]|mime_in[logo_file,image/jpeg,image/png,image/gif,image/webp]|max_size[logo_file,2048]',
            ]);
            if (!$valid) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Logo must be a JPEG, PNG, GIF, or WebP image under 2 MB.'])->setStatusCode(422);
                }
                return redirect()->back()->with('error', 'Logo must be a JPEG, PNG, GIF, or WebP image under 2 MB.');
            }
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'assets/images/logos', $newName);
            $data['logo_url'] = 'assets/images/logos/' . $newName;
        }

        $this->themeModel->saveSettings($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Theme saved!']);
        }

        return redirect()->to('admin/theme')->with('saved', '1');
    }

    // ── Theme Preview (iframe) ────────────────────────────────
    public function preview()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $page    = $this->request->getGet('page') ?? 'dashboard';
        $allowed = [
            'dashboard', 'appform', 'documents', 'track', 'exam',
            'admission-fee', 'reservation-fee', 'faq', 'followup', 'tuition-calc',
            'signin', 'signup', 'success',
        ];
        if (!in_array($page, $allowed, true)) {
            $page = 'dashboard';
        }

        $built = (new \App\Services\ThemePreviewDataService())->build($page);

        // A few real partials (sidebar, applicant-form step-1) read applicant
        // identity straight from session() rather than $data. Swap in the fake
        // persona for just this render, then restore whatever was there before —
        // net effect is zero persistence beyond this single response.
        $sessionKeys = array_keys(\App\Services\ThemePreviewDataService::SESSION_PERSONA);
        $original    = [];
        foreach ($sessionKeys as $key) {
            $original[$key] = session()->get($key);
        }
        session()->set(\App\Services\ThemePreviewDataService::SESSION_PERSONA);

        $data = $built['data'];
        $data['isPreview'] = true;

        $html = view($built['view'], $data);

        foreach ($sessionKeys as $key) {
            if ($original[$key] === null) {
                session()->remove($key);
            } else {
                session()->set($key, $original[$key]);
            }
        }

        return $this->response->setBody($html);
    }

    /**
     * API endpoint — returns current theme as JSON (used by portal views)
     */
    public function getThemeJson()
    {
        $theme = $this->themeModel->getAllSettings();
        return $this->response->setJSON($theme);
    }

    public function uploadLogo()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $file = $this->request->getFile('logo_file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid file'
            ]);
        }

        $valid = $this->validate([
            'logo_file' => 'is_image[logo_file]|mime_in[logo_file,image/jpeg,image/png,image/gif,image/webp]|max_size[logo_file,2048]',
        ]);
        if (!$valid) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Logo must be a JPEG, PNG, GIF, or WebP image under 2 MB.',
            ])->setStatusCode(422);
        }

        $newName = $file->getRandomName();

        $file->move(
            FCPATH . 'assets/images/logos',
            $newName
        );

        return $this->response->setJSON([
            'status' => 'success',
            'logo_path' => 'assets/images/logos/' . $newName,
            'logo_url'  => base_url('assets/images/logos/' . $newName)
        ]);
    }

    // ── Custom Presets ────────────────────────────────────────
    public function saveCustomPreset()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $rules = ['name' => 'required|max_length[100]'];
        foreach (self::PRESET_COLOR_FIELDS as $field) {
            $rules[$field] = 'required|regex_match[/^#[0-9a-fA-F]{3,8}$/]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'A preset name and 15 valid hex colors are required.',
            ])->setStatusCode(422);
        }

        $data = ['name' => $this->request->getPost('name')];
        foreach (self::PRESET_COLOR_FIELDS as $field) {
            $data[$field] = $this->request->getPost($field);
        }

        $id = $this->themePresetModel->createPreset($data);
        $data['id'] = $id;

        return $this->response->setJSON(['status' => 'success', 'preset' => $data]);
    }

    public function deleteCustomPreset($id)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $this->themePresetModel->delete((int) $id);

        return $this->response->setJSON(['status' => 'success']);
    }
}