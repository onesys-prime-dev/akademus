// main.js — Entry point

import { registerSearchHandlers, initSearchRowClick, initRowClick, initProgram } from './handlers.js';

function init() {
  registerSearchHandlers();
  initSearchRowClick();
  initRowClick();
  initProgram();
}

// Guard against running before DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
