// state.js — Central state for the dashboard search feature

const state = {
  search: {
    query: '',
    results: [],
    isLoading: false,
    isOpen: false,
    debounceTimer: null,
  },
  program: {
    value: ''
  }
};

/**
 * Returns a shallow copy of a state slice to prevent direct mutation.
 * @param {string} slice - Key of the state slice.
 */
export function getState(slice) {
  return { ...state[slice] };
}

/**
 * Merges partial updates into a state slice.
 * @param {string} slice
 * @param {object} updates
 */
export function setState(slice, updates) {
  if (typeof state[slice] === 'object') {
    state[slice] = { ...state[slice], ...updates };
  } else {
    state[slice] = updates;
  }
}
