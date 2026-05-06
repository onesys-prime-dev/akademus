const {
    baseUrl,
    applicantId = null,
    activeTab = ''
} = window.applicantsConfig || {};

// optional: export config separately if needed in api.js
export const config = {
    baseUrl
};

export const state = {
    // mode detection
    mode: applicantId ? 'profile' : 'list',

    // profile
    applicantId,
    activeTab,

    // shared
    status: 'all',
    search: '',
    page: 1,
    totalPages: 1,
    limit: 10,

    // list-only
    program: ''
};