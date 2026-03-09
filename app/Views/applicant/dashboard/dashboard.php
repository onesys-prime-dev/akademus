<!-- dashboard/pages/dashboard.php -->

<?php 
$applicant_number = session()->get('applicant_number');
?>

<div class="dashboard">

    <?= $this->include('applicant/dashboard/components/banner') ?>

    <?= $this->include('applicant/dashboard/components/progress') ?>

    <div class="checklist-summary-container">
        <?= $this->include('applicant/dashboard/components/checklist-form') ?>

        <?= $this->include('applicant/dashboard/components/checklist-documents') ?>
    </div>

    <?= $this->include('applicant/dashboard/components/payment-notification') ?>

</div>