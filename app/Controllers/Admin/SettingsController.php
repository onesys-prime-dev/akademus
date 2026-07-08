<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ThemeModel;

class SettingsController extends BaseController
{
    private function requireAdmin()
    {
        if (! session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->requireAdmin()) return $r;

        $themeModel       = new ThemeModel();
        $examEnabled      = $themeModel->getSetting('exam_enabled', '1') === '1';
        $fastTrackEnabled = $themeModel->getSetting('fast_track_enabled', '1') === '1';

        return view('admin/settings', [
            'theme'              => $themeModel->getAllSettings(),
            'exam_enabled'       => $examEnabled,
            'fast_track_enabled' => $fastTrackEnabled,
        ]);
    }

    public function save()
    {
        if ($r = $this->requireAdmin()) return $r;

        $themeModel  = new ThemeModel();
        $examEnabled = $this->request->getPost('exam_enabled') ? '1' : '0';
        $fastTrackEnabled = $this->request->getPost('fast_track_enabled') ? '1' : '0';

        $themeModel->saveSetting('exam_enabled', $examEnabled);
        $themeModel->saveSetting('fast_track_enabled', $fastTrackEnabled);

        return redirect()->to(base_url('admin/settings'))->with('success', 'Settings saved.');
    }
}
