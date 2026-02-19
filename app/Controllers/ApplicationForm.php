<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\UserModel;

class ApplicationForm extends BaseController 
{
    public function index(): string 
    {
        return view('applicationform');       
    }

    public function submit() {
        helper(['form']);

        $rules = [
            'fullname' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[applications.email]',
            'mobile' => 'required|min_length[10]|is_unique[applications.contactnum]',
        ];

        if (!$this->validate($rules)) {
            return view('applicationform', ['validation' => $this->validator]);
        }

        $applicationModel = new ApplicationModel();

        $applicationModel->insert([
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'contactnum'   => $this->request->getPost('contactnum'),
        ]);

        // temporary password generator
        $tempPassword = bin2hex(random_bytes(4));
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        // save users table
        $userModel = new UserModel();

        $userModel->insert([
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'password' => $hashedPassword,
            'role'     => 'applicant',
            'is_temp_password' => 1
        ]);

        // sending email
        $email = \Config\Services::email();

        $email->setTo($this->request->getPost('email'));
        $email->setSubject('Your Temporary Akademus Account');

        $email->setMessage(
            "Hello,

            Your temporary login credentials:

            Email: " . $this->request->getPost('email') . "
            Password: $tempPassword

            Please login and complete your application."
        );

        $email->send();

        return redirect()->to('/applicationform')->with('success', 'Application submitted successfully');
    }
}