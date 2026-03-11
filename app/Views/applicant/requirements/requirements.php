<!-- dashboard/pages/requirements.php  -->

<div class="requirements">

    <?= $this->include('applicant/requirements/components/progress') ?>

    <section class="requirements__content card">

        <div id="requirements-body" class="requirements__body"></div>

        <?= $this->include('applicant/requirements/components/actions') ?>

    </section>

</div>

<script>
window.requirementsConfig = <?= json_encode([
    "initialStep"=>$initial_step ?? 1,
    "maxSteps"=>$max_steps ?? 5,
    "baseUrl"=>base_url(),
    "stepUrl"=>base_url('requirements/step/'),
    "updateUrl"=>base_url('requirements/update/'),
    "dashboardUrl"=>base_url('dashboard')
]) ?>;
</script>

<script src="<?= base_url('assets/js/pages/requirements.js') . '?v=' . time(); ?>"></script>