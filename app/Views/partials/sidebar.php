<!-- HOMEPAGE NAVBAR | homepage.php  -->
<?php 
    $firstname = session()->get('firstname') ?? '';
    $lastname = session()->get('lastname') ?? '';
    $fullname = $firstname . ' ' . $lastname;
    $initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1) ?? '');
    $applicant_number = session()->get('applicant_number') ?? '';
    $role = ucwords(session()->get('role') ?? '');
?>

<nav class="sidebar">

    <div class="sidebar-main">

        <div class="sidebar-header">

            <a href="<?= base_url('/dashboard') ?>" class="sidebar-brand">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>">

                <div class="sidebar-brand-text">
                    <p>AKADEMUS</p>
                    <span>Admission Portal</span>
                </div>
            </a>

            <div class="sidebar-applicant-id">
                <span>Applicant ID</span>
                <p>#<?= $applicant_number ?></p>
            </div>

        </div>


        <div class="sidebar-menu">

            <div class="sidebar-group">

                <p class="sidebar-group-title">MENU</p>

                <div class="sidebar-group-links">

                    <a href="<?= base_url('/dashboard') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Dashboard</span>
                    </a>

                    <a href="<?= base_url('/applicant-form') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Applicant Form</span>
                    </a>

                    <a href="<?= base_url('/requirements') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Requirements</span>
                    </a>

                    <a href="<?= base_url('/tracking-progress') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Tracking Progress</span>
                    </a>

                </div>

            </div>


            <div class="sidebar-group">

                <p class="sidebar-group-title">PAYMENTS</p>

                <div class="sidebar-group-links">

                    <a href="<?= base_url('/admission-processing-fee') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Admission Processing Fee</span>
                    </a>

                    <a href="<?= base_url('/reservation-fee') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Reservation Fee</span>
                    </a>

                </div>

            </div>


            <div class="sidebar-group">

                <p class="sidebar-group-title">SUPPORT</p>

                <div class="sidebar-group-links">

                    <a href="<?= base_url('/faq') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>FAQ & Chat Support</span>
                    </a>

                    <a href="<?= base_url('/follow-up-messages') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Follow-Up Messages</span>
                    </a>

                    <a href="<?= base_url('/tuition-fee-calculator') ?>" class="sidebar-link">
                        <div class="sidebar-link-dot"></div>
                        <span>Tuition Fee Calculator</span>
                    </a>

                </div>

            </div>

        </div>

    </div>


    <div class="sidebar-footer">

        <div class="sidebar-profile">

            <div class="sidebar-profile-avatar">
                <span><?= $initials ?></span>
            </div>

            <div class="sidebar-profile-info">
                <p class="sidebar-profile-name"><?= $fullname ?></p>
                <span class="sidebar-profile-role"><?= $role ?></span>
            </div>

        </div>

        <a href="<?= base_url('logout') ?>" class="sidebar-logout">
            <span>Logout</span>
        </a>

    </div>

</nav>