<div class="exam-schedule__date-picker" data-picker>
    <button type="button" class="exam-schedule__data-picker-trigger btn-primary" data-picker-trigger>
        <div class="exam-schedule__date--icon">
            <img src="<?= base_url('assets/images/icons/calendar.svg') ?>" alt="">
        </div>

        <span class="exam-schedule__date-label" data-label>
            Select Month
        </span>
    </button>

    <div class="exam-schedule__dropdown" data-dropdown hidden>
        <div class="exam-schedule__dropdown-content">
            <select data-month></select>
            <select data-year></select>
        </div>

        <button type="button" class="exam-schedule__apply" data-apply>
            Apply
        </button>
    </div>
</div>