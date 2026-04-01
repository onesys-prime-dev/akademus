<?php
$counts = $counts ?? [
    'all'    => 0,
    'open'   => 0,
    'closed' => 0,
];
?>

<div class="top-controls">
    <div class="top-controls_left">
        <div class="top-controls_filters">
            <button type="button" data-filter="all" class="<?= (($filters['status'] ?? 'all') === 'all') ? 'filter-active' : '' ?>">
                All
                <span class="filters-button_count"><?= esc($counts['all'] ?? 0) ?></span>
            </button>

            <button type="button" data-filter="open" class="<?= (($filters['status'] ?? '') === 'open') ? 'filter-active' : '' ?>">
                Open
                <span class="filters-button_count"><?= esc($counts['open'] ?? 0) ?></span>
            </button>

            <button type="button" data-filter="closed" class="<?= (($filters['status'] ?? '') === 'closed') ? 'filter-active' : '' ?>">
                Closed
                <span class="filters-button_count"><?= esc($counts['closed'] ?? 0) ?></span>
            </button>
        </div>
    </div>

    <div class="top-controls_right">
        <div class="top-controls_search">
            <img src="<?= base_url('assets/images/icons/search.svg') ?>" alt="">
            <input type="text" placeholder="Search..." data-search value="<?= esc($filters['search'] ?? '') ?>">
        </div>

        <button type="button" class="exam-schedule__list--add btn-primary" data-open-schedule-modal="create">
            <img src="<?= base_url('assets/images/icons/plus.svg') ?>" alt="">
            Add Schedule
        </button>
    </div>
</div>