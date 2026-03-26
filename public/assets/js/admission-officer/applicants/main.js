import { initTabs, initFilters, initSearch, initPagination, init, initDocModal } from './handlers.js';

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initFilters();
    initSearch();
    initPagination();
    initDocModal();
    init();
});