<!-- dashboard/pages/applicant_form.php  -->

<div class="applicant-form">

    <?= $this->include('applicant/applicant-form/components/progress') ?>

    <section class="applicant-form__content card">

        <div id="applicant-form-body" class="applicant-form__body"></div>

        <?= $this->include('applicant/applicant-form/components/actions') ?>

    </section>

</div>

<script>
window.applicantFormConfig = <?= json_encode([
    "initialStep"=>$initial_step ?? 1,
    "maxSteps"=>$max_steps ?? 4,
    "baseUrl"=>base_url(),
    "stepUrl"=>base_url('applicant-form/step/'),
    "updateUrl"=>base_url('applicant-form/update/'),
    "programsUrl"=>base_url('get-programs'),
    "dashboardUrl"=>base_url('dashboard')
]) ?>;
</script>

<script src="<?= base_url('assets/js/pages/applicant-form.js') ?>"></script>