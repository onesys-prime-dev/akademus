<!-- dashboard/menu/applicant_form.php  -->
<!-- EXAMPLE VARIABLES TO BE USED. DELETE IF BACKEND IS WORKING -->
<?php
    $firstname = "Juan";
    $applicant_id = 'APP-2025-08741';
    $applicant_initials = 'JD'; 
?>

<Hello, class="applicant-form-container">
    <!-- Hello, this is the APPLICANT FORM. -->

    <div class="profile-banner">
        <div class="top-most-span">
            <span>AKADEMOS · SY 2025–2026</span>
        </div>

        <div class="banner-name-header">
            <h1>Hello, <span><?= $firstname ?></span></h1>
        </div>

        <div class="banner-header-info">
            <p>Your application is in progress. Upload the remaining documents to move to the verification stage.</p>
        </div>

        <div class="lower-profile-banner">
            <div class="profile-banner-applicant-id-box">
                <span>Applicant ID</span>
                <p>#<?= $applicant_id ?></p>
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
            <span>50%</span>
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
        <div class="applicant-form-checklist-summary">
            <div class="top-applicant-form-container">
                <div class="top-applicant-form-header">
                    <h4>Applicant Form</h4>
                    <p>4 sections to complete</p>
                </div>

                <div class="applicant-form-checklist-row-container">
                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Application Type & Program</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Personal Information</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Family Background</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Educational Background</p>
                    </div>
                </div>
            </div>

            <div class="bottom-applicant-form-container">
                <div class="checklist-completed-container">
                    <span>2 of 4 completed</span>
                </div>

                <div class="continue-applicant-form-btn-container">
                    <a href="" class="continue-applicant-form-btn">Continue</a>
                </div>
            </div>
        </div>

        <div class="document-checklist-summary">
            <div class="top-applicant-form-container">
                <div class="top-applicant-form-header">
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

                <div class="applicant-form-checklist-row-container">
                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>PSA Birth Certificate</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Form 138 / Report Card</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>Certificate of Good Moral</p>
                    </div>

                    <div class="applicant-form-checklist-row">
                        <div class="app-form-checklist-box"></div>
                        <p>2x2 Picture</p>
                    </div>
                    <div class="applicant-form-checklist-row">
                        <span>+6 more documents…</span>
                    </div>
                </div>
            </div>

            <div class="bottom-applicant-form-container">
                <div class="checklist-completed-container">
                    <span>30% Completed</span>
                </div>

                <div class="continue-applicant-form-btn-container">
                    <a href="" class="continue-applicant-form-btn">Upload</a>
                </div>
            </div>
        </div>
    </div>

    <div class="payment-notification-container">
        <div class="payment-notification-text-info">
            <p>You have been approved for admission.</p>
            <span>Pay the ₱2,000 reservation fee within 5 days to officially secure your slot.</span>
        </div>

        <a href="" class="pay-now-btn">Pay Now</a>
    </div>
</div>