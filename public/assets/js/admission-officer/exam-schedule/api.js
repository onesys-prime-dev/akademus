const config = window.examScheduleConfig || {};
const baseUrl = config.baseUrl || window.location.origin;

function buildUrl(path) {
    return `${baseUrl}${path}`;
}

export async function fetchExamScheduleContent(state) {
    const url = new URL(buildUrl('/admission-officer/exam-schedule/content'));

    url.searchParams.set('month', state.month);
    url.searchParams.set('year', state.year);
    url.searchParams.set('view', state.view);
    url.searchParams.set('page', state.page);
    url.searchParams.set('status', state.status);
    url.searchParams.set('search', state.search || '');

    if (state.day) {
        url.searchParams.set('day', state.day);
    }

    const res = await fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!res.ok) {
        throw new Error('Failed to load exam schedule content');
    }

    return res.json();
}

export async function fetchScheduleDetails(id) {
    const url = buildUrl(`/admission-officer/exam-schedule/show/${id}`);

    const res = await fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!res.ok) {
        throw new Error('Failed to load schedule details');
    }

    return res.json();
}

export async function postForm(endpoint, formData) {
    const url = buildUrl(endpoint);

    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    });

    if (!res.ok) {
        throw new Error('Request failed');
    }

    return res.json();
}