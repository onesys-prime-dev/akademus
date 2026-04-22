const { baseUrl } = window.applicantsConfig || {};


// ==============================
// PROFILE UI
// ==============================
export function setActiveTab(tab) {
    document.querySelectorAll('[data-tab]').forEach(el => {
        el.classList.toggle('application-tabs__item--active', el.dataset.tab === tab);
    });
}

export function renderContent(html) {
    const el = document.getElementById('application-content');
    if (el) el.innerHTML = html;
}

export function showError(message) {
    const container = document.getElementById('application-content');
    if (!container) return;
    container.innerHTML = `
        <div style="padding:40px;text-align:center;color:#c00;">
            <p style="font-size:14px;">${message}</p>
        </div>`;
}

export function showContentLoading() {
    const el = document.getElementById('application-content');
    if (!el) return;

    el.innerHTML = `
        <div class="application-content__loading" style="padding:24px;text-align:center;">
            Loading...
        </div>
    `;
}

export function updateTableOnly(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;

    const newTbody = temp.querySelector('#application-table-body');
    const current  = document.querySelector('#application-table-body');

    if (!newTbody || !current) {
        console.warn('Table body not found, fallback to full render');
        return false;
    }

    current.innerHTML = newTbody.innerHTML;
    return true;
}


// ==============================
// LIST UI
// ==============================
export function updateApplicantsTable(applicants) {
    const tbody = document.getElementById('applicants-table-body');
    if (!tbody) return;

    if (!applicants.length) {
        tbody.innerHTML = `
            <tr>
                <td class="applicants-table__cell" colspan="9" style="text-align:center;">No applicants found</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = applicants.map(app => `
        <tr class="applicants-table__row" data-row data-id="${app.applicant_id}">

            <td class="applicants-table__cell applicants-table__cell--checkbox">
                <input type="checkbox" data-select>
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_name)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.email)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_number)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_type || '')}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.program)}
            </td>

            <td class="applicants-table__cell">
                ${renderStatus(app.status)}
            </td>

            <td class="applicants-table__cell">
                ${formatDate(app.date_added)}
            </td>

            <td class="applicants-table__cell applicants-table__cell--actions">
                ${renderActions(app.applicant_id)}
            </td>

        </tr>
    `).join('');
}


// ==============================
// SHARED UI
// ==============================
export function showTableLoading() {
    const tbody =
        document.getElementById('applicants-table-body') ||
        document.getElementById('application-table-body');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td class="applicants-table__cell" colspan="10" style="text-align:center;">Loading...</td>
        </tr>
    `;
}

export function updatePagination({ page, total, limit }) {
    const totalPages = Math.max(1, Math.ceil(total / limit));

    const pageNumber = document.getElementById('page-number');
    const prevBtn = document.querySelector('[data-page="prev"]');
    const nextBtn = document.querySelector('[data-page="next"]');

    if (!pageNumber || !prevBtn || !nextBtn) return;

    const safePage = Math.min(page, totalPages);

    pageNumber.textContent = safePage;
    pageNumber.classList.add('pagination__btn--active-page');

    prevBtn.classList.toggle('pagination__btn--hidden', safePage <= 1);
    nextBtn.classList.toggle('pagination__btn--hidden', safePage >= totalPages);
}

export function updateFilterCounts(counts = {}) {
    document.querySelectorAll('[data-filter]').forEach(btn => {
        const key = btn.dataset.filter;
        const el = btn.querySelector('.filters-button_count');
        if (el) el.textContent = counts[key] ?? 0;
    });
}

export function resetFilters() {
    const input = document.querySelector('[data-search]');
    if (input) input.value = '';

    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('filter-active');
        if (btn.dataset.filter === 'all') {
            btn.classList.add('filter-active');
        }
    });
}

export function resetBulkSelection() {
    const selectAll = document.querySelector('.bulk-actions__btn--select-all input');
    if (selectAll) selectAll.checked = false;
}

