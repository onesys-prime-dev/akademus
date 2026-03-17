<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

use App\Models\Applicant\ApplicantsViewModel;
use App\Models\Program\ProgramModel;
use App\Models\ThemeModel;
use App\Services\EmailService;

class AuthController extends BaseController
{
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

        $programs = array_map(function($program) {
            return [
                'id'   => $program['program_id'],
                'name' => preg_replace('/^Bachelor of Science in\s*/', '', $program['program_name'])
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

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(' ', $this->validator->getErrors())
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

            EmailService::sendWelcomeEmail(
                $this->request->getPost('email'),
                $tempPassword
            );

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
                'message' => 'Registration failed: ' . $e->getMessage()
            ]);
        }
    }

    public function login()
    {
        $model = new ApplicantsViewModel();

        $user = $model
            ->where('email', $this->request->getPost('email'))
            ->first();

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
            'logged_in'        => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/sign-in');
    }
}