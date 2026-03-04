<!-- HOMEPAGE NAVBAR | homepage.php  -->
<?php $applicant_id = 'APP-2025-08741' ?>
<?php $applicant_initials = 'JD' ?>

<nav>
    <div class="sidebar-top-and-links">
        <div class="sidebar-top">
            <a href="<?= base_url('/dashboard') ?>" class="brand">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>">
                <div class="header-wrapper">
                    <p>AKADEMUS</p>
                    <span>Admission Portal</span>
                </div>
            </a>

            <div class="applicant-id-box">
                <span>Applicant ID</span>
                <p>#<?= $applicant_id ?></p>
            </div>
        </div>

        <div class="sidebar-links">
            <div class="group-container">
                <p>MENU</p>
                
                <div class="grouped-links">
                    <a href="<?= base_url('/dashboard') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= base_url('/applicant-form') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Applicant Form</span>
                    </a>
                    <a href="<?= base_url('/requirements') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Requirements</span>
                    </a>
                    <a href="<?= base_url('/tracking-progress') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Tracking Progress</span>
                    </a>
                </div>
            </div>

            <div class="group-container">
                <p>PAYMENTS</p>
                
                <div class="grouped-links">
                    <a href="<?= base_url('/admission-processing-fee') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Admission Processing Fee</span>
                    </a>
                    <a href="<?= base_url('/reservation-fee') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Reservation Fee</span>
                    </a>
                </div>
            </div>

            <div class="group-container">
                <p>SUPPORT</p>
                
                <div class="grouped-links">
                    <a href="<?= base_url('/faq') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>FAQ & Chat Support</span>
                    </a>
                    <a href="<?= base_url('/follow-up-messages') ?>" class="sidebar-nav-link">
                        <div class="dotted-link"></div>
                        <span>Follow-Up Messages</span>
                    </a>
                    <a href="<?= base_url('/tuition-fee-calculator') ?>" class="sidebar-nav-link">
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
                <!-- <img src="<?= base_url('assets/images/profile-placeholder.png') ?>" alt="Profile Picture"> -->
                <span><?= $applicant_initials ?></span>
            </div>
            <div class="profile-info">
                <p>John Doe</p>
                <span>Applicant</span>
            </div>
        </div>

        <a href="<?= base_url('logout') ?>" class="logout-link">
            <span>Logout</span>
        </a>
    </div>
</nav>