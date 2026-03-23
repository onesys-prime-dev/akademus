import { initTabs, initFilters, initSearch, initPagination, init } from './handlers.js';

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initFilters();
    initSearch();
    initPagination();
    init();
});