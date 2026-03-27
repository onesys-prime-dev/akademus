import { state } from './state.js';
import { fetchExamScheduleContent, fetchScheduleDetails, postForm } from './api.js';
import {
    populateDatePicker,
    syncContent,
    syncDatePicker,
    syncMonthCard,
    syncToggleView,
    toggleDropdown,
    getPrevMonth,
    getNextMonth,
    openModal,
    closeModal,
    renderScheduleDetails,
    setLoading,
} from './ui.js';

let searchTimer = null;

function applyStateToUi() {
    syncDatePicker();
    syncMonthCard();
    syncToggleView();
}

async function refreshContent() {
    setLoading(true);

    try {
        updateUrlState();
        
        const res = await fetchExamScheduleContent(state);
        syncContent(res.html);
        applyStateToUi();
    } finally {
        setLoading(false);
    }
}

function bindDatePicker() {
    const picker = document.querySelector('[data-picker]');
    if (!picker) return;

    const trigger = picker.querySelector('[data-picker-trigger]');
    const applyBtn = picker.querySelector('[data-apply]');
    const monthSelect = picker.querySelector('[data-month]');
    const yearSelect = picker.querySelector('[data-year]');

    populateDatePicker();
    applyStateToUi();

    trigger?.addEventListener('click', (e) => {
        e.stopPropagation();
        const dropdown = picker.querySelector('[data-dropdown]');
        if (!dropdown) return;
        toggleDropdown(dropdown.hidden);
    });

    applyBtn?.addEventListener('click', async () => {
        const month = Number(monthSelect?.value);
        const year = Number(yearSelect?.value);

        if (!month || !year) return;

        state.month = month;
        state.year = year;
        state.day = null;
        state.page = 1;

        toggleDropdown(false);
        applyStateToUi();
        await refreshContent();
    });

    document.addEventListener('click', (e) => {
        if (!picker.contains(e.target)) {
            toggleDropdown(false);
        }
    });
}

function bindToggleView() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-view]');
        if (!btn) return;

        const nextView = btn.dataset.view;
        if (nextView === state.view) return;

        state.view = nextView;
        state.page = 1;

        if (nextView === 'calendar') {
            state.day = null;
        }

        applyStateToUi();
        await refreshContent();
    });
}

function bindMonthCard() {
    document.addEventListener('click', async (e) => {
        const prevBtn = e.target.closest('[data-month-prev], [data-month-prev-btn]');
        const nextBtn = e.target.closest('[data-month-next], [data-month-next-btn]');
        if (!prevBtn && !nextBtn) return;

        const nextState = prevBtn
            ? getPrevMonth(state.month, state.year)
            : getNextMonth(state.month, state.year);

        state.month = nextState.month;
        state.year = nextState.year;
        state.day = null;
        state.page = 1;

        applyStateToUi();
        await refreshContent();
    });
}