function ensureDocModal() {
    if (document.getElementById('ao-doc-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'ao-doc-modal';
    modal.style.cssText = [
        'display:none',
        'position:fixed',
        'top:0', 'left:0', 'right:0', 'bottom:0',
        'z-index:99999',
        'background:rgba(0,0,0,0.65)',
        'align-items:center',
        'justify-content:center',
    ].join(';');

    modal.innerHTML = `
        <div style="background:#fff;border-radius:10px;width:92%;max-width:860px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #eee;flex-shrink:0;">
                <span id="ao-doc-modal-title" style="font-weight:600;font-size:14px;color:#222;max-width:80%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                <button id="ao-doc-modal-close" style="background:none;border:none;cursor:pointer;font-size:26px;color:#666;line-height:1;padding:0 4px;">&times;</button>
            </div>
            <div id="ao-doc-modal-body" style="flex:1;overflow:auto;min-height:480px;display:flex;align-items:center;justify-content:center;padding:8px;"></div>
        </div>`;

    document.body.appendChild(modal);
}

export function openDocModal(url, filename) {
    ensureDocModal();

    const modal = document.getElementById('ao-doc-modal');
    const title = document.getElementById('ao-doc-modal-title');
    const body  = document.getElementById('ao-doc-modal-body');

    title.textContent = filename || 'Document Preview';
    body.innerHTML = '';

    const ext     = (filename || '').split('.').pop().toLowerCase();
    const isImage = ['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext);
    const isPdf   = ext === 'pdf';

    if (isImage) {
        const img = document.createElement('img');
        img.src = url;
        img.style.cssText = 'max-width:100%;max-height:75vh;display:block;';
        body.appendChild(img);
    } else if (isPdf) {
        const iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.style.cssText = 'width:100%;height:75vh;border:none;';
        body.appendChild(iframe);
    } else {
        const safeFilename = filename ? filename.replace(/"/g, '&quot;') : 'document';
        body.innerHTML = `
            <div style="text-align:center;padding:32px;">
                <p style="margin-bottom:16px;color:#555;">Preview not available for this file type.</p>
                <a href="${url}" download="${safeFilename}" style="display:inline-block;padding:10px 20px;background:#1a6fdb;color:#fff;border-radius:6px;text-decoration:none;font-size:14px;">
                    Download File
                </a>
            </div>`;
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

export function closeDocModal() {
    const modal = document.getElementById('ao-doc-modal');
    if (!modal) return;
    modal.style.display = 'none';
    document.getElementById('ao-doc-modal-body').innerHTML = '';
    document.body.style.overflow = '';
}

export function updateBulkButtons() {
    const count = document.querySelectorAll('[data-select]:checked').length;

    document.querySelectorAll(
        '.bulk-actions__btn--cancel, .bulk-actions__btn--reject, .bulk-actions__btn--approve'
    ).forEach(btn => {
        btn.disabled = count === 0;
        btn.classList.toggle('btn-disabled', count === 0);
    });
}


// ==============================
// INTERNAL HELPERS
// ==============================
function renderStatus(status) {
    const map = {
        approved: 'status-badge status-badge--approved',
        pending: 'status-badge status-badge--pending',
        rejected: 'status-badge status-badge--rejected',
        in_progress: 'status-badge status-badge--in_progress',
        completed: 'status-badge status-badge--completed'
    };

    const cls = map[status] || 'status-badge';

    return `<span class="${cls}">
        ${formatStatus(status)}
    </span>`;
}

function formatStatus(status) {
    if (!status) return '-';

    return status
        .replace('_', ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
}

function renderActions(id) {
    return `
        <img class="applicants-table__cell--actions-img" src="${baseUrl}/assets/images/icons/view.svg" data-action="view" data-id="${id}">
    `;
}

function escapeHtml(str) {
    if (!str) return '';

    return str.replace(/[&<>"']/g, m => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[m]));
}

function formatDate(date) {
    if (!date) return '-';

    return new Date(date).toISOString().split('T')[0];
}


// PAYMENT
export function updatePaymentUI(paymentId, action, remarks = '') {
  const container = document.querySelector(`#pr-action-${paymentId}`);
  if (!container) return;

  // 🔥 Remove action area (buttons + textarea)
  container.remove();

  // 🔥 Update status banner
  const banner = document.querySelector('.pr-status-banner');
  if (!banner) return;

  const isApprove = action === 'approve';

  banner.classList.remove(
    'pr-status-banner--pending',
    'pr-status-banner--approved',
    'pr-status-banner--rejected'
  );

  banner.classList.add(
    isApprove ? 'pr-status-banner--approved' : 'pr-status-banner--rejected'
  );

  const label = isApprove ? 'Approved' : 'Rejected';

  const textSpan = banner.querySelector('span');
  if (textSpan) {
    textSpan.innerHTML = `${label} &mdash; Admission Processing Fee`;
  }

  // 🔥 Add remarks (if rejected)
  if (!isApprove && remarks) {
    const details = document.querySelector('.pr-info-grid');

    const remarksBlock = document.createElement('div');
    remarksBlock.className = 'pr-info-item pr-info-item--full';
    remarksBlock.innerHTML = `
      <div class="pr-info-label">Remarks</div>
      <div class="pr-info-value">${remarks}</div>
    `;

    details?.appendChild(remarksBlock);
  }
}

export function revertPaymentUI(paymentId) {
  // simplest: just reload section (safe fallback)
  location.reload();
}