<?php
// ApplicantForm.php

namespace App\Controllers;

use App\Models\ProgramModel;

class Program extends BaseController
{
    public function getPrograms() {
        $model = new ProgramModel();

        $programs = $model->findAll();

        $response = [
            'programs' => array_map(function($program) {
                return [
                    'id' => $program['program_id'],
                    'name' => $program['program_name']
                ];
            }, $programs)
        ];

        return $this->response->setJSON($response);
    }
}