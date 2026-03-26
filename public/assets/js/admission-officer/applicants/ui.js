export function setActiveTab(tab) {
    document.querySelectorAll('[data-tab]').forEach(el => {
        el.classList.toggle('application-tabs__item--active', el.dataset.tab === tab);
    });
}

export function renderContent(html) {
    const container = document.getElementById('application-content');
    if (!container) return;

    container.innerHTML = html;
}

export function showError(message) {
    const container = document.getElementById('application-content');
    if (!container) return;
    container.innerHTML = `
        <div style="padding:40px;text-align:center;color:#c00;">
            <p style="font-size:14px;">${message}</p>
        </div>`;
}

export function showLoading() {
    const container = document.getElementById('application-content');
    if (!container) return;

    container.innerHTML = `
        <div class="application-content__loading" style="padding: 24px; text-align: center;">
            Loading...
        </div>
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
        const countEl = btn.querySelector('.filters-button_count');

        if (!countEl) return;

        countEl.textContent = counts[key] ?? 0;
    });
}

export function resetFilters() {
    const searchInput = document.querySelector('[data-search]');
    if (searchInput) {
        searchInput.value = '';
    }

    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('filter-active');
        if (btn.dataset.filter === 'all') {
            btn.classList.add('filter-active');
        }
    });
}

export function updateTableOnly(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;

    const newTbody = temp.querySelector('#application-table-body');
    const currentTbody = document.querySelector('#application-table-body');

    // ❗ guard
    if (!newTbody || !currentTbody) {
        console.warn('Table not found, fallback to full render');
        return false;
    }

    currentTbody.innerHTML = newTbody.innerHTML;
    return true;
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

export function showTableLoading() {
    const tbody = document.getElementById('application-table-body');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr class="application-table__row">
            <td class="application-table__cell" colspan="8" style="text-align:center;">
                Loading...
            </td>
        </tr>
    `;
}