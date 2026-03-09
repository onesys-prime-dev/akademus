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
        <p>Your application is in progress. Upload the remaining documents to move to the verification stage.</p>
    </div>

    <div class="lower-profile-banner">
        <div class="profile-banner-applicant-id-box">
            <span>Applicant ID</span>
            <!-- FRONTEND NOTE: $applicantDetails['applicant_id'] contains the applicant_id. -->
            <p>#<?= $applicant_number ?></p>
        </div>

        <div class="quick-actions">
            <a href="" class="upload-documents-btn btn-accent">Upload Documents</a>
            <a href="" class="pay-reservation-fee-btn btn-accent">Pay Reservation Fee</a>
        </div>
    </div>
</div>