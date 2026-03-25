<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\FollowUpMessageModel;
use App\Models\Applicant\ApplicantModel;
use App\Models\ThemeModel;

class FollowUpController extends BaseController
{
    protected FollowUpMessageModel $messageModel;

    public function __construct()
    {
        $this->messageModel = new FollowUpMessageModel();
    }

    public function index()
    {
        helper('theme');

        $themeModel    = new ThemeModel();
        $conversations = $this->messageModel->getConversationsSummary();

        return view('admission-officer/main', [
            'title'         => 'Follow-Up Messages',
            'page_content'  => 'admission-officer/follow-up/follow_up',
            'breadcrumb'    => [
                ['label' => 'Dashboard', 'url' => base_url('/admission-officer/dashboard')],
                ['label' => 'Follow-Up Messages'],
            ],
            'theme'         => $themeModel->getAllSettings(),
            'conversations' => $conversations,
        ]);
    }

    public function conversation(int $applicantId)
    {
        helper('theme');

        $themeModel     = new ThemeModel();
        $applicantModel = new ApplicantModel();

        $applicant = $applicantModel->find($applicantId);

        if (!$applicant) {
            return redirect()->to(base_url('/admission-officer/follow-up'));
        }

        $db   = \Config\Database::connect();
        $user = $db->table('users')->where('user_id', $applicant['user_id'])->get()->getRowArray();

        $this->messageModel->markAsRead($applicantId, 'applicant');

        $messages      = $this->messageModel->getMessagesForApplicant($applicantId);
        $conversations = $this->messageModel->getConversationsSummary();

        return view('admission-officer/main', [
            'title'           => 'Follow-Up Messages',
            'page_content'    => 'admission-officer/follow-up/follow_up',
            'breadcrumb'      => [
                ['label' => 'Dashboard', 'url' => base_url('/admission-officer/dashboard')],
                ['label' => 'Follow-Up Messages', 'url' => base_url('/admission-officer/follow-up')],
                ['label' => ($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')],
            ],
            'theme'           => $themeModel->getAllSettings(),
            'conversations'   => $conversations,
            'activeApplicant' => array_merge($applicant, ['firstname' => $user['firstname'] ?? '', 'lastname' => $user['lastname'] ?? '']),
            'messages'        => $messages,
        ]);
    }

    public function send()
    {
        $applicantId = (int) ($this->request->getPost('applicant_id') ?? 0);
        $message     = trim($this->request->getPost('message') ?? '');

        if (!$applicantId || $message === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid payload']);
        }

        $applicantModel = new ApplicantModel();
        if (!$applicantModel->find($applicantId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Applicant not found']);
        }

        $this->messageModel->insert([
            'applicant_id' => $applicantId,
            'sender_role'  => 'officer',
            'message'      => $message,
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function fetchMessages()
    {
        $applicantId = (int) ($this->request->getGet('applicant_id') ?? 0);

        if (!$applicantId) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->messageModel->markAsRead($applicantId, 'applicant');

        $messages = $this->messageModel->getMessagesForApplicant($applicantId);

        return $this->response->setJSON([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    public function unreadCount()
    {
        return $this->response->setJSON([
            'success' => true,
            'count'   => $this->messageModel->getTotalUnreadForOfficer(),
        ]);
    }

    public function fetchConversations()
    {
        $conversations = $this->messageModel->getConversationsSummary();
        return $this->response->setJSON([
            'success'       => true,
            'conversations' => $conversations,
        ]);
    }
}
