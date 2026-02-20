<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function sign_in()
    {
        return view('auth/sign_in');
    }

    public function sign_up()
    {
        return view('auth/sign_up');
    }

    public function register()
    {
        helper(['form']);

        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'mobile' => 'required|min_length[10]',
            'program' => 'required',
            'password' => 'required|min_length[6]',
            'passconf' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/sign_up', [
                'validation' => $this->validator
            ]);
        }

        $model = new UserModel();

        $model->insert([
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'program' => $this->request->getPost('program'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'applicant'
        ]);

        return redirect()->to('/sign_in')->with('success', 'Account created successfully.');
    }

    public function login()
    {
        $model = new UserModel();

        $user = $model->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        session()->set([
            'user_id'  => $user['id'],
            'firstname'=> $user['firstname'],
            'email'    => $user['email'],
            'role'     => $user['role'],
            'logged_in'=> true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/sign_in');
    }
}