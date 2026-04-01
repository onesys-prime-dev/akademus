<?php 
    $firstname = session()->get('firstname') ?? 'Applicant';
    $lastname  = session()->get('lastname')  ?? '';
    $fullname  = trim($firstname . ' ' . $lastname);

    // Generate initials from name
    $initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
    if (empty(trim($initials))) $initials = 'AP';

    $applicant_number = session()->get('applicant_number');

    // Theme values
    $app_name  = $theme['app_name']  ?? 'AKADEMUS';
    $logo_url  = !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png');
?>

<nav>
    <div class="sidebar-top-and-links">
        <div class="sidebar-top">
            <a href="<?= base_url('/admission-officer/dashboard') ?>" class="brand">
                <img src="<?= $logo_url ?>">
                <div class="header-wrapper">
                    <p><?= esc($app_name) ?></p>
                    <span>Admission Portal</span>
                </div>
            </a>
        </div>

        <div class="sidebar-links">
            <div class="group-container">
                <p>GENERAL</p>
                
                <div class="grouped-links">
                    <?php $uri = uri_string(); ?>
                    <a href="<?= base_url('/admission-officer/dashboard') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-officer/dashboard') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= base_url('/admission-officer/applicants') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-officer/applicants') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Applicants</span>
                    </a>
                    <a href="<?= base_url('/admission-officer/exam-schedule') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-officer/exam-schedule') ? 'active' : '' ?>">
                        <div class="dotted-link"></div>
                        <span>Exam Schedule</span>
                    </a>
                    <a href="<?= base_url('/admission-officer/follow-up') ?>" class="sidebar-nav-link <?= str_contains($uri, 'admission-officer/follow-up') ? 'active' : '' ?>" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="dotted-link"></div>
                            <span>Follow Up</span>
                        </div>
                        <span id="sidebarMsgBadge" style="
                            display:none;
                            min-width:18px;height:18px;padding:0 5px;
                            background:var(--primary);
                            color:#fff;font-size:10px;font-weight:800;
                            border-radius:999px;
                            align-items:center;justify-content:center;
                            line-height:1;
                        "></span>
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