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
    $app_name    = $theme['app_name']  ?? 'AKADEMUS';
    $logo_url    = !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png');
    $examEnabled = (new \App\Models\ThemeModel())->getSetting('exam_enabled', '1') === '1';
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
                    <a href="<?= base_url('/exam-schedule') ?>" class="sidebar-nav-link <?= str_contains($uri, 'exam-schedule') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Exam Schedule</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="group-container">
                <p>PAYMENTS</p>
                <div class="grouped-links">
                    <a href="<?= base_url('/admission-processing-fee') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-processing-fee') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Admission Processing Fee</span>
                    </a>
                    <a href="<?= base_url('/reservation-fee') ?>" class="sidebar-nav-link <?= str_contains($uri, 'reservation-fee') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Reservation Fee</span>
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
                    <a href="<?= base_url('/tuition-fee-calculator') ?>" class="sidebar-nav-link <?= str_contains($uri, 'tuition-fee-calculator') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Tuition Fee Calculator</span>
                    </a>
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