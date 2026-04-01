<?php

namespace App\Controllers\Admin;

class ApplicantsController extends BaseAdminController
{
    protected $applicationModel;
    protected $applicantModel;

    public function __construct()
    {
        parent::__construct();
        $this->applicationModel = model('ApplicationModel');
        $this->applicantModel = model('UserModel'); // Assuming UserModel exists
    }

    public function index()
    {
        $this->pageTitle = 'Applications';
        $this->setBreadcrumb('Applicants');

        $status = $this->request->getGet('status');
        $query = $this->applicationModel;

        if ($status && in_array($status, ['pending', 'reviewed', 'approved', 'rejected'])) {
            $query = $query->where('status', $status);
        }

        $data = [
            'sidebarActive' => 'applicants',
            'applications' => $query->orderBy('submitted_at', 'DESC')->paginate(20),
            'pager' => $this->applicationModel->pager,
            'currentStatus' => $status,
        ];

        return $this->renderAdmin('admin/applicants/list', $data);
    }

    public function view($applicationId)
    {
        $application = $this->applicationModel->getApplicationWithAnswers($applicationId);
        if (!$application) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $this->pageTitle = 'View Application';
        $this->setBreadcrumb('Applicants', '/admin/applicants');
        $this->setBreadcrumb('Application');

        $course = model('CourseModel')->find($application['course_id']);
        $mergedFields = service('FormFieldService')
            ->getMergedFormFields($application['course_id']);

        $data = [
            'sidebarActive' => 'applicants',
            'application' => $application,
            'course' => $course,
            'fields' => $mergedFields,
        ];

        return $this->renderAdmin('admin/applicants/view', $data);
    }

    public function updateStatus($applicationId)
    {
        $status = $this->request->getPost('status');

        if (!in_array($status, ['pending', 'reviewed', 'approved', 'rejected'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid status']);
        }

        $this->applicationModel->update($applicationId, ['status' => $status]);

        return redirect()->back()->with('success', 'Application status updated');
    }
}
