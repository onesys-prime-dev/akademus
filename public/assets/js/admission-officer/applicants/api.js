import { state, config } from './state.js';

const { baseUrl } = config;

// ===============================
// PROFILE (TAB CONTENT)
// ===============================
export async function fetchApplicationContent() {
    const params = new URLSearchParams({
        applicant_id: state.applicantId,
        tab: state.activeTab,
        status: state.status,
        search: state.search,
        page: String(state.page)
    });

    const res = await fetch(
        `${baseUrl}/admission-officer/detailed-applicant/fetch-content?${params}`
    );

    return res.json();
}


// ===============================
// LIST (APPLICANTS)
// ===============================
export async function fetchApplicants() {
    const params = new URLSearchParams({
        status: state.status,
        program: state.program,
        search: state.search,
        page: state.page
    });

    const res = await fetch(
        `${baseUrl}/admission-officer/applicants/fetch-applicants?${params}`
    );

    return res.json();
}


// ===============================
// BULK ACTIONS
// ===============================
export async function bulkUpdateApplicants(ids, status) {
    const res = await fetch(
        `${baseUrl}/admission-officer/applicants/bulk-update`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids, status })
        }
    );

    return res.json();
}


// ===============================
// NAVIGATION
// ===============================
export function goToApplicant(id) {
    window.location.href = `${baseUrl}admission-officer/detailed-applicant/${id}`;
}


export async function decideDocument(id, status) {
    const res = await fetch(
        `${baseUrl}/admission-officer/applicants/document/${id}/decide`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        }
    );

    return res.json();
}