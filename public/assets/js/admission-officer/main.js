import { initFilters, initProgram, initSearch, initPagination, initBulkSelect, initBulkActions, init } from './handlers.js';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initProgram();
    initSearch();
    initPagination();
    initBulkSelect();
    initBulkActions();
    init();
});