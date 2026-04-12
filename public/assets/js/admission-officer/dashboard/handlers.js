// handlers.js — Event handling and orchestration

import { getState, setState }               from './state.js';
import { fetchSearchResults, goToApplicant, fetchDashboardData }               from './api.js';
import { getSearchInput, getDropdown, getSearchWrapper,
         openDropdown, closeDropdown,
         setLoadingState, renderResults,
         renderError, moveFocus, getActiveItem, renderDashboard } from './ui.js';

const DEBOUNCE_DELAY = 300; // ms
const MIN_QUERY_LENGTH = 2;

let abortController = null;

// ── Search Input ───────────────────────────────────────────────────────────────

function onSearchInput(event) {
  const query = event.target.value;
  setState('search', { query });

  const { debounceTimer } = getState('search');
  clearTimeout(debounceTimer);

  if (query.trim().length < MIN_QUERY_LENGTH) {
    closeDropdown();
    setState('search', { results: [], isOpen: false });
    return;
  }

  const timer = setTimeout(() => executeSearch(query), DEBOUNCE_DELAY);
  setState('search', { debounceTimer: timer });
}

async function executeSearch(query) {
  // Cancel any in-flight request
  if (abortController) abortController.abort();
  abortController = new AbortController();

  setState('search', { isLoading: true });
  setLoadingState(true);
  openDropdown();

  try {
    const results = await fetchSearchResults(query, abortController.signal);
    setState('search', { results, isLoading: false, isOpen: true });
    renderResults(results, query);
  } catch (error) {
    if (error.name === 'AbortError') return; // Superseded by newer request
    setState('search', { isLoading: false });
    renderError('Unable to load results. Please try again.');
    console.error('[Search] Fetch error:', error);
  } finally {
    setLoadingState(false);
  }
}

// ── Keyboard Navigation ────────────────────────────────────────────────────────

function onSearchKeydown(event) {
  const { isOpen } = getState('search');

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault();
      if (isOpen) moveFocus('next');
      break;

    case 'ArrowUp':
      event.preventDefault();
      if (isOpen) moveFocus('prev');
      break;

    case 'Escape':
      closeDropdown();
      setState('search', { isOpen: false });
      getSearchInput()?.focus();
      break;

    case 'Enter': {
      const activeItem = getActiveItem();
      if (activeItem?.dataset.url) {
        window.location.href = activeItem.dataset.url;
      }
      break;
    }
  }
}

// ── Click on Result Item ───────────────────────────────────────────────────────

function onDropdownClick(event) {
  const item = event.target.closest('.search-dropdown__item');
  if (!item) return;

  if (item.dataset.url) {
    window.location.href = item.dataset.url;
  }
}

// ── Click Outside to Close ─────────────────────────────────────────────────────

function onDocumentClick(event) {
  const wrapper = getSearchWrapper();
  if (wrapper && !wrapper.contains(event.target)) {
    closeDropdown();
    setState('search', { isOpen: false });
  }
}

// ── Focus Trap: Close on Input Blur (defer to allow click events) ─────────────

function onSearchBlur() {
  // Use setTimeout to allow click events on dropdown items to fire first
  setTimeout(() => {
    const dropdown = getDropdown();
    const activeEl = document.activeElement;

    if (dropdown && !dropdown.contains(activeEl)) {
      closeDropdown();
      setState('search', { isOpen: false });
    }
  }, 150);
}

// ── Registration ───────────────────────────────────────────────────────────────

export function registerSearchHandlers() {
  const input = getSearchInput();
  const dropdown = getDropdown();

  if (!input || !dropdown) {
    console.warn('[Search] Required DOM elements not found.');
    return;
  }

  input.addEventListener('input',   onSearchInput);
  input.addEventListener('keydown', onSearchKeydown);
  input.addEventListener('blur',    onSearchBlur);
  dropdown.addEventListener('click', onDropdownClick);
  document.addEventListener('click', onDocumentClick);
}

// ===============================
export function initSearchRowClick() {
    document.addEventListener('click', (e) => {
        const row = e.target.closest('.search-dropdown__item');
        if (!row) return;

        if (row.dataset.id) {
        goToApplicant(row.dataset.id);
        }
    });
}

// ===============================
export function initProgram() {
  document.addEventListener('change', (e) => {
    if (!e.target.matches('[data-program]')) return;

    const program = e.target.value;

    setState('program', { value: program });

    update({ fullRender: true }); // 🔥 triggers everything
  });
}

// ===============================
export async function update({ fullRender = false } = {}) {
  const programState = getState('program');

  try {
    const data = await fetchDashboardData({
      program: programState.value // ✅ THIS FIXES EVERYTHING
    });

    console.log('Program sent:', programState.value);

    renderDashboard(data);

  } catch (err) {
    console.error('[Dashboard] Update failed:', err);
  }
}

export function initRowClick() {
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