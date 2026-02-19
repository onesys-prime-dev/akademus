<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

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

        $model = new ApplicationModel();

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'contactnum'   => $this->request->getPost('contactnum'),
        ];

        $model->insert($data);

        return redirect()->to('/applicationform')->with('success', 'Application submitted successfully');
    }
}