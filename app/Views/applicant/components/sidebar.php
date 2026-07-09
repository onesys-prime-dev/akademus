<!-- HOMEPAGE NAVBAR | homepage.php  -->
<!-- HOMEPAGE NAVBAR | sidebar.php  -->
<?php
$firstname = session()->get('firstname') ?? 'Applicant';
$lastname  = session()->get('lastname')  ?? '';
$fullname  = trim($firstname . ' ' . $lastname);

// Generate initials from name
$initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
if (empty(trim($initials))) $initials = 'AP';

$applicant_number = session()->get('applicant_number');

// Theme values
/** @var array $theme */
$app_name    = $theme['app_name']  ?? 'AKADEMUS';
$logo_url    = !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png');
$examEnabled = (new \App\Models\ThemeModel())->getSetting('exam_enabled', '1') === '1';
$tuitionCalcEnabled = (new \App\Models\ThemeModel())->getSetting('tuition_calculator_enabled', '1') === '1';

$isApproved = (new \App\Models\Applicant\ApplicantModel())->getAppStatus('application_status', '') === 'approved';
$examPassed = (new \App\Models\ExamBookingModel())->getResultStatus('result_status', '') === 'PASSED';
?>

<nav>
    <div class="sidebar-top-and-links">
        <div class="sidebar-top">
            <a href="<?= base_url('/dashboard') ?>" class="brand">
                <img src="<?= $logo_url ?>">
                <div class="header-wrapper">
                    <p><?= esc($app_name) ?></p>
                    <span>Admission Portal</span>
                </div>
            </a>

            <div class="applicant-id-box">
                <span>Applicant ID</span>
                <p>#<?= esc($applicant_number) ?></p>
            </div>
        </div>

        <div class="sidebar-links">
            <div class="group-container">
                <p>MENU</p>

                <?php $uri = uri_string(); ?>
                <div class="grouped-links">
                    <a href="<?= base_url('/dashboard') ?>" class="sidebar-nav-link <?= $uri === 'dashboard' ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= base_url('/applicant-form') ?>" class="sidebar-nav-link <?= str_contains($uri, 'applicant-form') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Applicant Form</span>
                    </a>
                    <a href="<?= base_url('/requirements') ?>" class="sidebar-nav-link <?= str_contains($uri, 'requirements') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Requirements</span>
                    </a>
                    <a href="<?= base_url('/tracking-progress') ?>" class="sidebar-nav-link <?= str_contains($uri, 'tracking-progress') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Tracking Progress</span>
                    </a>
                    <?php if ($examEnabled): ?>
                        <a
                            href="<?= $isApproved ? base_url('/exam-schedule') : 'javascript:void(0)' ?>"
                            class="sidebar-nav-link <?= str_contains($uri, 'exam-schedule') ? 'active' : '' ?> <?= !$isApproved ? 'disabled-link' : '' ?>">
                            <div class="dotted-link"></div>
                            <span>Exam Schedule</span>

                            <?php if (!$isApproved): ?>
                                <div class="lock-help-wrap" onclick="this.classList.toggle('show-tip')">
                                    <svg class="lock-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>

                                    <div class="lock-tooltip">
                                        unlocks after admission approval
                                    </div>
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="group-container">
                <div class="group-title-row">
                    <p>PAYMENTS</p>
                </div>
                <div class="grouped-links">

                    <a href="<?= base_url('/admission-processing-fee') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-processing-fee') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Admission Processing Fee</span>
                    </a>

                    <a
                        href="<?= $examPassed ? base_url('/reservation-fee') : 'javascript:void(0)' ?>"
                        class="sidebar-nav-link <?= str_contains($uri, 'reservation-fee') ? 'active' : '' ?> <?= !$examPassed ? 'disabled-link' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Reservation Fee</span>

                        <?php if (!$examPassed): ?>
                            <div class="lock-help-wrap" onclick="this.classList.toggle('show-tip')">
                                <!-- Lock icon -->
                                <svg class="lock-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>

                                <!-- Tooltip -->
                                <div class="lock-tooltip">
                                    unlocks after passing the entrance examination
                                </div>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>

            </div>

            <div class="group-container">
                <p>SUPPORT</p>

                <div class="grouped-links">
                    <a href="<?= base_url('/faq') ?>" class="sidebar-nav-link <?= $uri === 'faq' ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>FAQ & Chat Support</span>
                    </a>
                    <a href="<?= base_url('/follow-up-messages') ?>" class="sidebar-nav-link <?= str_contains($uri, 'follow-up-messages') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Follow-Up Messages</span>
                    </a>
                    <?php if ($tuitionCalcEnabled): ?>
                        <a href="<?= base_url('/tuition-fee-calculator') ?>" class="sidebar-nav-link <?= str_contains($uri, 'tuition-fee-calculator') ? 'active' : '' ?>">
                            <div class="dotted-link"></div>
                            <span>Tuition Fee Calculator</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-bottom">
        <div class="profile-container">
            <div class="profile-picture">
                <span><?= $initials ?></span>
            </div>
            <div class="profile-info">
                <p><?= esc($fullname) ?></p>
                <span>Applicant</span>
            </div>
        </div>

        <a href="<?= base_url('logout') ?>" class="logout-link">
            <span>Logout</span>
        </a>
    </div>
</nav>

<script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.lock-help-wrap').forEach(el => {
            if (!el.contains(e.target)) {
                el.classList.remove('show-tip');
            }
        });
    });
</script>