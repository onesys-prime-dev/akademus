<?php

namespace App\Controllers;

use App\Models\User\UserModel;
use App\Models\Program\ProgramModel;
use App\Models\Applicant\ApplicantModel;
use App\Models\Applicant\ApplicantsViewModel;
use App\Models\ThemeModel;
use App\Services\EmailService;

class Auth extends BaseController
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
                'form'      => view('auth/sign-in', ['programs' => $programs]),
                'programs'  => $programs
            ]);
        }

        $data = [
            'title'           => 'Sign Up',
            'auth_header'     => 'Create Account',
            'auth_sub_header' => 'Start your admission journey today.',
            'auth_field'      => 'auth/sign-in',
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
        // Verify Cloudflare Turnstile — DISABLED FOR TESTING
        /*
        $turnstileSecret = 'YOUR_SECRET_KEY_HERE';
        $turnstileToken  = $this->request->getPost('cf-turnstile-response');

        $verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'secret'   => $turnstileSecret,
                    'response' => $turnstileToken,
                    'remoteip' => $this->request->getIPAddress(),
                ])
            ]
        ]));

        $result = json_decode($verify);

        if (!$result || !$result->success) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please complete the CAPTCHA verification.'
            ]);
        }
        */

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

        $userModel      = new UserModel();
        $applicantModel = new ApplicantModel();
        $programModel   = new ProgramModel();

        $programId = $this->request->getPost('program');
        $program   = $programModel->find($programId);

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
            $userModel->insert([
                'firstname' => $this->request->getPost('firstname'),
                'lastname'  => $this->request->getPost('lastname'),
                'email'     => $this->request->getPost('email'),
                'mobile'    => $this->request->getPost('mobile'),
                'role'      => 'applicant'
            ]);

            $userId = $userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('User insert failed');
            }

            if (!$applicantModel->insert([
                'user_id'            => $userId,
                'program_id'         => $programId,
                'applicant_number'   => null,
                'password'           => $hashedPassword,
                'temp_password'      => 1,
                'application_status' => 'pending',
                'application_date'   => date('Y-m-d H:i:s')
            ])) {
                throw new \Exception('Applicant insert failed: ' . json_encode($applicantModel->errors()));
            }

            $applicantId     = $applicantModel->getInsertID();
            $year            = date('Y');
            $sequence        = str_pad($applicantId, 5, '0', STR_PAD_LEFT);
            $applicantNumber = "APP-$year-$sequence";

            $applicantModel->update($applicantId, [
                'applicant_number' => $applicantNumber
            ]);

            $db->transComplete();

            $emailSent = EmailService::sendWelcomeEmail(
                $this->request->getPost('email'),
                $tempPassword
            );

            if (!$emailSent) {
                $email = \Config\Services::email();
                //dd($email->printDebugger(['headers', 'subject', 'body']));
            }

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
            'email'            => $user['email'],
            'role'             => $user['role'],
            'applicant_number' => $user['applicant_number'],
            'applicant_id'     => $user['applicant_id'],
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
