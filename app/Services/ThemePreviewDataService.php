<?php

namespace App\Services;

use App\Models\FormPageModel;
use App\Models\ProgramModel;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;

/**
 * Builds fake-but-realistic $data payloads for the admin Theme Customizer's
 * live preview, reusing the exact real view templates and, wherever safe
 * (global/program-scoped data, not tied to a real applicant row), the exact
 * real services — so the preview only ever diverges from production in the
 * handful of applicant-specific numbers (name, progress, uploaded docs),
 * never in markup or CSS.
 *
 * The fake persona uses a sentinel applicant_id that can never match a real
 * row, so any query scoped to "this applicant" (uploaded docs, exam booking,
 * payments, messages) comes back empty/null — safe, and reads as "just
 * started" rather than broken.
 */
class ThemePreviewDataService
{
    private const PERSONA_FIRSTNAME  = 'Juan';
    private const PERSONA_LASTNAME   = 'Dela Cruz';
    private const PERSONA_NUMBER     = 'APP-2026-00001';
    private const PERSONA_APPLICANT_ID = 999999999;
    private const PERSONA_PROGRAM_ID   = 1;
    private const PERSONA_PROGRAM_NAME = 'Bachelor of Science in Computer Science';

    /**
     * Session keys the shared layouts/partials read directly (not via $data).
     * ThemeController snapshots + restores these around the render.
     */
    public const SESSION_PERSONA = [
        'firstname'          => self::PERSONA_FIRSTNAME,
        'lastname'            => self::PERSONA_LASTNAME,
        'applicant_number'    => self::PERSONA_NUMBER,
        'applicant_id'        => self::PERSONA_APPLICANT_ID,
        'program_name'        => self::PERSONA_PROGRAM_NAME,
        'selected_program_id' => self::PERSONA_PROGRAM_ID,
        'program_id'          => self::PERSONA_PROGRAM_ID,
    ];

    private ThemeModel $themeModel;

    public function __construct()
    {
        $this->themeModel = new ThemeModel();
    }

    public function build(string $page): array
    {
        return match ($page) {
            'dashboard'      => $this->buildDashboard(),
            'appform'        => $this->buildApplicantForm(),
            'documents'      => $this->buildRequirements(),
            'track'          => $this->buildTrackingProgress(),
            'exam'           => $this->buildExamSchedule(),
            'admission-fee'  => $this->buildAdmissionFee(),
            'reservation-fee' => $this->buildReservationFee(),
            'faq'            => $this->buildFaq(),
            'followup'       => $this->buildFollowUp(),
            'tuition-calc'   => $this->buildTuitionCalculator(),
            'signin'         => $this->buildSignIn(),
            'signup'         => $this->buildSignUp(),
            'success'        => $this->buildSuccess(),
            default          => $this->buildDashboard(),
        };
    }

    private function fakeApplicant(): array
    {
        return [
            'applicant_id'       => self::PERSONA_APPLICANT_ID,
            'applicant_number'   => self::PERSONA_NUMBER,
            'application_status' => 'pending',
            'decision_remarks'   => null,
            'application_date'   => date('Y-m-d H:i:s', strtotime('-3 days')),
            'program_id'         => self::PERSONA_PROGRAM_ID,
        ];
    }

    private function fakeApplication(): array
    {
        return [
            'application_id'     => self::PERSONA_APPLICANT_ID,
            'application_status' => '',
            'is_fast_track'      => 0,
            'program_id'         => self::PERSONA_PROGRAM_ID,
            'revision_note'      => null,
            'revision_pages'     => '[]',
            'applicant_type'     => 'freshman',
            'updated_at'         => null,
        ];
    }

    private function theme(): array
    {
        return $this->themeModel->getAllSettings();
    }

