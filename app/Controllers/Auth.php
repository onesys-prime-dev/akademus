<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProgramModel;
use App\Models\ApplicantModel;
use App\Models\ApplicantsViewModel;
use App\Services\EmailService;
use App\Models\ThemeModel;

class Auth extends BaseController
{
    public function sign_in()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'title'     => 'Sign In',
                'header'    => 'Welcome Back!',
                'subheader' => 'Sign in to continue your application.',
                'form'      => view('auth/sign_in')
            ]);
        }

        $themeModel = new ThemeModel();
$data['theme'] = $themeModel->getAllSettings();

$data['title']           = 'Sign In';
$data['auth_header']     = 'Welcome Back!';
$data['auth_sub_header'] = 'Sign in to continue your application.';
$data['auth_field']      = 'auth/sign_in';

return view('auth/main', $data);
    }

    public function sign_up()
    {
        $themeModel   = new ThemeModel();
        $programModel = new ProgramModel();
        $programs = $programModel->findAll();

        $programs = array_map(function($program) {
            return [
                'id' => $program['program_id'],
                'name' => preg_replace(
                    '/^Bachelor of Science in\s*/',
                    '',
                    $program['program_name']
                )
            ];
        }, $programs);

        // $programId = $this->request->getPost('program');

        // $program = (new ProgramModel())
        //     ->where('program_id', $programId)
        //     ->first();

        // if (!$program) {
        //     return redirect()->back()->with('error', 'Invalid program selected');
        // }

        if ($this->request->isAJAX()) {
    return $this->response->setJSON([
        'title'     => 'Sign Up',
        'header'    => 'Create Account',
        'subheader' => 'Start your admission journey today.',
        'form'      => view('auth/sign_up', ['programs' => $programs]),
        'programs'  => $programs 
    ]);
}

        $data = [
    'title'           => 'Sign Up',
    'auth_header'     => 'Create Account',
    'auth_sub_header' => 'Start your admission journey today.',
    'auth_field'      => 'auth/sign_up',
    'programs'        => $programs,
    'theme'           => $themeModel->getAllSettings()
];
        return view('auth/main', $data);
    }

    public function success()
{
    $themeModel = new ThemeModel();
    $applicantNumber = session()->getFlashdata('applicant_number');

    if (!$applicantNumber) {
        return redirect()->to('/sign_in');
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
    //dd($this->request->getPost());
    $rules = [
        'firstname' => 'required',
        'lastname'  => 'required',
        'email'     => 'required|valid_email|is_unique[users.email]',
        'mobile' => 'required|min_length[10]|max_length[13]',
        'program'   => 'required',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', 'Validation failed');
    }

    $userModel      = new UserModel();
    $applicantModel = new ApplicantModel();
    $programModel   = new ProgramModel();

    $programId = $this->request->getPost('program');
    $program   = $programModel->find($programId);

    if (!$program) {
        return redirect()->back()->with('error', 'Invalid program selected');
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

        $applicantId = $applicantModel->getInsertID();
        $year        = date('Y');
        $sequence    = str_pad($applicantId, 5, '0', STR_PAD_LEFT);
        $applicantNumber = "APP-$year-$sequence";

        $applicantModel->update($applicantId, [
            'applicant_number' => $applicantNumber
        ]);

        $db->transComplete();

        // Send email and dump error if it fails
        $emailSent = EmailService::sendWelcomeEmail(
            $this->request->getPost('email'),
            $tempPassword
        );

        if (!$emailSent) {
            // Temporary: show why email failed
            $email = \Config\Services::email();
            //dd($email->printDebugger(['headers', 'subject', 'body']));
        }

        return redirect()->to('success')->with('applicant_number', $applicantNumber);

    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Registration failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Registration failed: ' . $e->getMessage());
    }
}

    public function login()
    {
        //$model = new UserModel();
        $model = new ApplicantsViewModel();

        $user = $model
            ->where('email', $this->request->getPost('email'))
            ->first();
        /*
        $user = $model
            ->select('users.*, applicant.password')
            ->join('applicant', 'applicant.user_id = users.user_id')
            ->join('programs', 'programs.program_id = applicant.program_id')
            ->where('users.email', $this->request->getPost('email'))
            ->first();
        */

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
            'user_id'         => $user['user_id'],
            'firstname'       => $user['firstname'],
            'email'           => $user['email'],
            'role'            => $user['role'],
            'applicant_number'=> $user['applicant_number'], 
            'applicant_id'    => $user['applicant_id'],
            'logged_in'       => true
        ]);
        
        return redirect()->to('/dashboard');
        // return $this->response->setJSON([
        //     'status'   => 'success',
        //     'redirect' => base_url('dashboard')
        // ]);

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/sign_in');
    }
}