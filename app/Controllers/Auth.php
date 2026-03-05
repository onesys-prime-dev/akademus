<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProgramModel;
use App\Models\ApplicantModel;
use App\Models\ApplicantsViewModel;

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

        $data['title']           = 'Sign In';
        $data['auth_header']     = 'Welcome Back!';
        $data['auth_sub_header'] = 'Sign in to continue your application.';
        $data['auth_field']      = 'auth/sign_in';

        return view('auth/main', $data);
    }

    public function sign_up()
    {
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
                'form'      => view('auth/sign_up'),
                'programs'  => $programs 
            ]);
        }

        $data = [
            'title'           => 'Sign Up',
            'auth_header'     => 'Create Account',
            'auth_sub_header' => 'Start your admission journey today.',
            'auth_field'      => 'auth/sign_up',
            'programs'        => $programs
        ];

        return view('auth/main', $data);
    }

    public function success($applicantNumber = null)
    {
        $applicantNumber = session()->getFlashdata('applicant_number');

        $data['title'] = 'Success';
        $data['auth_header'] = 'Account Created!';
        $data['auth_sub_header'] = 'Your unique Applicant ID has been assigned.';
        $data['applicant_number'] = $applicantNumber;
        $data['auth_field'] = 'auth/success';

        return view('auth/main', $data);
    }

    public function register()
    {
        helper(['form']);

        $rules = [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'mobile'    => 'required|numeric|min_length[10]|max_length[11]',
            'program'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed');
            // return view('auth/sign_up', [
            //     'validation' => $this->validator
            // ]);
        }

        $userModel = new UserModel();
        $applicantModel = new ApplicantModel();
        $programModel = new ProgramModel();

        $programId = $this->request->getPost('program');

        // verification of provided programs (bs compsci, BSIT, etc.)
        $program = $programModel->find($programId);

        if (!$programId) {
            return redirect()->back()->with('error', 'Invalid program selected');
        }

        // auto generate temporary password :D
        $tempPassword = bin2hex(random_bytes(6));
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        $db = \Config\Database::connect();
        $db->transBegin();

        // INSERT USER 1st
        if (!$userModel->insert([
    'firstname' => $this->request->getPost('firstname'),
    'lastname'  => $this->request->getPost('lastname'),
    'email'     => $this->request->getPost('email'),
    'mobile'    => $this->request->getPost('mobile'),
    'role'      => 'applicant'
])) {
    dd($userModel->errors(), $db->error());
}

$userId = $userModel->getInsertID();

        // applicant_num generator
        // e.g. #APP-2026-00001

        // INSERT APPLICANT first (without applicant_number)
if (!$applicantModel->insert([
    'user_id'            => $userId,
    'program_id'         => $programId,
    'applicant_number'   => null,
    'password'           => $hashedPassword,
    'temp_password'      => 1,
    'application_status' => 'pending',
    'application_date'   => date('Y-m-d H:i:s')
])) {
    dd($applicantModel->errors(), $db->error(), $userId, $programId);
}        // {
        //     dd($applicantModel->errors());
        // };

        $applicantId = $applicantModel->getInsertID();

        if (!$applicantId) {
            throw new \Exception('Applicant insert failed');
        }

        // generate applicant number safely using primary key
        $year = date('Y');
        $sequence = str_pad($applicantId, 5, '0', STR_PAD_LEFT);

        $applicantNumber = "APP-$year-$sequence";

        // update record with generated applicant number
        $applicantModel->update($applicantId, [
            'applicant_number' => $applicantNumber
        ]);

        $db->transComplete(); // test

        // $session = session();
        // $session->set('applicant_id', $applicantNumber);

        // function will send the autogenerated pass sa email ni user
        $email = \Config\Services::email();

        $email->setFrom('yourgmail@gmail.com', 'Akademus Portal');
        $email->setTo($this->request->getPost('email'));
        $email->setSubject('Your Akademus Temporary Account');
        $email->setMessage("
        Welcome to Akademus Portal

        Your account has been created successfully.
        
        Portal: " . base_url('sign_in') . "

        Email: " .$this->request->getPost('email') . "
        Temporary Password: $tempPassword 

        Please login
        ");

        // if (!$email->send()) {
        //     echo $email->printDebugger(['headers','subject','body']);
        //     exit;
        // } 
        // $email->send(); 
        if (!$email->send()) {
            log_message('error', $email->printDebugger(['headers']));
        }
        
        return redirect()->to('/success')->with('applicant_number', $applicantNumber);
        //return redirect()->to('/sign_in')->with('success', 'Account created successfully.\n Check your email for temporary login credentials');    
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