    // ── Dashboard ─────────────────────────────────────────────
    private function buildDashboard(): array
    {
        $applicant   = $this->fakeApplicant();
        $application = $this->fakeApplication();
        $programId   = self::PERSONA_PROGRAM_ID;

        $progressData = (new ApplicationProgressService())->build($applicant, $application);

        $steps = [];
        $stepStatuses = [];
        foreach ($progressData['steps'] as $index => $step) {
            $num = $index + 1;
            $steps[$num] = $step['title'];
            $stepStatuses[$num] = match ($step['status']) {
                'done', 'rejected' => 'done',
                'active'           => 'inprogress',
                default            => '',
            };
        }
        $doneSteps = count(array_filter($progressData['steps'], fn ($s) => in_array($s['status'], ['done', 'rejected'], true)));
        $overallProgress = count($progressData['steps']) > 0
            ? (int) round(($doneSteps / count($progressData['steps'])) * 100)
            : 0;

        $formPages = (new PresetService())->getFormPagesForApplicant($programId);
        if (empty($formPages)) {
            $formPages = (new FormPageModel())->getEnabledOrdered();
        }
        $formPageCount  = count($formPages);
        $formCompletion = array_fill_keys(array_column($formPages, 'id'), false);
        $formPagesCompleted = 0;
        $formProgress = 0;

        $allReqs = (new RequirementService())->getRequirements($programId);
        $uploadedDocs = !empty($allReqs)
            ? (new ApplicantDocumentService())->getStepDocuments(self::PERSONA_APPLICANT_ID, $allReqs)
            : [];

        $totalRequired    = count(array_filter($allReqs, fn ($r) => $r['is_required']));
        $uploadedRequired = count(array_filter($allReqs, fn ($r) => $r['is_required'] && isset($uploadedDocs[$r['program_requirement_id']])));
        $totalOptional    = count(array_filter($allReqs, fn ($r) => !$r['is_required']));
        $uploadedOptional = count(array_filter($allReqs, fn ($r) => !$r['is_required'] && isset($uploadedDocs[$r['program_requirement_id']])));

        $docStats = [
            'total_required'    => $totalRequired,
            'uploaded_required' => $uploadedRequired,
            'missing_required'  => $totalRequired - $uploadedRequired,
            'total_optional'    => $totalOptional,
            'uploaded_optional' => $uploadedOptional,
            'all_requirements'  => $allReqs,
            'uploaded_docs'     => $uploadedDocs,
        ];
        $docProgress = $totalRequired > 0 ? (int) round(($uploadedRequired / $totalRequired) * 100) : 0;

        $reservationFee = (new PaymentFeeTypeModel())->where('code', PaymentFeeTypeModel::CODE_RESERVATION)->where('is_active', 1)->first();
        $fastTrackFee   = (new PaymentFeeTypeModel())->where('code', PaymentFeeTypeModel::CODE_FAST_TRACK)->where('is_active', 1)->first();

        $paymentCheckList = [
            ['title' => 'Admission Processing Fee', 'paid' => false],
            ['title' => 'Reservation Fee', 'paid' => false],
        ];

        return [
            'view' => 'applicant/main',
            'data' => [
                'title'            => 'Dashboard',
                'page_content'     => 'applicant/dashboard/dashboard',
                'applicant_number' => self::PERSONA_NUMBER,
                'firstname'        => self::PERSONA_FIRSTNAME,
                'theme'            => $this->theme(),
                'steps'            => $steps,
                'progress'         => $overallProgress,
                'banner_message'   => 'Welcome! Start your application by filling out the applicant form below.',
                'step_statuses'    => $stepStatuses,
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
                'is_fast_track'         => false,
                'pending_fast_track_pages' => [],
                'form_pages'       => $formPages,
                'form_completion'  => $formCompletion,
                'form_completed'   => $formPagesCompleted,
                'form_total'       => $formPageCount,
                'form_progress'    => $formProgress,
                'doc_stats'        => $docStats,
                'doc_progress'     => $docProgress,
                'reservation_fee_amount'  => $reservationFee['amount'] ?? 2000,
                'fast_track_fee_amount'   => $fastTrackFee['amount'] ?? 1000,
                'fast_track_fee_paid'     => false,
                'paymentCheckList'        => $paymentCheckList,
                'showPaymentNotification' => false,
            ],
        ];
    }

    // ── Applicant Form ────────────────────────────────────────
    private function buildApplicantForm(): array
    {
        $theme = $this->theme();
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);

