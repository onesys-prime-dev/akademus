import { state } from './state.js';
import {
    fetchApplicationContent,
    fetchApplicants,
    bulkUpdateApplicants,
    goToApplicant
} from './api.js';

import {
    // profile
    setActiveTab,
    renderContent,
    showError,
    showContentLoading,
    updateTableOnly,

    // list
    updateApplicantsTable,
    resetBulkSelection,
    updateBulkButtons,

    // shared
    showTableLoading,
    updatePagination,
    updateFilterCounts,
    resetFilters,

    // doc modal
    openDocModal,
    closeDocModal
} from './ui.js';

import { handlePaymentAction } from './payments.js';

let debounceTimer;


// ===============================
// CORE UPDATE ENGINE
// ===============================
async function update({ fullRender = false } = {}) {

    // ================= PROFILE =================
    if (state.mode === 'profile') {
        fullRender ? showContentLoading() : showTableLoading();

        let res;
        try {
            res = await fetchApplicationContent();
        } catch (err) {
            console.error('fetchApplicationContent error:', err);
            showError('Failed to load content. Please refresh the page.');
            return;
        }

        if (!res.success) {
            showError(res.message || 'Failed to load content.');
            return;
        }

        state.totalPages = Math.max(1, Math.ceil((res.total || 0) / res.limit));

        if (state.page > state.totalPages) {
            state.page = state.totalPages;
        }

        setActiveTab(state.activeTab);

        if (fullRender) {
            renderContent(res.html);
        } else {
            const ok = updateTableOnly(res.html);
            if (!ok) renderContent(res.html);
        }

        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.toggle('filter-active', btn.dataset.filter === state.status);
        });

        updatePagination(res);
        updateFilterCounts(res.counts || {});
    }


    // ================= LIST =================
    if (state.mode === 'list') {

        showTableLoading();

        resetBulkSelection();
        updateBulkButtons();

        const res = await fetchApplicants();

        state.totalPages = Math.max(1, Math.ceil(res.total / res.limit));

        if (state.page > state.totalPages) {
            state.page = state.totalPages;
        }

        updateApplicantsTable(res.data);
        updatePagination(res);
        updateFilterCounts(res.counts || {});
    }
}


// ===============================
// COMMON EVENTS
// ===============================
export function initFilters() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-filter]');
        if (!btn) return;

        state.status = btn.dataset.filter;
        state.page = 1;

        document.querySelectorAll('[data-filter]').forEach(b => {
            b.classList.remove('filter-active');
        });

        btn.classList.add('filter-active');

        update();
    });
}

export function initSearch() {
    document.addEventListener('input', (e) => {
        if (!e.target.matches('[data-search]')) return;

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            state.search = e.target.value;
            state.page = 1;

            update();
        }, 400);
    });
}

export function initPagination() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-page]');
        if (!btn) return;

        if (btn.dataset.page === 'next' && state.page < state.totalPages) {
            state.page++;
        }

        if (btn.dataset.page === 'prev' && state.page > 1) {
            state.page--;
        }

        // ✅ FIX HERE
        if (state.mode === 'profile') {
            update({ fullRender: true });
        } else {
            update();
        }
    });
}


// ===============================
// PROFILE EVENTS
// ===============================
export function initTabs() {
    if (state.mode !== 'profile') return;

    document.addEventListener('click', (e) => {
        const tab = e.target.closest('[data-tab]');
        if (!tab) return;

        const nextTab = tab.dataset.tab;
        if (!nextTab || nextTab === state.activeTab) return;

        state.activeTab = nextTab;
        state.status = 'all';
        state.search = '';
        state.page = 1;

        resetFilters();

        update({ fullRender: true });
    });
}


// ===============================
// LIST EVENTS
// ===============================
export function initProgram() {
    if (state.mode !== 'list') return;

    document.addEventListener('change', (e) => {
        if (!e.target.matches('[data-program]')) return;

        state.program = e.target.value;
        state.page = 1;

        update();
    });
}

export function initDocModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.ao-view-doc-btn');
        if (btn) {
            openDocModal(btn.dataset.docUrl, btn.dataset.docName);
            return;
        }

        const closeBtn = e.target.closest('#ao-doc-modal-close');
        if (closeBtn) { closeDocModal(); return; }

        const modal = document.getElementById('ao-doc-modal');
        if (modal && e.target === modal) { closeDocModal(); }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDocModal();
    });
}

