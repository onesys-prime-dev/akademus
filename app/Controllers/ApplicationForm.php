<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

class ApplicationForm extends BaseController 
{
    public function index(): string 
    {
        return view('applicationform.php');       
    }

    public function submit(){
        $model = new ApplicationModel();

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'mobile'   => $this->request->getPost('mobile'),
        ];

        $model->insert($data);

        return redirect()->to('/applicationform')->with('success', 'Application submitted successfully');
    }
}