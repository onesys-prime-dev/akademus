<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;
use App\Models\Applicant\ApplicantModel;
use App\Models\FollowUpMessageModel;
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
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        helper('theme');

        $applicantModel = new ApplicantModel();
        $themeModel     = new ThemeModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$applicant) {
            return redirect()->to('/sign-in');
        }

        $applicantId = $applicant['applicant_id'];

        $this->messageModel->markAsRead($applicantId, 'officer');

        $messages = $this->messageModel->getMessagesForApplicant($applicantId);

        return view('applicant/main', [
            'title'        => 'Follow-Up Messages',
            'page_content' => 'applicant/follow-up-messages/follow_up_messages',
            'theme'        => $themeModel->getAllSettings(),
            'applicant_id' => $applicantId,
            'messages'     => $messages,
        ]);
    }

    /**
     * Polling endpoint — only returns messages newer than since_id.
     * Also marks any new officer messages as read while the applicant is on the page.
     */
    public function fetchMessages()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false]);
        }

        $applicantModel = new ApplicantModel();
        $applicant      = $applicantModel->where('user_id', session()->get('user_id'))->first();

        if (!$applicant) {
            return $this->response->setJSON(['success' => false]);
        }

        $applicantId = (int) $applicant['applicant_id'];
        $sinceId     = (int) ($this->request->getGet('since_id') ?? 0);

        $messages = $sinceId > 0
            ? $this->messageModel->getMessagesSince($applicantId, $sinceId)
            : $this->messageModel->getMessagesForApplicant($applicantId);

        // Mark new officer messages as read while the applicant is actively viewing
        if (!empty($messages)) {
            $this->messageModel->markAsRead($applicantId, 'officer');
        }

        return $this->response->setJSON([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    public function unreadCount()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'count' => 0, 'notifications' => []]);
        }

        $applicantModel = new ApplicantModel();
        $applicant      = $applicantModel->where('user_id', session()->get('user_id'))->first();

        if (!$applicant) {
            return $this->response->setJSON(['success' => false, 'count' => 0, 'notifications' => []]);
        }

        $summary = $this->messageModel->getUnreadSummaryForApplicant((int) $applicant['applicant_id']);

        return $this->response->setJSON([
            'success'       => true,
            'count'         => $summary['count'],
            'notifications' => $summary['notifications'],
        ]);
    }

    public function send()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $applicantModel = new ApplicantModel();
        $applicant      = $applicantModel->where('user_id', session()->get('user_id'))->first();

        if (!$applicant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Applicant not found']);
        }

        $message = trim($this->request->getPost('message') ?? '');

        if ($message === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Message cannot be empty']);
        }

        $messageId = $this->messageModel->insert([
            'applicant_id' => $applicant['applicant_id'],
            'sender_role'  => 'applicant',
            'message'      => $message,
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message_id' => (int) $messageId]);
    }
}
