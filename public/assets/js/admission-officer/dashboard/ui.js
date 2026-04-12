// ui.js — All DOM reads, writes, and rendering

const { baseUrl } = window.dashboardConfig || {};

const CLASSES = {
  dropdownOpen:  'search-dropdown--open',
  dropdownLoading: 'search-dropdown--loading',
  itemActive:    'search-dropdown__item--active',
  noResults:     'search-dropdown__empty',
};

// ── Selectors ─────────────────────────────────────────────────────────────────

export function getSearchInput() {
  return document.querySelector('[data-search]');
}

export function getDropdown() {
  return document.querySelector('.search-dropdown');
}

export function getSearchWrapper() {
  return document.querySelector('.top-controls_search');
}

// ── Visibility ─────────────────────────────────────────────────────────────────

export function openDropdown() {
  const dropdown = getDropdown();
  if (dropdown) dropdown.classList.add(CLASSES.dropdownOpen);
}

export function closeDropdown() {
  const dropdown = getDropdown();
  if (dropdown) dropdown.classList.remove(CLASSES.dropdownOpen);
}

export function setLoadingState(isLoading) {
  const dropdown = getDropdown();
  if (!dropdown) return;
  dropdown.classList.toggle(CLASSES.dropdownLoading, isLoading);
}

// ── Rendering ──────────────────────────────────────────────────────────────────

/**
 * Clears and re-renders the dropdown with grouped search results.
 * @param {{ applicants: Array }} results
 * @param {string} query - Highlighted query term.
 */
export function renderResults(results, query) {
  const dropdown = getDropdown();
  if (!dropdown) return;

  dropdown.innerHTML = '';

  const { applicants = [] } = results;

  if (!applicants.length) {
    dropdown.appendChild(buildEmptyState(query));
    return;
  }

  const group = buildGroup('Applicants', applicants, query);
  dropdown.appendChild(group);
}

export function renderError(message) {
  const dropdown = getDropdown();
  if (!dropdown) return;
  dropdown.innerHTML = '';
  dropdown.appendChild(buildErrorState(message));
}

// ── Builders ───────────────────────────────────────────────────────────────────

function buildGroup(title, items, query) {
  const group = document.createElement('div');
  group.className = 'search-dropdown__group';

  const groupTitle = document.createElement('div');
  groupTitle.className = 'search-dropdown__group-title';
  groupTitle.textContent = title;
  group.appendChild(groupTitle);

  items.forEach(item => group.appendChild(buildItem(item, query)));

  return group;
}

/**
 * @param {{ name: string, program: string, status: string, applicant_id: string, url?: string }} item
 * @param {string} query
 */
function buildItem(item, query) {
  const el = document.createElement('div');
  el.className = 'search-dropdown__item';
  el.setAttribute('role', 'option');
  el.setAttribute('tabindex', '-1');

  el.dataset.id = item.applicant_id;

  if (item.url) {
    el.dataset.url = item.url;
    el.style.cursor = 'pointer';
  }

  el.innerHTML = `
    <span class="search-dropdown__item--name">${highlight(item.applicant_name, query)}</span>
    <div class="search-dropdown__item--details">
      <span class="search-dropdown__item--program">${escapeHtml(item.program)}</span>
      <div class="dot-separator"></div>
      <span class="search-dropdown__item--status">${escapeHtml(item.status)}</span>
      <div class="dot-separator"></div>
      <span class="search-dropdown__item--applicant-id">#${escapeHtml(item.applicant_number)}</span>
    </div>
  `;

  return el;
}

function buildEmptyState(query) {
  const el = document.createElement('div');
  el.className = 'search-dropdown__empty';
  el.textContent = `No results for "${escapeHtml(query)}"`;
  return el;
}

function buildErrorState(message) {
  const el = document.createElement('div');
  el.className = 'search-dropdown__error';
  el.textContent = message || 'Something went wrong. Please try again.';
  return el;
}

// ── Keyboard Navigation ────────────────────────────────────────────────────────

/**
 * Moves focus to the next/previous item in the dropdown.
 * @param {'next'|'prev'} direction
 */
export function moveFocus(direction) {
  const dropdown = getDropdown();
  if (!dropdown) return;

  const items = [...dropdown.querySelectorAll('.search-dropdown__item')];
  if (!items.length) return;

  const active = dropdown.querySelector(`.${CLASSES.itemActive}`);
  const currentIndex = active ? items.indexOf(active) : -1;

  let nextIndex;
  if (direction === 'next') {
    nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
  } else {
    nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
  }

  items.forEach(item => item.classList.remove(CLASSES.itemActive));
  items[nextIndex].classList.add(CLASSES.itemActive);
  items[nextIndex].focus();
}

/**
 * Returns the currently active (keyboard-focused) item element.
 */
export function getActiveItem() {
  const dropdown = getDropdown();
  return dropdown ? dropdown.querySelector(`.${CLASSES.itemActive}`) : null;
}

// ── Utilities ──────────────────────────────────────────────────────────────────

