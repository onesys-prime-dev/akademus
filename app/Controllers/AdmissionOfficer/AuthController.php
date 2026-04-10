<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('ao_logged_in')) {
            return redirect()->to('admission-officer/dashboard');
        }

        $data['theme'] = (new ThemeModel())->getAllSettings();
        return view('admission-officer/login', $data);
    }

    public function loginPost()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $db   = \Config\Database::connect();
        $user = $db->table('users')
            ->where('email', $email)
            ->where('role', 'admission_officer')
            ->get()->getRowArray();

        if (!$user || !password_verify($password, $user['password'] ?? '')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid credentials. Please try again.']);
            }
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        session()->set([
            'ao_logged_in' => true,
            'ao_id'        => $user['user_id'],
            'ao_name'      => $user['firstname'] . ' ' . $user['lastname'],
            'ao_email'     => $user['email'],
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('admission-officer/dashboard')]);
        }

        return redirect()->to('admission-officer/dashboard');
    }

    public function logout()
    {
        session()->remove(['ao_logged_in', 'ao_id', 'ao_name', 'ao_email']);
        return redirect()->to('admission-officer/login');
    }
}
