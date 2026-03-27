<div class="exam-schedule__toggle-view card">
    <button
        type="button"
        class="exam-schedule__toggle-item--calendar <?= ($activeView ?? 'calendar') === 'calendar' ? 'toggle--item--active' : '' ?>"
        data-view="calendar"
    >
        Calendar
    </button>

    <button
        type="button"
        class="exam-schedule__toggle-item--list <?= ($activeView ?? 'calendar') === 'list' ? 'toggle--item--active' : '' ?>"
        data-view="list"
    >
        List
    </button>
</div>