function escapeHtml(str) {
  const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return String(str).replace(/[&<>"']/g, ch => map[ch]);
}

/**
 * Wraps matched query characters in a <mark> tag for highlighting.
 */
function highlight(text, query) {
  if (!query) return escapeHtml(text);
  const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const regex = new RegExp(`(${escaped})`, 'gi');
  return escapeHtml(text).replace(regex, '<mark>$1</mark>');
}


// ===============================
// DASHBOARD RENDERING
// ===============================

export function renderDashboard(data) {
  if (!data) return;

  const hasData = (data.statusCounts?.all || 0) > 0;

  updateKeyMetrics(data.statusCounts);
  updateApplicantsPerProgram(data.perProgram);
  updateApplicantsPerStatus(data.statusCounts);
  updatePassingRate(data.passingRate, hasData);
  updateTable(data.rows);
}

function updateKeyMetrics(counts = {}) {
  const values = document.querySelectorAll('.metric__details--value');
  if (!values.length) return;

  values[0].textContent = format(counts.all || 0);
  values[1].textContent = format(counts.pending || 0);
  values[2].textContent = format(counts.in_progress || 0);
  values[3].textContent = format((counts.approved || 0) + (counts.completed || 0));
}

function updateApplicantsPerProgram(perProgram = []) {
  const body = document.querySelector('.applicants-per-program__body');
  const svg = document.querySelector('.applicants-per-program__svg');

  if (!body || !svg) return;

  // =========================
  // EMPTY STATE
  // =========================
  let emptyEl = body.querySelector('.applicants-empty');

  if (!perProgram.length) {
    svg.style.display = 'none';

    if (!emptyEl) {
      emptyEl = document.createElement('p');
      emptyEl.className = 'applicants-empty';
      emptyEl.textContent = 'No applicant data yet.';
      body.appendChild(emptyEl);
    }

    return;
  }

  if (emptyEl) emptyEl.remove();
  svg.style.display = '';

  // =========================
  // CONFIG (MATCH PHP)
  // =========================
  const colors = ['#3b82f6','#8b5cf6','#f59e0b','#ef4444','#10b981','#f97316','#06b6d4','#ec4899'];

  const barGap = 46;
  const barH = 28;
  const labelX = 100;
  const barX = 120;
  const maxBarW = 700;

  const safeData = perProgram.map(p => ({
    ...p,
    total: Number(p.total) || 0
  }));

  const maxVal = Math.max(...safeData.map(p => p.total), 1);

  // dynamic height
  const svgHeight = Math.max(80, safeData.length * barGap + 20);
  svg.setAttribute('viewBox', `0 0 900 ${svgHeight}`);

  // =========================
  // RENDER
  // =========================
  let content = '';

  safeData.forEach((prog, i) => {
    const y = 10 + i * barGap;
    const value = prog.total;

    const barW = value > 0
      ? Math.max(6, Math.round((value / maxVal) * maxBarW))
      : 4;

    const color = colors[i % colors.length];
    const midY = y + barH / 2 + 4;

    const rawLabel = prog.program_code || prog.program_name || `Program ${i + 1}`;

    // truncate (match PHP)
    const label = escapeHtml(
      rawLabel.length > 18 ? rawLabel.slice(0, 18) + '...' : rawLabel
    );

    const valueX = Math.min(barX + barW + 8, 880);

    content += `
      <!-- Label -->
      <text x="${labelX}" y="${midY}"
            text-anchor="end"
            class="bar-label">
        ${label}
      </text>

      <!-- Background -->
      <rect x="${barX}" y="${y}"
            width="${maxBarW}" height="${barH}"
            rx="6"
            class="bar-bg"></rect>

      <!-- Fill -->
      <rect x="${barX}" y="${y}"
            width="${barW}" height="${barH}"
            rx="6"
            fill="${color}"
            class="bar-fill">
        <title>${escapeHtml(rawLabel)}: ${format(value)}</title>
      </rect>

      <!-- Value -->
      <text x="${valueX}" y="${midY}"
            class="bar-value">
        ${format(value)}
      </text>
    `;
  });

  svg.innerHTML = content;
}

function updateApplicantsPerStatus(counts = {}) {
  const body = document.querySelector('.applicants-per-status__body');
  const svg = document.querySelector('.applicants-per-status__svg');
  const legends = document.querySelector('.applicants-per-status__legends');

  if (!body || !svg || !legends) return;

  const values = [
    Number(counts.in_progress) || 0,
    Number(counts.pending) || 0,
    (Number(counts.approved) || 0) + (Number(counts.completed) || 0),
    Number(counts.rejected) || 0
  ];

  const total = values.reduce((a, b) => a + b, 0);

  // =========================
  // EMPTY STATE (MATCH PROGRAM)
  // =========================
  let emptyEl = body.querySelector('.applicants-empty');

  if (total === 0) {
    svg.style.display = 'none';
    legends.style.display = 'none';

    if (!emptyEl) {
      emptyEl = document.createElement('p');
      emptyEl.className = 'applicants-empty';
      emptyEl.textContent = 'No applicant data yet.';
      body.appendChild(emptyEl);
    }

    return;
  }

  // =========================
  // RESTORE STRUCTURE
  // =========================
  if (emptyEl) emptyEl.remove();

  svg.style.display = '';
  legends.style.display = '';

  // =========================
  // UPDATE CHART
  // =========================
  const chartTop = 20;
  const chartBottom = 260;
  const chartHeight = chartBottom - chartTop;

  const max = Math.max(...values, 1);

  const rects = svg.querySelectorAll('.applicants-per-status__bars rect');
  const barsGroup = svg.querySelector('.applicants-per-status__bars');

  rects.forEach((rect, i) => {
    const value = values[i];
    const height = value > 0 ? (value / max) * chartHeight : 0;
    const y = chartBottom - height;

    rect.setAttribute('y', y);
    rect.setAttribute('height', height);
  });

  // =========================
  // SAFE LABEL REBUILD
  // =========================
  if (barsGroup) {
    barsGroup.querySelectorAll('text').forEach(el => el.remove());

    rects.forEach((rect, i) => {
      const value = values[i];
      if (!value) return;

      const x = parseFloat(rect.getAttribute('x')) + 70;
      const y = parseFloat(rect.getAttribute('y')) - 6;

      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', x);
      text.setAttribute('y', y);
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('fill', '#555');
      text.setAttribute('font-size', '13');
      text.textContent = format(value);

      barsGroup.appendChild(text);
    });
  }
}

function updatePassingRate(rate = 0, hasData = true) {
  const body = document.querySelector('.passing-rate__body');
  const chart = document.querySelector('.passing-rate__chart');
  const pie = document.querySelector('.passing-rate__pie');
  const text = document.querySelector('.passing-rate__center span');
  const legends = document.querySelector('.passing-rate__legends');

  if (!body || !chart || !pie || !text || !legends) return;

  let emptyEl = body.querySelector('.applicants-empty');

  // =========================
  // EMPTY STATE
  // =========================
  if (!hasData) {
    chart.style.display = 'none';
    legends.style.display = 'none';

    if (!emptyEl) {
      emptyEl = document.createElement('p');
      emptyEl.className = 'applicants-empty';
      emptyEl.textContent = 'No applicant data yet.';
      body.appendChild(emptyEl);
    }

    return;
  }

  // =========================
  // RESTORE UI
  // =========================
  if (emptyEl) emptyEl.remove();

  chart.style.display = '';
  legends.style.display = '';

  // =========================
  // UPDATE CHART
  // =========================
  const safeRate = Number(rate) || 0;

  pie.style.background = `conic-gradient(var(--primary) 0% ${safeRate}%, #e2e8f0 ${safeRate}% 100%)`;
  text.textContent = `${safeRate}%`;

  const legendTitles = document.querySelectorAll('.passing-rate__legend--title');

  if (legendTitles.length >= 2) {
    legendTitles[0].textContent = `Passed (${safeRate}%)`;
    legendTitles[1].textContent = `Failed (${100 - safeRate}%)`;
  }
}

function updateTable(rows = []) {
  const tbody = document.querySelector('#recent-applicants-table-body');
  if (!tbody) return;

  if (!rows.length) {
    tbody.innerHTML = `
      <tr class="recent-applicants-table__row">
        <td class="recent-applicants-table__cell" colspan="8" style="text-align:center;color:#718096;padding:24px 0;">
          No applicants found.
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = rows.map(row => {
    const status = row.status || '';

    return `
      <tr class="recent-applicants-table__row" data-row data-id="${row.applicant_id}">
        <td class="recent-applicants-table__cell">${escape(row.applicant_name)}</td>
        <td class="recent-applicants-table__cell">${escape(row.email)}</td>
        <td class="recent-applicants-table__cell">${escape(row.applicant_number)}</td>
        <td class="recent-applicants-table__cell">${escape(row.applicant_type)}</td>
        <td class="recent-applicants-table__cell">${escape(row.program)}</td>
        <td class="recent-applicants-table__cell">
          <span class="status-badge status-badge--${status}">
            ${escape(status.replace('_',' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase()))}
          </span>
        </td>
        <td class="recent-applicants-table__cell">
          ${formatDate(row.date_added)}
        </td>
        <td class="recent-applicants-table__cell recent-applicants-table__cell--actions">
          <a href="${baseUrl}/admission-officer/detailed-applicant/${row.applicant_id}">
            <img class="recent-applicants-table__cell--actions-img" src="${baseUrl}/assets/images/icons/view.svg">
          </a>
        </td>
      </tr>
    `;
  }).join('');
}

function format(num) {
  return new Intl.NumberFormat().format(num);
}

function formatDate(date) {
  const d = new Date(date);

  if (isNaN(d)) return ''; // safety

  return d.toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric'
  });
}

function escape(str = '') {
  return str.replace(/[&<>"']/g, m => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[m]));
}