export function initBulkSelect() {
    if (state.mode !== 'list') return;

    const selectAll = document.querySelector('.bulk-actions__btn--select-all input');

    document.addEventListener('change', (e) => {

        // select all
        if (e.target === selectAll) {
            const checked = e.target.checked;

            document.querySelectorAll('[data-select]').forEach(cb => {
                cb.checked = checked;
            });
        }

        // individual
        if (e.target.matches('[data-select]')) {
            const all = document.querySelectorAll('[data-select]');
            const checked = document.querySelectorAll('[data-select]:checked');

            if (selectAll) {
                selectAll.checked = all.length === checked.length;
            }
        }

        updateBulkButtons();
    });
}

export function initBulkActions() {
    if (state.mode !== 'list') return;

    document.addEventListener('click', async (e) => {

        // CANCEL
        if (e.target.closest('.bulk-actions__btn--cancel')) {
            resetBulkSelection();

            document.querySelectorAll('[data-select]').forEach(cb => {
                cb.checked = false;
            });

            updateBulkButtons();
            return;
        }

        const ids = getSelectedIds();
        if (!ids.length) return;

        if (e.target.closest('.bulk-actions__btn--approve')) {
            await bulkUpdateApplicants(ids, 'approved');
            update();
        }

        if (e.target.closest('.bulk-actions__btn--reject')) {
            await bulkUpdateApplicants(ids, 'rejected');
            update();
        }
    });
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('[data-select]:checked'))
        .map(cb => cb.closest('[data-row]')?.dataset.id)
        .filter(Boolean);
}

export function initRowClick() {
    if (state.mode !== 'list') return;

    document.addEventListener('click', (e) => {
        const row = e.target.closest('[data-row]');
        if (!row) return;

        if (
            e.target.closest('[data-select]') ||
            e.target.closest('[data-action]')
        ) {
            return;
        }

        const id = row.dataset.id;
        if (!id) return;

        goToApplicant(id);
    });
}

export function initActions() {
    document.addEventListener('click', (e) => {
        const actionEl = e.target.closest('[data-action]');
        if (!actionEl) return;

        e.stopPropagation(); // ✅ THIS FIXES YOUR BUG

        const action = actionEl.dataset.action;
        const id = actionEl.dataset.id;

        if (!id) return;

        if (action === 'view') {
            goToApplicant(id);
        }

        if (action === 'edit') {
            console.log('edit', id);
        }

        if (action === 'delete') {
            console.log('delete', id);
        }
    });
}


// ===============================
// PAYMENT ACTIONS
// ===============================
export function initPaymentActions() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-payment-action]');
    if (!btn) return;

    const paymentId = btn.dataset.paymentId;
    const action    = btn.dataset.action;
    const panel     = btn.dataset.panel;

    if (!paymentId || !action) return;

    handlePaymentAction(paymentId, action, panel);
  });
}

export function initLightbox() {

  // OPEN
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-lightbox]');
    if (!trigger) return;

    const url = trigger.dataset.src;

    const img = document.getElementById('pr-lightbox-img');
    const box = document.getElementById('pr-lightbox');

    if (img && box) {
      img.src = url;
      box.classList.add('active');
    }
  });

  // CLOSE (overlay or button)
  document.addEventListener('click', (e) => {
    const isOverlay = e.target.matches('[data-lightbox-overlay]');
    const isCloseBtn = e.target.closest('[data-lightbox-close]');

    if (!isOverlay && !isCloseBtn) return;

    const box = document.getElementById('pr-lightbox');
    const img = document.getElementById('pr-lightbox-img');

    if (box) box.classList.remove('active');
    if (img) img.src = '';
  });

  // ESC key support (nice UX)
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;

    const box = document.getElementById('pr-lightbox');
    const img = document.getElementById('pr-lightbox-img');

    if (box?.classList.contains('active')) {
      box.classList.remove('active');
      if (img) img.src = '';
    }
  });
}


// ===============================
// INIT
// ===============================
export function init() {

    if (state.mode === 'profile') {
        setActiveTab(state.activeTab);
        update({ fullRender: true });
    }

    if (state.mode === 'list') {
        update();
    }
}