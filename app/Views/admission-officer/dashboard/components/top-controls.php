<div class="top-controls">
    <div class="top-controls_left">
        <div class="top-controls_search">
            <img src="<?= base_url('assets/images/icons/search.svg') ?>" alt="">

            <input class="search--dashboard" type="text" placeholder="Search..." data-search>

            <?= $this->include('admission-officer/dashboard/components/search-dropdown') ?>
        </div>

        <?= $this->include('admission-officer/components/program-dropdown') ?>
    </div>

    <div class="top-controls_right">
        <!--

        <?//= $this->include('admission-officer/dashboard/components/academic-year-dropdown') ?>
        <?//= $this->include('admission-officer/dashboard/components/semester-dropdown') ?>
        -->
    </div>
</div>