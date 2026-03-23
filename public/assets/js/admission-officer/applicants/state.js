const { applicantId, activeTab } = window.applicantsConfig;

export const state = {
    applicantId,
    activeTab: activeTab,
    status: 'all',
    search: '',
    page: 1,
    totalPages: 1,
    limit: 10
};