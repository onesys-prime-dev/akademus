<?php

namespace App\Controllers\Admin;

use App\Models\ProgramModel;
use App\Models\ThemeModel;

class CoursesController extends BaseAdminController
{
    protected $programModel;
    protected $themeModel;

    public function __construct()
    {
        parent::__construct();
        $this->programModel = new ProgramModel();
        $this->themeModel   = new ThemeModel();
    }

    private function theme(): array
    {
        return $this->themeModel->getAllSettings() ?? [];
    }

    public function index()
    {
        return view('admin/programs/list', [
            'programs' => $this->programModel->getAllPrograms(),
            'theme'    => $this->theme(),
            'title'    => 'Manage Programs',
        ]);
    }

    public function create()
    {
        $errors = [];
        $old    = ['program_name' => '', 'program_code' => ''];

        if ($this->request->getMethod() === 'POST') {
            $name = trim($this->request->getPost('program_name') ?? '');
            $code = strtoupper(trim($this->request->getPost('program_code') ?? ''));
            $old  = ['program_name' => $name, 'program_code' => $code];

            if ($name === '') $errors['program_name'] = 'Program name is required.';
            if ($code === '') $errors['program_code'] = 'Program code is required.';

            if (empty($errors)) {
                // Use plain where() — MySQL utf8mb4_general_ci handles case-insensitivity
                if ($this->programModel->where('program_name', $name)->countAllResults()) {
                    $errors['program_name'] = 'A program with this name already exists.';
                }
                if ($this->programModel->where('program_code', $code)->countAllResults()) {
                    $errors['program_code'] = 'A program with this code already exists.';
                }
            }

            if (empty($errors)) {
                $inserted = $this->programModel->insert([
                    'program_name' => $name,
                    'program_code' => $code,
                ]);

                if ($inserted) {
                    session()->setFlashdata('success', 'Program created successfully.');
                    return redirect()->to(base_url('admin/programs'));
                }

                $errors['_general'] = 'Could not save the program. Please try again.';
            }
        }

        return view('admin/programs/form', [
            'theme'   => $this->theme(),
            'title'   => 'Create Program',
            'errors'  => $errors,
            'old'     => $old,
            'program' => null,
        ]);
    }

    public function edit($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            session()->setFlashdata('error', 'Program not found.');
            return redirect()->to(base_url('admin/programs'));
        }

        $errors = [];
        $old    = ['program_name' => $program['program_name'], 'program_code' => $program['program_code']];

        if ($this->request->getMethod() === 'POST') {
            $name = trim($this->request->getPost('program_name') ?? '');
            $code = strtoupper(trim($this->request->getPost('program_code') ?? ''));
            $old  = ['program_name' => $name, 'program_code' => $code];

            if ($name === '') $errors['program_name'] = 'Program name is required.';
            if ($code === '') $errors['program_code'] = 'Program code is required.';

            if (empty($errors)) {
                if ($this->programModel->where('program_name', $name)->where('program_id !=', $programId)->countAllResults()) {
                    $errors['program_name'] = 'A program with this name already exists.';
                }
                if ($this->programModel->where('program_code', $code)->where('program_id !=', $programId)->countAllResults()) {
                    $errors['program_code'] = 'A program with this code already exists.';
                }
            }

            if (empty($errors)) {
                $updated = $this->programModel->update($programId, [
                    'program_name' => $name,
                    'program_code' => $code,
                ]);

                if ($updated) {
                    session()->setFlashdata('success', 'Program updated successfully.');
                    return redirect()->to(base_url('admin/programs'));
                }

                $errors['_general'] = 'Could not update the program. Please try again.';
            }
        }

        return view('admin/programs/form', [
            'theme'   => $this->theme(),
            'title'   => 'Edit Program',
            'errors'  => $errors,
            'old'     => $old,
            'program' => $program,
        ]);
    }

    public function delete($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            session()->setFlashdata('error', 'Program not found.');
            return redirect()->to(base_url('admin/programs'));
        }

        $db         = \Config\Database::connect();
        $inUseCount = $db->table('applicants')->where('program_id', $programId)->countAllResults();

        if ($inUseCount > 0) {
            session()->setFlashdata('error', "Cannot delete \"{$program['program_name']}\" — {$inUseCount} applicant(s) are enrolled in this program.");
            return redirect()->to(base_url('admin/programs'));
        }

        $this->programModel->delete($programId);
        session()->setFlashdata('success', "\"{$program['program_name']}\" deleted successfully.");
        return redirect()->to(base_url('admin/programs'));
    }
}
