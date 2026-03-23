import { initFilters, initProgram, initSearch, initPagination, initBulkSelect, initBulkActions, initRowClick, init } from './handlers.js';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initProgram();
    initSearch();
    initPagination();
    initBulkSelect();
    initBulkActions();
    initRowClick();
    init();
});