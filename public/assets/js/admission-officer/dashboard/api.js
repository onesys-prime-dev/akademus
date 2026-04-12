// api.js — Data-fetching layer

const { baseUrl } = window.dashboardConfig;

/**
 * Fetches search results from the server.
 * @param {string} query - The search string.
 * @param {AbortSignal} [signal] - Optional AbortController signal.
 * @returns {Promise<{ applicants: Array }>}
 */
export async function fetchSearchResults(query, signal) {
  const url = new URL(`${baseUrl}admission-officer/dashboard/search`);
  url.searchParams.set('q', query.trim());

  const response = await fetch(url.toString(), {
    method: 'GET',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    signal,
  });

  if (!response.ok) {
    throw new Error(`Search request failed: ${response.status}`);
  }

  return response.json();
}

export function goToApplicant(id) {
    window.location.href = `${baseUrl}admission-officer/detailed-applicant/${id}`;
}

export async function fetchDashboardData(params = {}) {
  const url = new URL(`${baseUrl}admission-officer/dashboard/data`);

  Object.keys(params).forEach(key => {
    if (params[key]) url.searchParams.set(key, params[key]);
  });

  const response = await fetch(url.toString(), {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  });

  if (!response.ok) {
    throw new Error('Failed to fetch dashboard data');
  }

  return response.json();
}