import { state } from './state.js';

const MONTHS = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];

export function getMonthName(month) {
    return MONTHS[month - 1] || '';
}

export function getPrevMonth(month, year) {
    if (month === 1) return { month: 12, year: year - 1 };
    return { month: month - 1, year };
}

export function getNextMonth(month, year) {
    if (month === 12) return { month: 1, year: year + 1 };
    return { month: month + 1, year };
}

export function populateDatePicker() {
    const monthSelect = document.querySelector('[data-month]');
    const yearSelect = document.querySelector('[data-year]');

    if (!monthSelect || !yearSelect) return;

    monthSelect.innerHTML = MONTHS.map((month, index) => {
        const value = String(index + 1);
        return `<option value="${value}">${month}</option>`;
    }).join('');

    yearSelect.innerHTML = '';

    const currentYear = new Date().getFullYear();
    for (let year = currentYear - 5; year <= currentYear + 5; year++) {
        const option = document.createElement('option');
        option.value = String(year);
        option.textContent = String(year);
        yearSelect.appendChild(option);
    }
}

export function syncDatePicker() {
    const label = document.querySelector('[data-label]');
    const monthSelect = document.querySelector('[data-month]');
    const yearSelect = document.querySelector('[data-year]');

    if (label) label.textContent = `${getMonthName(state.month)} ${state.year}`;
    if (monthSelect) monthSelect.value = String(state.month);
    if (yearSelect) yearSelect.value = String(state.year);
}

export function syncMonthCard() {
    const prevLabel = document.querySelector('[data-month-prev-label]');
    const currentLabel = document.querySelector('[data-month-current]');
    const nextLabel = document.querySelector('[data-month-next-label]');

    const prev = getPrevMonth(state.month, state.year);
    const next = getNextMonth(state.month, state.year);

    if (prevLabel) prevLabel.textContent = getMonthName(prev.month);
    if (currentLabel) currentLabel.textContent = `${getMonthName(state.month)} ${state.year}`;
    if (nextLabel) nextLabel.textContent = getMonthName(next.month);
}

export function syncToggleView() {
    document.querySelectorAll('[data-view]').forEach((btn) => {
        btn.classList.toggle('toggle--item--active', btn.dataset.view === state.view);
    });
}

export function toggleDropdown(show) {
    const dropdown = document.querySelector('[data-dropdown]');
    if (!dropdown) return;
    dropdown.hidden = !show;
}

export function syncContent(html) {
    const container = document.querySelector('[data-content-container]');
    if (!container) return;
    container.innerHTML = html;
}

export function setLoading(loading) {
    const loader = document.getElementById('exam-schedule-table-loading');
    if (!loader) return;
    loader.hidden = !loading;
}

export function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.hidden = false;
}

export function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.hidden = true;
}

export function renderScheduleDetails(data) {
    const schedule = data?.schedule;
    const applicants = data?.applicants || [];

    if (!schedule) return;

    const setText = (key, value) => {
        const el = document.querySelector(`[data-detail="${key}"]`);
        if (el) el.textContent = value ?? '-';
    };

    setText('date', schedule.exam_date ?? '-');
    setText('time', schedule.time ?? `${schedule.start_time ?? '-'} - ${schedule.end_time ?? '-'}`);
    setText('location', schedule.location ?? '-');
    setText('slots', `${schedule.booked_slots ?? 0} / ${schedule.max_slots ?? 0} (${schedule.available_slots ?? 0} available)`);
    setText('status', schedule.status ?? '-');
    setText('created_at', schedule.created_at ?? '-');

    const tbody = document.querySelector('[data-applicants-body]');
    if (!tbody) return;

    if (!applicants.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center;">No applicants booked yet</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = applicants.map((applicant) => {
        const checkedIn = applicant.checked_in_at
            ? applicant.checked_in_at
            : 'Not yet';

        return `
            <tr>
                <td>${escapeHtml(applicant.applicant ?? '-')}</td>
                <td>${escapeHtml(applicant.contact ?? '-')}</td>
                <td>${escapeHtml(applicant.status ?? '-')}</td>
                <td>${escapeHtml(applicant.booked_at ?? '-')}</td>
                <td>${escapeHtml(checkedIn)}</td>
                <td>
                    ${!applicant.checked_in_at ? `<button type="button" data-check-in="${applicant.booking_id}">Check-in</button>` : ''}
                    ${applicant.status !== 'cancelled' ? `<button type="button" data-cancel-booking="${applicant.booking_id}">Cancel</button>` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}