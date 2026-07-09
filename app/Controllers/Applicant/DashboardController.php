<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\Applicant\ApplicantModel;
use App\Models\Application\PersonalInformationModel;
use App\Models\Application\FamilyBackgroundModel;
use App\Models\Application\EducationalBackgroundModel;
use App\Models\FormPageModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\ThemeModel;

use App\Services\RequirementService;
use App\Services\ApplicantDocumentService;
use App\Services\ApplicationProgressService;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        $applicantModel = new ApplicantModel();
        $themeModel     = new ThemeModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$applicant) {
            return redirect()->to('/sign-in');
        }

        $db = \Config\Database::connect();

        $application = $db->table('applications')
            ->where('applicant_id', $applicant['applicant_id'])
            ->get()
            ->getRowArray();

        $appStatus = strtolower(trim($applicant['application_status'] ?? ''));

        /*
        |--------------------------------------------------------------------------
        | Shared application progress
        |--------------------------------------------------------------------------
        */
        $progressData = (new ApplicationProgressService())
            ->build($applicant, $application);

        $progressSteps = $progressData['steps'];

        /*
        |--------------------------------------------------------------------------
        | Convert ApplicationProgressService steps
        | -> dashboard step format
        |--------------------------------------------------------------------------
        |
        | dashboard view expects:
        | $steps = [
        |   1 => 'Account',
        |   2 => 'Form',
        | ]
        |
        | $step_statuses = [
        |   1 => 'done',
        |   2 => 'inprogress',
        | ]
        |
        */

        $steps = [];
        $stepStatuses = [];

        foreach ($progressSteps as $index => $step) {
            $num = $index + 1;

            $steps[$num] = $step['title'];

            $stepStatuses[$num] = match ($step['status']) {
                'done', 'rejected' => 'done',
                'active'           => 'inprogress',
                default            => '',
            };
        }

        $doneSteps = count(array_filter(
            $progressSteps,
            fn($step) => in_array($step['status'], ['done', 'rejected'])
        ));

        $overallProgress = count($progressSteps) > 0
            ? (int) round(($doneSteps / count($progressSteps)) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Form + document checklist stats (dashboard cards)
        |--------------------------------------------------------------------------
        */
        $programId = $application['program_id']
            ?? $applicant['program_id']
            ?? session()->get('selected_program_id');

        if ($programId) {
            $presetSvc = new \App\Services\PresetService();
            $formPages = $presetSvc->getFormPagesForApplicant((int) $programId);

            if (empty($formPages)) {
                $formPages = (new FormPageModel())->getEnabledOrdered();
            }
        } else {
            $formPages = (new FormPageModel())->getEnabledOrdered();
        }

        $formPageCount  = count($formPages);
        $formCompletion = array_fill_keys(array_column($formPages, 'id'), false);

        if ($application) {
            $appId = $application['application_id'];

            $pi = (new PersonalInformationModel())
                ->where('application_id', $appId)
                ->first();

            $fb = (new FamilyBackgroundModel())
                ->where('application_id', $appId)
                ->first();

            $eb = (new EducationalBackgroundModel())
                ->where('application_id', $appId)
                ->first();

            foreach ($formPages as $page) {
                $pageId = (int) $page['id'];

                $formCompletion[$pageId] = match ($pageId) {
                    1 => !empty($application['applicant_type']),
                    2 => !empty($pi['firstname']),
                    3 => !empty($fb['father_lastname']) || !empty($fb['guardian_name']),
                    4 => !empty($eb['school_name']),
                    default => false,
                };
            }
        }

        $formPagesCompleted = count(array_filter($formCompletion));

        $formProgress = $formPageCount > 0
            ? (int) round(($formPagesCompleted / $formPageCount) * 100)
            : 0;

        $docStats = [
            'total_required'    => 0,
            'uploaded_required' => 0,
            'missing_required'  => 0,
            'total_optional'    => 0,
            'uploaded_optional' => 0,
            'all_requirements'  => [],
            'uploaded_docs'     => [],
        ];

        if ($programId) {
            $allReqs = (new RequirementService())->getRequirements($programId);

            if (!empty($allReqs)) {
                $uploadedDocs = (new ApplicantDocumentService())
                    ->getStepDocuments($applicant['applicant_id'], $allReqs);

                $totalRequired = count(array_filter(
                    $allReqs,
                    fn($r) => $r['is_required']
                ));

                $uploadedRequired = count(array_filter(
                    $allReqs,
                    fn($r) =>
                    $r['is_required']
                        && isset($uploadedDocs[$r['program_requirement_id']])
                ));

                $totalOptional = count(array_filter(
                    $allReqs,
                    fn($r) => !$r['is_required']
                ));

                $uploadedOptional = count(array_filter(
                    $allReqs,
                    fn($r) =>
                    !$r['is_required']
                        && isset($uploadedDocs[$r['program_requirement_id']])
                ));

                $docStats = [
                    'total_required'    => $totalRequired,
                    'uploaded_required' => $uploadedRequired,
                    'missing_required'  => $totalRequired - $uploadedRequired,
                    'total_optional'    => $totalOptional,
                    'uploaded_optional' => $uploadedOptional,
                    'all_requirements'  => $allReqs,
                    'uploaded_docs'     => $uploadedDocs,
                ];
            }
        }

        $docProgress = $docStats['total_required'] > 0
            ? (int) round(
                ($docStats['uploaded_required'] / $docStats['total_required']) * 100
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Banner message
        |--------------------------------------------------------------------------
        */
        if ($appStatus === 'enrolled') {
            $bannerMessage = 'You are now officially enrolled. Welcome to the family!';
        } elseif ($appStatus === 'approved') {
            $bannerMessage = 'Congratulations! Your application has been approved. Please settle your reservation fee.';
        } elseif ($docProgress === 100) {
            $bannerMessage = 'All documents uploaded! Your application is under review.';
        } elseif ($docProgress > 0) {
            $bannerMessage = 'Your application is in progress. Upload the remaining documents to move to the verification stage.';
        } elseif ($formProgress === 100) {
            $bannerMessage = 'Your form is complete! Proceed to upload the required documents.';
        } elseif ($formProgress > 0) {
            $bannerMessage = 'Your application form is in progress. Continue where you left off.';
        } else {
            $bannerMessage = 'Welcome! Start your application by filling out the applicant form below.';
        }

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */
        $reservationFee = (new PaymentFeeTypeModel())->getByCode(PaymentFeeTypeModel::CODE_RESERVATION);
        $reservationFeeAmount = $reservationFee['amount'] ?? 2000;

        $fastTrackFee = (new PaymentFeeTypeModel())->getByCode(PaymentFeeTypeModel::CODE_FAST_TRACK);
        $fastTrackFeeAmount = $fastTrackFee['amount'] ?? 1000;

        $payments = (new PaymentModel())
            ->select('payment.payment_status, payment_fee_type.code')
            ->join(
                'payment_fee_type',
                'payment_fee_type.payment_fee_type_id = payment.payment_fee_type_id'
            )
            ->where('payment.applicant_id', $applicant['applicant_id'])
            ->findAll();

        $paidMap = [];

        foreach ($payments as $pay) {
            if ($pay['payment_status'] === 'PAID') {
                $paidMap[$pay['code']] = true;
            }
        }

        $isFastTrack = $progressData['is_fast_track'];

        $paymentCheckList = [
            [
                'title' => 'Admission Processing Fee',
                'paid'  => isset($paidMap['ADMISSION_FEE']),
            ],
            [
                'title' => 'Reservation Fee',
                'paid'  => isset($paidMap['RESERVATION_FEE']),
            ],
        ];

        if ($isFastTrack) {
            $paymentCheckList[] = [
                'title' => 'Fast Track Fee',
                'paid'  => isset($paidMap['FAST_TRACK_FEE']),
            ];
        }

        $showPaymentNotification =
            $appStatus === 'approved'
            && !isset($paidMap['RESERVATION_FEE']);

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */
        $data = [
            'title'            => 'Dashboard',
            'page_content'     => 'applicant/dashboard/dashboard',
            'applicant_number' => $applicant['applicant_number'] ?? 'N/A',
            'firstname'        => session()->get('firstname'),
            'theme'            => $themeModel->getAllSettings(),

            // shared progress
            'steps'            => $steps,
            'progress'         => $overallProgress,
            'banner_message'   => $bannerMessage,
            'step_statuses'    => $stepStatuses,

            // merged shared meta
            'app_status'         => $progressData['app_status'],
            'decision_remarks'   => $progressData['decision_remarks'],
            'form_complete'      => $progressData['form_complete'],
            'form_submitted'     => $progressData['form_submitted'],
            'docs_complete'      => $progressData['docs_complete'],
            'exam_enabled'       => $progressData['exam_enabled'],
            'exam_booking'       => $progressData['exam_booking'],
            'revision_requested' => $progressData['revision_requested'],
            'revision_note'      => $progressData['revision_note'],
            'revision_pages'     => $progressData['revision_pages'],
            'is_fast_track'         => $isFastTrack,
            'pending_fast_track_pages' => $progressData['pending_fast_track_pages'],

            // checklist cards
            'form_pages'       => $formPages,
            'form_completion'  => $formCompletion,
            'form_completed'   => $formPagesCompleted,
            'form_total'       => $formPageCount,
            'form_progress'    => $formProgress,

            'doc_stats'        => $docStats,
            'doc_progress'     => $docProgress,

            // payment
            'reservation_fee_amount'  => $reservationFeeAmount,
            'fast_track_fee_amount'   => $fastTrackFeeAmount,
            'fast_track_fee_paid'     => isset($paidMap['FAST_TRACK_FEE']),
            'paymentCheckList'        => $paymentCheckList,
            'showPaymentNotification' => $showPaymentNotification
        ];

        return view('applicant/main', $data);
    }
}
