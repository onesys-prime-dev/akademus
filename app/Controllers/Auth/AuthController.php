<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

use App\Models\Auth\RememberTokenModel;
use App\Models\Program\ProgramModel;
use App\Models\ThemeModel;
use App\Services\NotificationService;

class AuthController extends BaseController
{
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function sign_in()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title'     => 'Sign In',
                'header'    => 'Welcome Back!',
                'subheader' => 'Sign in to continue your application.',
                'form'      => view('auth/sign-in')
            ]);
        }

        $themeModel = new ThemeModel();

        $data['theme']           = $themeModel->getAllSettings();
        $data['title']           = 'Sign In';
        $data['auth_header']     = 'Welcome Back!';
        $data['auth_sub_header'] = 'Sign in to continue your application.';
        $data['auth_field']      = 'auth/sign-in';

        return view('auth/main', $data);
    }

    public function sign_up()
    {
        $themeModel   = new ThemeModel();
        $programModel = new ProgramModel();
        $programs     = $programModel->findAll();

        $programs = array_map(function ($program) {
            return [
                'id'   => $program['program_id'],
                'name' => $program['program_name'],
            ];
        }, $programs);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title'     => 'Sign Up',
                'header'    => 'Create Account',
                'subheader' => 'Start your admission journey today.',
                'form'      => view('auth/sign-up', ['programs' => $programs]),
                'programs'  => $programs
            ]);
        }

        $data = [
            'title'           => 'Sign Up',
            'auth_header'     => 'Create Account',
            'auth_sub_header' => 'Start your admission journey today.',
            'auth_field'      => 'auth/sign-up',
            'programs'        => $programs,
            'theme'           => $themeModel->getAllSettings()
        ];

        return view('auth/main', $data);
    }

    public function success()
    {
        $themeModel      = new ThemeModel();
        $applicantNumber = session()->getFlashdata('applicant_number');

        if (!$applicantNumber) {
            return redirect()->to('/sign-in');
        }

        $data['title']            = 'Success';
        $data['auth_header']      = 'Account Created!';
        $data['auth_sub_header']  = 'Your unique Applicant ID has been assigned.';
        $data['applicant_number'] = $applicantNumber;
        $data['auth_field']       = 'auth/success';
        $data['theme']            = $themeModel->getAllSettings();
        $data['programs']         = [];

        return view('auth/main', $data);
    }

    public function register()
    {
        $rules = [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'mobile'    => 'required|min_length[10]|max_length[13]',
            'program'   => 'required',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'This email is already registered. Please use a different email or sign in instead.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => array_values($errors)[0],
            ]);
        }

        $programModel = new ProgramModel();
        $programId    = $this->request->getPost('program');
        $program      = $programModel->find($programId);

        if (!$program) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid program selected'
            ]);
        }

        $tempPassword   = bin2hex(random_bytes(6));
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $db->table('users')->insert([
                'firstname' => $this->request->getPost('firstname'),
                'lastname'  => $this->request->getPost('lastname'),
                'email'     => $this->request->getPost('email'),
                'mobile'    => $this->request->getPost('mobile'),
                'role'      => 'applicant'
            ]);

            $userId = $db->insertID();

            if (!$userId) {
                throw new \Exception('User insert failed');
            }

            $db->table('applicants')->insert([
                'user_id'            => $userId,
                'program_id'         => $programId,
                'applicant_number'   => null,
                'password'           => $hashedPassword,
                'temp_password'      => 1,
                'application_status' => 'pending',
                'application_date'   => date('Y-m-d H:i:s')
            ]);

            $applicantId     = $db->insertID();
            $year            = date('Y');
            $sequence        = str_pad($applicantId, 5, '0', STR_PAD_LEFT);
            $applicantNumber = "APP-$year-$sequence";

            $db->table('applicants')->where('applicant_id', $applicantId)->update([
                'applicant_number' => $applicantNumber
            ]);

            $db->transComplete();

            $this->notificationService->trigger('auth.welcome', $applicantId, [
                'temp_password' => $tempPassword
            ]);

            session()->setFlashdata('applicant_number', $applicantNumber);
            return $this->response->setJSON([
                'status'   => 'success',
                'redirect' => base_url('success')
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Registration failed. Please try again later.'
            ]);
        }
    }

    public function login()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('applicants a');
        $builder->select('
        a.applicant_id,
        u.user_id,
        a.program_id,
        p.program_name,
        u.firstname,
        u.lastname,
        u.email,
        u.mobile,
        u.role,
        a.applicant_number,
        a.password,
        a.temp_password,
        a.application_status,
        a.application_date,
        a.created_at
    ');

        $builder->join('users u', 'a.user_id = u.user_id');
        $builder->join('programs p', 'a.program_id = p.program_id');

        $builder->where('u.email', $this->request->getPost('email'));

        $user = $builder->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'User not found'
            ]);
        }

        if (!password_verify($this->request->getPost('password'), $user['password'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid email or password'
            ]);
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $db->table('applicants')
                ->where('applicant_id', $user['applicant_id'])
                ->update(['password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)]);
        }

        session()->set([
            'user_id'          => $user['user_id'],
            'firstname'        => $user['firstname'],
            'lastname'         => $user['lastname'],
            'email'            => $user['email'],
            'role'             => $user['role'],
            'applicant_number' => $user['applicant_number'],
            'applicant_id'     => $user['applicant_id'],
            'program_id'       => $user['program_id'],
            'program_name'     => $user['program_name'],
            'temp_password'    => (bool) $user['temp_password'],
            'logged_in'        => true
        ]);

        $remember = $this->request->getPost('remember_me');

        if ($remember) {

            $token = bin2hex(random_bytes(32));

            $rememberTokenModel = new RememberTokenModel();

            $rememberTokenModel->insert([
                'user_id'    => $user['user_id'],
                'token'      => hash('sha256', $token),
                'expires_at' => date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)),
            ]);

            setcookie(
                'remember_token',
                $token,
                time() + (30 * 24 * 60 * 60),
                '/',
                '',
                ENVIRONMENT === 'production',
                true
            );
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'redirect' => base_url('dashboard'),
            ]);
        }
    }

    public function forgotPassword()
    {
        $email = trim($this->request->getPost('email') ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter a valid email address.']);
        }

        $db   = \Config\Database::connect();
        $user = $db->table('users')->where('email', $email)->where('role', 'applicant')->get()->getRowArray();

        // Always return success so we don't reveal if an email is registered
        if ($user) {
            $token = bin2hex(random_bytes(32));

            $db->table('password_resets')->insert([
                'email'      => $email,
                'token'      => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'used'       => 0,
            ]);

            $resetUrl = base_url('reset-password/' . $token);

            try {
                $this->notificationService->trigger('auth.password_reset', $user['user_id'], [
                    'email' => $email,
                    'reset_url' => $resetUrl
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Password reset notification error: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'If that email is registered, a reset link has been sent.',
        ]);
    }

    public function showResetForm(string $token)
    {
        $themeModel = new ThemeModel();
        $db         = \Config\Database::connect();

        $reset = $db->table('password_resets')
            ->where('token', $token)
            ->where('used', 0)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->get()->getRowArray();

        return view('auth/reset-password', [
            'theme'   => $themeModel->getAllSettings(),
            'token'   => $token,
            'valid'   => !empty($reset),
        ]);
    }

    public function processReset()
    {
        $token           = $this->request->getPost('token') ?? '';
        $newPassword     = $this->request->getPost('new_password') ?? '';
        $confirmPassword = $this->request->getPost('confirm_password') ?? '';

        $db = \Config\Database::connect();

        $reset = $db->table('password_resets')
            ->where('token', $token)
            ->where('used', 0)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->get()->getRowArray();

        if (!$reset) {
            return $this->response->setJSON(['success' => false, 'message' => 'This link is invalid or has expired.']);
        }

        if (strlen($newPassword) < 8) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['success' => false, 'message' => 'Passwords do not match.']);
        }

        // Look up the applicant by email
        $user = $db->table('users')->where('email', $reset['email'])->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found.']);
        }

        $applicant = $db->table('applicants')->where('user_id', $user['user_id'])->get()->getRowArray();

        if (!$applicant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found.']);
        }

        // Update password
        $db->table('applicants')
            ->where('applicant_id', $applicant['applicant_id'])
            ->update([
                'password'      => password_hash($newPassword, PASSWORD_DEFAULT),
                'temp_password' => 0,
            ]);

        // Invalidate token
        $db->table('password_resets')->where('token', $token)->update(['used' => 1]);

        return $this->response->setJSON(['success' => true]);
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $newPassword     = $this->request->getPost('new_password') ?? '';
        $confirmPassword = $this->request->getPost('confirm_password') ?? '';

        if (strlen($newPassword) < 8) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['success' => false, 'message' => 'Passwords do not match.']);
        }

        $db = \Config\Database::connect();
        $db->table('applicants')
            ->where('applicant_id', session()->get('applicant_id'))
            ->update([
                'password'      => password_hash($newPassword, PASSWORD_DEFAULT),
                'temp_password' => 0,
            ]);

        session()->set('temp_password', false);

        return $this->response->setJSON(['success' => true]);
    }

    public function logout()
    {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];

            $rememberModel = new \App\Models\Auth\RememberTokenModel();
            $rememberModel->where('token', hash('sha256', $token))->delete();

            setcookie('remember_token', '', time() - 3600, '/');
        }

        session()->destroy();

        return redirect()->to('/sign-in');
    }
}
