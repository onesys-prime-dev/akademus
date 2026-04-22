import {
    initFilters,
    initSearch,
    initPagination,
    initTabs,
    initProgram,
    initBulkSelect,
    initBulkActions,
    initRowClick,
    initDocModal,
    initActions,
    initPaymentActions,
    initLightbox,
    init
} from './handlers.js';

import { state } from './state.js';

document.addEventListener('DOMContentLoaded', () => {

    // ================= COMMON =================
    initFilters();
    initSearch();
    initPagination();
    initDocModal();
    initPaymentActions();
    initLightbox();

    // ================= PROFILE =================
    if (state.mode === 'profile') {
        initTabs();
    }

    // ================= LIST =================
    if (state.mode === 'list') {
        initProgram();
        initBulkSelect();
        initBulkActions();
        initRowClick();
        initActions();
    }

    // ================= INITIAL LOAD =================
    init();
});