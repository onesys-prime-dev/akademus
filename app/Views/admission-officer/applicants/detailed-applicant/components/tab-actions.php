<div class="top-controls">
    <div class="top-controls_filters">
        <button data-filter="all" class="filter-active">
            All
            <span class="filters-button_count"><?= esc($counts['all'] ?? 0) ?></span>
        </button>

        <button data-filter="in_progress">
            In-Progress
            <span class="filters-button_count"><?= esc($counts['in_progress'] ?? 0) ?></span>
        </button>

        <button data-filter="pending">
            Pending
            <span class="filters-button_count"><?= esc($counts['pending'] ?? 0) ?></span>
        </button>

        <button data-filter="approved">
            Approved
            <span class="filters-button_count"><?= esc($counts['approved'] ?? 0) ?></span>
        </button>

        <button data-filter="rejected">
            Rejected
            <span class="filters-button_count"><?= esc($counts['rejected'] ?? 0) ?></span>
        </button>
    </div>

    <div class="top-controls_search">
        <img src="<?= base_url('assets/images/icons/search.svg') ?>" alt="">
        <input type="text" placeholder="Search..." data-search value="<?= esc($filters['search'] ?? '') ?>">
    </div>
</div>