function bindContentEvents() {
    document.addEventListener('click', async (e) => {
        const dayCell = e.target.closest('[data-day]');
        if (dayCell) {
            state.day = Number(dayCell.dataset.day);
            state.view = 'list';
            state.page = 1;
            applyStateToUi();
            await refreshContent();
            return;
        }

        const filterBtn = e.target.closest('[data-filter]');
        if (filterBtn) {
            state.status = filterBtn.dataset.filter || 'all';
            state.view = 'list';
            state.day = null;
            state.page = 1;
            applyStateToUi();
            await refreshContent();
            return;
        }

        const pageBtn = e.target.closest('[data-page]');
        if (pageBtn) {
            state.page = Number(pageBtn.dataset.page) || 1;
            await refreshContent();
            return;
        }

        const row = e.target.closest('[data-schedule-id]');
        if (row && !e.target.closest('[data-action]')) {
            const id = row.dataset.scheduleId;
            state.selectedScheduleId = id;

            const details = await fetchScheduleDetails(id);
            renderScheduleDetails(details);
            openModal('schedule-details-modal');
            return;
        }

        const actionBtn = e.target.closest('[data-action]');
        if (actionBtn) {
            e.stopPropagation();

            const id = actionBtn.dataset.id;
            const action = actionBtn.dataset.action;

            if (action === 'view') {
                const details = await fetchScheduleDetails(id);
                renderScheduleDetails(details);
                openModal('schedule-details-modal');
            }

            if (action === 'edit') {
                const details = await fetchScheduleDetails(id);
                openModal('exam-schedule-modal');

                const form = document.getElementById('exam-schedule-form');
                if (form) {
                    form.mode.value = 'edit';
                    form.exam_schedule_id.value = details?.schedule?.exam_schedule_id ?? id;
                    form.exam_date.value = details?.schedule?.exam_date ?? '';
                    form.start_time.value = details?.schedule?.start_time ?? '';
                    form.end_time.value = details?.schedule?.end_time ?? '';
                    form.location.value = details?.schedule?.location ?? '';
                    form.max_slots.value = details?.schedule?.max_slots ?? 1;
                }
            }

            if (action === 'delete') {
                const ok = confirm('Delete this exam schedule?');
                if (!ok) return;

                const res = await postForm(`/admission-officer/exam-schedule/delete/${id}`, new FormData());

                if (res.success) {
                    await refreshContent();
                }
            }
        }

        const checkInBtn = e.target.closest('[data-check-in]');
        if (checkInBtn) {
            const bookingId = checkInBtn.dataset.checkIn;

            await postForm(`/admission-officer/exam-schedule/check-in/${bookingId}`, new FormData());

            const details = await fetchScheduleDetails(state.selectedScheduleId);
            renderScheduleDetails(details);
            await refreshContent();
            return;
        }

        const cancelBtn = e.target.closest('[data-cancel-booking]');
        if (cancelBtn) {
            const bookingId = cancelBtn.dataset.cancelBooking;
            const ok = confirm('Cancel this booking?');
            if (!ok) return;

            await postForm(`/admission-officer/exam-schedule/cancel-booking/${bookingId}`, new FormData());

            const details = await fetchScheduleDetails(state.selectedScheduleId);
            renderScheduleDetails(details);
            await refreshContent();
            return;
        }

        const addBtn = e.target.closest('[data-open-schedule-modal="create"]');
        if (addBtn) {
            const form = document.getElementById('exam-schedule-form');
            if (form) {
                form.reset();
                form.mode.value = 'create';
                form.exam_schedule_id.value = '';
            }

            openModal('exam-schedule-modal');
        }
    });

    const searchInput = document.querySelector('[data-search]');
    if (searchInput) {
        searchInput.value = state.search;

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(async () => {
                state.search = searchInput.value.trim();
                state.view = 'list';
                state.day = null;
                state.page = 1;
                applyStateToUi();
                await refreshContent();
            }, 300);
        });
    }
}

function bindModals() {
    document.addEventListener('click', (e) => {
        const modal = e.target.closest('.modal');
        if (!modal) return;

        if (e.target.matches('.modal')) {
            modal.hidden = true;
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        document.querySelectorAll('.modal').forEach((modal) => {
            modal.hidden = true;
        });
    });
}

function bindScheduleForm() {
    const form = document.getElementById('exam-schedule-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const mode = formData.get('mode');
        const id = formData.get('exam_schedule_id');

        const url =
            mode === 'edit'
                ? `/admission-officer/exam-schedule/update/${id}`
                : '/admission-officer/exam-schedule/create';

        try {
            const res = await postForm(url, formData); // ✅ FIX

            if (res.success) { // ✅ use JSON response
                closeModal('exam-schedule-modal');
                form.reset(); // optional but good
                await refreshContent();
            } else {
                alert('Failed to save schedule');
            }

        } catch (err) {
            console.error(err);
            alert('Something went wrong');
        }
    });
}

function updateUrlState() {
    const url = new URL(window.location.href);

    url.searchParams.set('month', state.month);
    url.searchParams.set('year', state.year);
    url.searchParams.set('view', state.view);

    if (state.day) {
        url.searchParams.set('day', state.day);
    } else {
        url.searchParams.delete('day');
    }

    if (state.search) {
        url.searchParams.set('search', state.search);
    } else {
        url.searchParams.delete('search');
    }

    if (state.status && state.status !== 'all') {
        url.searchParams.set('status', state.status);
    } else {
        url.searchParams.delete('status');
    }

    if (state.page > 1) {
        url.searchParams.set('page', state.page);
    } else {
        url.searchParams.delete('page');
    }

    window.history.replaceState({}, '', url);
}

export function init() {
    bindDatePicker();
    bindToggleView();
    bindMonthCard();
    bindContentEvents();
    bindModals();
    bindScheduleForm();
}