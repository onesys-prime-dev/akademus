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
            <div class="steps-bar-container">
                <div id="account-step-bar" class="steps-bar"></div>
                <div id="form-step-bar" class="steps-bar"></div>
                <div id="documents-step-bar" class="steps-bar"></div>
                <div id="verified-step-bar" class="steps-bar"></div>
                <div id="enrolled-step-bar"class="steps-bar"></div>
            </div>

            <div class="steps-label-row-container">
                <!-- step 1 : ACCOUNT -->
                <div class="step-label">
                    <div class="step-circle">
                        <span>1</span>
                    </div>
                    
                    <p>Account</p>
                </div>

                <div class="steps-line-separator"></div>

                <!-- step 2 : FORM -->
                <div class="step-label">
                    <div class="step-circle">
                        <span>2</span>
                    </div>
                    
                    <p>Form</p>
                </div>

                <div class="steps-line-separator"></div>

                <!-- step 3 : DOCUMENTS -->
                <div class="step-label">
                    <div class="step-circle">
                        <span>3</span>
                    </div>
                    
                    <p>Documents</p>
                </div>

                <div class="steps-line-separator"></div>

                <!-- step 4 : VERIFIED -->
                <div class="step-label">
                    <div class="step-circle">
                        <span>4</span>
                    </div>
                    
                    <p>Verified</p>
                </div>

                <div class="steps-line-separator"></div>

                <!-- step 5 : ENROLLED -->
                <div class="step-label">
                    <div class="step-circle">
                        <span>5</span>
                    </div>
                    
                    <p>Enrolled</p>
                </div>
            </div>
        </div>
    </div>

</div>