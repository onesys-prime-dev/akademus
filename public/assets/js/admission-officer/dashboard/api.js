import { state } from './state.js';

const { baseUrl } = window.applicantsConfig;

export async function fetchApplicants() {
    const params = new URLSearchParams(state);

    const res = await fetch(`${baseUrl}/admission-officer/dashboard/fetch-applicants?${params}`);
    return await res.json();
}

export async function bulkUpdateApplicants(ids, status) {
    const res = await fetch(`${baseUrl}/admission-officer/dashboard/bulk-update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids, status })
    });

    return await res.json();
}

export function goToApplicant(id) {
    window.location.href = `${baseUrl}admission-officer/applicants/${id}`;
}