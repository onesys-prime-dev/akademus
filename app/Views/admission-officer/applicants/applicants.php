<div class="applicants card">
    <div class="applicants__top-actions">
        <div class="applicants__top-actions--back">
            <a href="<?= base_url('admission-officer/dashboard') ?>" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit;">
                <img src="<?= base_url('assets/images/icons/less-than.svg') ?>" alt="">
                <span>Applicant Profile</span>
            </a>
        </div>

        <div class="applicants__top-actions--trash">
            <img src="<?= base_url('assets/images/icons/trash.svg') ?>" alt="">
        </div>
    </div>

    <?= $this->include('admission-officer/applicants/components/applicant-profile') ?>

    <div class="applicants__application" id="applicants-application">
        <?= $this->include('admission-officer/applicants/components/navigation-tabs') ?>
        <?= $this->include('admission-officer/applicants/components/application-content') ?>
    </div>
</div>

<script>
window.applicantsConfig = <?= json_encode([
    'baseUrl'    => base_url(),
    'applicantId'=> $applicantId,
    'activeTab'  => $activeTab ?? 'requirements',
]) ?>;
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/applicants/main.js') ?>?v=<?= time() ?>"></script>