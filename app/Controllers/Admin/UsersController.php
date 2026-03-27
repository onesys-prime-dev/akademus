<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ThemeModel;

class UsersController extends BaseController
{
    private function requireAdmin()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->requireAdmin()) return $r;

        $db    = \Config\Database::connect();
        $theme = (new ThemeModel())->getAllSettings();

        $users = $db->table('users')
            ->whereIn('role', ['admin', 'admission_officer'])
            ->orderBy('role', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->get()->getResultArray();

        return view('admin/users/index', [
            'theme' => $theme,
            'users' => $users,
        ]);
    }

    public function store()
    {
        if ($r = $this->requireAdmin()) return $r;

        $firstname = trim($this->request->getPost('firstname') ?? '');
        $lastname  = trim($this->request->getPost('lastname')  ?? '');
        $email     = trim($this->request->getPost('email')     ?? '');
        $password  = $this->request->getPost('password')       ?? '';
        $role      = $this->request->getPost('role')           ?? '';

        if (!in_array($role, ['admin', 'admission_officer'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid role.']);
        }
        if (!$firstname || !$lastname || !$email || !$password) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid email address.']);
        }
        if (strlen($password) < 8) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
        }

        $db = \Config\Database::connect();

        if ($db->table('users')->where('email', $email)->countAllResults() > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email is already in use.']);
        }

        $db->transBegin();
        try {
            $db->table('users')->insert([
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
                'role'      => $role,
            ]);
            $userId = $db->insertID();

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            if ($role === 'admin') {
                $db->table('admin_credentials')->insert([
                    'user_id'  => $userId,
                    'password' => $hashed,
                ]);
            } else {
                $db->table('users')->where('user_id', $userId)->update([
                    'password' => $hashed,
                ]);
            }

            $db->transComplete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'User created successfully.']);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'UsersController::store — ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create user.']);
        }
    }

    public function update($id)
    {
        if ($r = $this->requireAdmin()) return $r;

        $firstname = trim($this->request->getPost('firstname') ?? '');
        $lastname  = trim($this->request->getPost('lastname')  ?? '');
        $email     = trim($this->request->getPost('email')     ?? '');
        $role      = $this->request->getPost('role')           ?? '';

        if (!in_array($role, ['admin', 'admission_officer'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid role.']);
        }
        if (!$firstname || !$lastname || !$email) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid email address.']);
        }

        $db = \Config\Database::connect();

        $existing = $db->table('users')->where('email', $email)->where('user_id !=', $id)->countAllResults();
        if ($existing > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email is already in use.']);
        }

        $user = $db->table('users')->where('user_id', $id)->whereIn('role', ['admin', 'admission_officer'])->get()->getRowArray();
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.']);
        }

        $db->transBegin();
        try {
            $oldRole = $user['role'];

            $db->table('users')->where('user_id', $id)->update([
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
                'role'      => $role,
            ]);

            // If role changed between admin ↔ admission_officer, migrate credentials
            if ($oldRole !== $role) {
                if ($role === 'admin') {
                    // Move password from users.password → admin_credentials
                    $pw = $db->table('users')->select('password')->where('user_id', $id)->get()->getRowArray();
                    if ($pw && $pw['password']) {
                        $db->table('admin_credentials')->insert(['user_id' => $id, 'password' => $pw['password']]);
                        $db->table('users')->where('user_id', $id)->update(['password' => null]);
                    }
                } else {
                    // Move password from admin_credentials → users.password
                    $ac = $db->table('admin_credentials')->where('user_id', $id)->get()->getRowArray();
                    if ($ac) {
                        $db->table('users')->where('user_id', $id)->update(['password' => $ac['password']]);
                        $db->table('admin_credentials')->where('user_id', $id)->delete();
                    }
                }
            }

            $db->transComplete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'UsersController::update — ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update user.']);
        }
    }

    public function resetPassword($id)
    {
        if ($r = $this->requireAdmin()) return $r;

        $password = $this->request->getPost('password') ?? '';

        if (strlen($password) < 8) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
        }

        $db   = \Config\Database::connect();
        $user = $db->table('users')->where('user_id', $id)->whereIn('role', ['admin', 'admission_officer'])->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.']);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if ($user['role'] === 'admin') {
            $exists = $db->table('admin_credentials')->where('user_id', $id)->countAllResults();
            if ($exists) {
                $db->table('admin_credentials')->where('user_id', $id)->update(['password' => $hashed]);
            } else {
                $db->table('admin_credentials')->insert(['user_id' => $id, 'password' => $hashed]);
            }
        } else {
            $db->table('users')->where('user_id', $id)->update(['password' => $hashed]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Password reset successfully.']);
    }

    public function delete($id)
    {
        if ($r = $this->requireAdmin()) return $r;

        // Prevent deleting self
        if ((int) $id === (int) session()->get('admin_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You cannot delete your own account.']);
        }

        $db   = \Config\Database::connect();
        $user = $db->table('users')->where('user_id', $id)->whereIn('role', ['admin', 'admission_officer'])->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.']);
        }

        $db->transBegin();
        try {
            $db->table('admin_credentials')->where('user_id', $id)->delete();
            $db->table('users')->where('user_id', $id)->delete();
            $db->transComplete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted.']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete user.']);
        }
    }
}