        return [
            'view' => 'applicant/main',
            'data' => [
                'title'         => 'Applicant Form',
                'page_content'  => 'applicant/applicant-form/applicant-form',
                'initial_step'  => 1,
                'max_steps'     => 4,
                'is_submitted'  => false,
                'theme'         => $theme,
            ],
        ];
    }

    // ── Requirements ──────────────────────────────────────────
    private function buildRequirements(): array
    {
        $theme = $this->theme();
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);

        $categories = (new RequirementService())->getCategories(self::PERSONA_PROGRAM_ID);

        return [
            'view' => 'applicant/main',
            'data' => [
                'title'                 => 'Requirements',
                'page_content'          => 'applicant/requirements/requirements',
                'initial_step'          => 1,
                'max_steps'             => count($categories) + 1,
                'categories'            => $categories,
                'theme'                 => $theme,
                'application_status'    => '',
                'requiredDocsVerified'  => false,
                'is_fast_track'         => false,
                'fast_track_required_docs' => [],
            ],
        ];
    }

    // ── Tracking Progress ─────────────────────────────────────
    private function buildTrackingProgress(): array
    {
        $applicant   = $this->fakeApplicant();
        $application = $this->fakeApplication();
        $progressData = (new ApplicationProgressService())->build($applicant, $application);

        return [
            'view' => 'applicant/main',
            'data' => array_merge($progressData, [
                'title'            => 'Tracking Progress',
                'page_content'     => 'applicant/tracking-progress/tracking_progress',
                'theme'            => $this->theme(),
                'applicant_number' => self::PERSONA_NUMBER,
            ]),
        ];
    }

    // ── Exam Schedule ─────────────────────────────────────────
    private function buildExamSchedule(): array
    {
        return [
            'view' => 'applicant/main',
            'data' => [
                'title'     => 'Exam Schedule',
                'page_content' => 'applicant/exam-schedule/exam-schedule',
                'theme'     => $this->theme(),
                'booking'   => null,
                'available' => (new ExamScheduleService())->getAvailableSchedules(),
            ],
        ];
    }

    // ── Admission Processing Fee ──────────────────────────────
    private function buildAdmissionFee(): array
    {
        $fee = (new PaymentFeeTypeModel())->where('code', PaymentFeeTypeModel::CODE_ADMISSION)->where('is_active', 1)->first();

        return [
            'view' => 'applicant/main',
            'data' => [
                'title'            => 'Admission Processing Fee',
                'page_content'     => 'applicant/payments/admission-processing-fee',
                'theme'            => $this->theme(),
                'fee'              => $fee,
                'payment_methods'  => $this->paymentMethods(),
                'existing_payment' => null,
                'amount'           => $fee['amount'] ?? 0,
            ],
        ];
    }

    // ── Reservation Fee ───────────────────────────────────────
    private function buildReservationFee(): array
    {
        $fee = (new PaymentFeeTypeModel())->where('code', PaymentFeeTypeModel::CODE_RESERVATION)->where('is_active', 1)->first();

        return [
            'view' => 'applicant/main',
            'data' => [
                'title'            => 'Reservation Fee',
                'page_content'     => 'applicant/payments/reservation-fee',
                'theme'            => $this->theme(),
                'fee'              => $fee,
                'payment_methods'  => $this->paymentMethods(),
                'existing_payment' => null,
                'amount'           => $fee['amount'] ?? 0,
            ],
        ];
    }

    private function paymentMethods(): array
    {
        $rows = (new PaymentMethodModel())->getActiveWithAccounts();
        $map  = [];
        foreach ($rows as $row) {
            $code = $row['code'];
            if (isset($map[$code])) {
                continue;
            }
            $map[$code] = [
                'payment_method_id'         => (int) $row['payment_method_id'],
                'code'                      => $code,
                'name'                      => $row['name'],
                'payment_method_account_id' => !empty($row['payment_method_account_id']) ? (int) $row['payment_method_account_id'] : null,
                'account_number'            => $row['account_number'] ?? null,
                'account_name'              => $row['account_name'] ?? null,
                'label'                     => 'Send payment via ' . $row['name'],
            ];
        }
        return array_values($map);
    }

    // ── FAQ ────────────────────────────────────────────────────
    private function buildFaq(): array
    {
        return [
            'view' => 'applicant/main',
            'data' => [
                'title'        => 'FAQ & Chat Support',
                'page_content' => 'applicant/faq/faq',
                'theme'        => $this->theme(),
            ],
        ];
    }

    // ── Follow-Up Messages ────────────────────────────────────
    private function buildFollowUp(): array
    {
        return [
            'view' => 'applicant/main',
            'data' => [
                'title'         => 'Follow-Up Messages',
                'page_content'  => 'applicant/follow-up-messages/follow_up_messages',
                'theme'         => $this->theme(),
                'applicant_id'  => self::PERSONA_APPLICANT_ID,
                'messages'      => [],
            ],
        ];
    }

    // ── Tuition Fee Calculator ────────────────────────────────
    private function buildTuitionCalculator(): array
    {
        return [
            'view' => 'applicant/main',
            'data' => [
                'title'        => 'Tuition Fee Calculator',
                'active_menu'  => 'tuition_calculator',
                'page_content' => 'applicant/tuition-fee-calculator/tuition-fee-calculator',
                'theme'        => $this->theme(),
            ],
        ];
    }

    // ── Auth pages ─────────────────────────────────────────────
    private function buildSignIn(): array
    {
        return [
            'view' => 'auth/main',
            'data' => [
                'title'           => 'Sign In',
                'auth_header'     => 'Welcome Back!',
                'auth_sub_header' => 'Sign in to continue your application.',
                'auth_field'      => 'auth/sign-in',
                'theme'           => $this->theme(),
            ],
        ];
    }

    private function buildSignUp(): array
    {
        $programs = array_map(
            fn ($p) => ['id' => $p['program_id'], 'name' => $p['program_name']],
            (new ProgramModel())->findAll()
        );

        return [
            'view' => 'auth/main',
            'data' => [
                'title'           => 'Sign Up',
                'auth_header'     => 'Create Account',
                'auth_sub_header' => 'Start your admission journey today.',
                'auth_field'      => 'auth/sign-up',
                'programs'        => $programs,
                'theme'           => $this->theme(),
            ],
        ];
    }

    private function buildSuccess(): array
    {
        return [
            'view' => 'auth/main',
            'data' => [
                'title'            => 'Success',
                'auth_header'      => 'Account Created!',
                'auth_sub_header'  => 'Your unique Applicant ID has been assigned.',
                'applicant_number' => self::PERSONA_NUMBER,
                'auth_field'       => 'auth/success',
                'programs'         => [],
                'theme'            => $this->theme(),
            ],
        ];
    }

    private function lightenHex(string $hex, float $amount): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return '#e8f0ff';
        }
        [$r, $g, $b] = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
        $r = (int) round($r + (255 - $r) * $amount);
        $g = (int) round($g + (255 - $g) * $amount);
        $b = (int) round($b + (255 - $b) * $amount);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
