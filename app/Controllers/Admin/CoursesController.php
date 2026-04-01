<?php

namespace App\Controllers\Admin;

class ProgramsController extends BaseAdminController
{
    protected $programModel;

    public function __construct()
    {
        parent::__construct();
        $this->programModel = model('ProgramModel');
    }

    public function index()
    {
        $this->pageTitle = 'Manage Programs';
        $this->setBreadcrumb('Programs');

        $data = [
            'sidebarActive' => 'programs',
            'programs' => $this->programModel->getAllPrograms(),
        ];

        return $this->renderAdmin('admin/programs/list', $data);
    }

    public function create()
    {
        $this->pageTitle = 'Create Program';
        $this->setBreadcrumb('Programs', '/admin/programs');
        $this->setBreadcrumb('Create');

        if ($this->request->getMethod() === 'post') {
            $data = [
                'program_name' => $this->request->getPost('program_name'),
                'program_code' => $this->request->getPost('program_code'),
            ];

            if ($this->programModel->save($data)) {
                return redirect()->to('/admin/programs')->with('success', 'Program created');
            }

            $data['errors'] = $this->programModel->errors();
        }

        $data['sidebarActive'] = 'programs';

        return $this->renderAdmin('admin/programs/form', $data ?? []);
    }

    public function edit($programId)
    {
        $program = $this->programModel->find($programId);
        if (!$program) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $this->pageTitle = 'Edit Program';
        $this->setBreadcrumb('Programs', '/admin/programs');
        $this->setBreadcrumb('Edit');

        if ($this->request->getMethod() === 'post') {
            $data = [
                'program_id' => $programId,
                'program_name' => $this->request->getPost('program_name'),
                'program_code' => $this->request->getPost('program_code'),
            ];

            if ($this->programModel->save($data)) {
                return redirect()->to('/admin/programs')->with('success', 'Program updated');
            }

            $program['errors'] = $this->programModel->errors();
        }

        $program['sidebarActive'] = 'programs';

        return $this->renderAdmin('admin/programs/form', $program);
    }

    public function delete($programId)
    {
        $this->programModel->delete($programId);
        return redirect()->to('/admin/programs')->with('success', 'Program deleted');
    }
}
