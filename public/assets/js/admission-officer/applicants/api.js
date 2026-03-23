import { state } from './state.js';

const { baseUrl } = window.applicantsConfig;

export async function fetchApplicationContent() {
    const params = new URLSearchParams({
        applicant_id: state.applicantId,
        tab: state.activeTab,
        status: state.status,
        search: state.search,
        page: String(state.page)
    });

    const res = await fetch(`${baseUrl}/admission-officer/applicants/fetch-content?${params.toString()}`);
    return await res.json();
}