<!-- dashboard/menu/dashboard.php  -->
<?php $applicant_number = session()->get('applicant_number') ?>

<!-- FRONTEND MOCK DATA: These are example variables. Backend should populate dynamically. -->
<?php
    /*  i'll comment this for now; remove ko to soon
        bali automatic na to, after user signs up and in
        mapakit na nang applicant_num nya
    - JOSH B */
    // Applicant
    // $applicantDetails = [
    //     'firstname' => 'Juan',
    //     'applicant_id' => 'APP-2025-08741',
    //     'applicant_initials' => 'JD',
    // ];

    // Progress
    $bannerProgress = [
        'banner_progress_info' => 'Your application is in progress. Upload the remaining documents to move to the verification stage.',
        'progress_percentage' => '40',
    ];

    $payments = [
        'applicant_balance' => '2000',
        'payment_notification_text' => 'You have been approved for admission.',
    ];
?>

<Hello, class="dashboard-container">
    <!-- Hello, this is the DASHBOARD. -->

    <div class="profile-banner">
        <div class="top-most-span">
            <span>AKADEMOS · SY 2025–2026</span>
        </div>
        
        <div class="banner-name-header">
            <!-- FRONTEND NOTE: $applicantDetails['firstname'] contains the applicant's first name. Backend should populate dynamically -->
            <h1>Hello, <span><?= $firstname ?></span></h1>
        </div>

        <div class="banner-header-info">
            <!-- FRONTEND NOTE: $bannerProgress['banner_progress_info'] contains applicant progress info. --> 
            <p><?= $bannerProgress['banner_progress_info'] ?></p>
        </div>

        <div class="lower-profile-banner">
            <div class="profile-banner-applicant-id-box">
                <span>Applicant ID</span>
                <!-- FRONTEND NOTE: $applicantDetails['applicant_id'] contains the applicant_id. -->
                <p>#<?= $applicant_number ?></p>
            </div>

            <div class="quick-actions">
                <a href="" class="upload-documents-btn">Upload Documents</a>
                <a href="" class="pay-reservation-fee-btn">Pay Reservation Fee</a>
            </div>
        </div>
    </div>

    <div class="application-progress">
        <div class="app-progress-header">
            <h2>Overall Application Progress</h2>
            <!-- FRONTEND NOTE: $bannerProgress['progress_percentage'] contains overall completion percentage. -->
            <span><?= $bannerProgress['progress_percentage'] ?>%</span>
        </div>

        <div class="steps-container">
            <!-- step 1 : ACCOUNT -->
            <div class="step-box">
                <div class="step-bar-container">
                    <div class="step-bar"></div>
                </div>

                <div class="step-bottom">
                    <div class="step-ring">
                        <div class="step-line left-line"></div>
                        <div class="step-circle">1</div>
                        <div class="step-line"></div>
                    </div>

                    <span>Account</span>
                </div>
            </div>

            <!-- step 2 : FORM -->
            <div class="step-box">
                <div class="step-bar-container">
                    <div class="step-bar"></div>
                </div>

                <div class="step-bottom">
                    <div class="step-ring">
                        <div class="step-line"></div>
                        <div class="step-circle">2</div>
                        <div class="step-line"></div>
                    </div>

                    <span>Form</span>
                </div>
            </div>

            <!-- step 3 : DOCUMENTS -->
            <div class="step-box">
                <div class="step-bar-container">
                    <div class="step-bar"></div>
                </div>

                <div class="step-bottom">
                    <div class="step-ring">
                        <div class="step-line"></div>
                        <div class="step-circle">3</div>
                        <div class="step-line"></div>
                    </div>

                    <span>Documents</span>
                </div>
            </div>

            <!-- step 4 : VERIFIED -->
            <div class="step-box">
                <div class="step-bar-container">
                    <div class="step-bar"></div>
                </div>

                <div class="step-bottom">
                    <div class="step-ring">
                        <div class="step-line"></div>
                        <div class="step-circle">4</div>
                        <div class="step-line"></div>
                    </div>

                    <span>Verified</span>
                </div>
            </div>

            <!-- step 5 : ENROLLED -->
            <div class="step-box">
                <div class="step-bar-container">
                    <div class="step-bar"></div>
                </div>

                <div class="step-bottom">
                    <div class="step-ring">
                        <div class="step-line"></div>
                        <div class="step-circle">5</div>
                        <div class="step-line right-line"></div>
                    </div>

                    <span>Enrolled</span>
                </div>
            </div>
        </div>
    </div>

    <div class="checklist-summary-container">
        <div class="dashboard-checklist-summary">
            <div class="top-dashboard-container">
                <div class="top-dashboard-header">
                    <h4>Applicant Form</h4>
                    <p>4 sections to complete</p>
                </div>

                <div class="dashboard-checklist-row-container">
                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Application Type & Program</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Personal Information</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Family Background</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Educational Background</p>
                    </div>
                </div>
            </div>

            <div class="bottom-dashboard-container">
                <div class="checklist-completed-container">
                    <span>2 of 4 completed</span>
                </div>

                <div class="continue-dashboard-btn-container">
                    <a href="<?= base_url('/applicant-form/step/1') ?>" class="continue-dashboard-btn">Continue</a>
                </div>
            </div>
        </div>

        <div class="document-checklist-summary">
            <div class="top-dashboard-container">
                <div class="top-dashboard-header">
                    <h4>Documents</h4>
                    <p>3 of 10 required uploaded</p>
                </div>

                <div class="documents-progress-container">
                    <div class="documents-progress-bar">
                        <div class="documents-current-progress"></div>
                    </div>

                    <div class="documents-progress-legend-container">
                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle"></div>
                            <p>Uploaded <span>3</span></p>
                        </div>

                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle"></div>
                            <p>Missing <span>7</span></p>
                        </div>

                        <div class="document-progress-legend">
                            <div class="documents-progress-legend-circle"></div>
                            <p>Optional <span>1</span></p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-checklist-row-container">
                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>PSA Birth Certificate</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Form 138 / Report Card</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>Certificate of Good Moral</p>
                    </div>

                    <div class="dashboard-checklist-row">
                        <div class="dashboard-checklist-box"></div>
                        <p>2x2 Picture</p>
                    </div>
                    <div class="dashboard-checklist-row">
                        <span>+6 more documents…</span>
                    </div>
                </div>
            </div>

            <div class="bottom-dashboard-container">
                <div class="checklist-completed-container">
                    <span>30% Completed</span>
                </div>

                <div class="continue-dashboard-btn-container">
                    <a href="" class="continue-dashboard-btn">Upload</a>
                </div>
            </div>
        </div>
    </div>

    <div class="payment-notification-container">
        <div class="payment-notification-text-info">
            <!-- FRONTEND NOTE: $payments['payment_notification_text'] contains approval message. -->
            <p><?= $payments['payment_notification_text'] ?></p>
            <!-- FRONTEND NOTE: $payments['applicant_balance'] contains the reservation fee amount. -->
            <span>Pay the ₱<?= $payments['applicant_balance'] ?> reservation fee within 5 days to officially secure your slot.</span>
        </div>

        <a href="" class="pay-now-btn">Pay Now</a>
    </div>
</div>