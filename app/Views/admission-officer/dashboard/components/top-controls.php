<div class="top-controls">
    <div class="top-controls_left">
        <div class="top-controls_search">
            <img src="<?= base_url('assets/images/icons/search.svg') ?>" alt="">

            <input type="text" placeholder="Search..." data-search>
        </div>

        <div class="top-controls_filters">
            <button data-filter="all" class="filter-active">
                All
                <span class="filters-button_count">0</span>
            </button>
            <button data-filter="in_progress">
                In-Progress
                <span class="filters-button_count">0</span>
            </button>
            <button data-filter="pending">
                Pending
                <span class="filters-button_count">0</span>
            </button>
            <button data-filter="approved">
                Approved
                <span class="filters-button_count">0</span>
            </button>
            <button data-filter="rejected">
                Rejected
                <span class="filters-button_count">0</span>
            </button>
        </div>
    </div>

    <div class="top-controls_right">
        <?= $this->include('admission-officer/components/program-dropdown') ?>

        <button class="btn-primary">
            <img src="<?= base_url('assets/images/icons/plus.svg') ?>" alt="">
             Add Applicant
        </button>
    </div>
</div>