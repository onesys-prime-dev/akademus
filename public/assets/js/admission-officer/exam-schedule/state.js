const config = window.examScheduleConfig || {};

export const state = {
    month: Number(config.month) || new Date().getMonth() + 1,
    year: Number(config.year) || new Date().getFullYear(),
    view: config.activeView || 'calendar',
    page: 1,
    search: '',
    status: 'all',
    day: null,
    selectedScheduleId: null,
    baseUrl: config.baseUrl || window.location.origin,
    contentUrl: config.contentUrl || '/admission-officer/exam-schedule/content',
};