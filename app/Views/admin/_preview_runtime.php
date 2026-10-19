<?php
// Included only when $isPreview is true (set exclusively by
// ThemeController::preview()) — never rendered for real applicant/visitor
// traffic. Gives the admin Theme Customizer's live preview two things:
// 1) instant CSS-variable updates as the admin edits colors, without a reload
// 2) working in-preview navigation between the 13 real pages it supports
?>
<script>
(function () {
    var currentPreviewPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';

    // ── 1. Live CSS variable updates (postMessage from the parent admin page) ──
    var COLOR_VAR_MAP = {
        primary_color: '--primary', primary_dark: '--primary-dark', primary_light: '--primary-light',
        accent_color: '--accent', accent_text: '--accent-text', sidebar_color: '--sidebar',
        sidebar_active_color: '--sidebar-active',
        banner_bg_color: '--banner-bg', banner_box_color: '--banner-box-bg',
        button_primary_bg: '--button-primary-bg', button_primary_text: '--button-primary-text',
        button_accent_bg: '--button-accent-bg', button_accent_text: '--button-accent-text',
        header_text_color: '--header-text', progress_active_color: '--progress-active',
    };

    window.addEventListener('message', function (e) {
        if (!e.data || e.data.type !== 'theme-update') return;
        var t = e.data.theme;
        var r = document.documentElement;

        Object.keys(COLOR_VAR_MAP).forEach(function (key) {
            if (t[key]) r.style.setProperty(COLOR_VAR_MAP[key], t[key]);
        });
        if (t.primary_light) {
            r.style.setProperty('--card-gradient', 'linear-gradient(180deg, rgba(255,255,255,0.61), ' + t.primary_light + ')');
        }
        if (t.border_radius) r.style.setProperty('--radius', t.border_radius);
        if (t.font_family) {
            r.style.setProperty('--font', "'" + t.font_family + "', sans-serif");
            document.body.style.fontFamily = "'" + t.font_family + "', sans-serif";
        }
        if (t.app_name !== undefined) {
            document.querySelectorAll('.brand-name, .preview-app-name').forEach(function (el) { el.textContent = t.app_name || 'AKADEMUS'; });
        }
    });

    // ── 2. In-preview navigation ─────────────────────────────────────────────
    var PATH_TO_PAGE = {
        '/dashboard': 'dashboard',
        '/applicant-form': 'appform',
        '/requirements': 'documents',
        '/tracking-progress': 'track',
        '/exam-schedule': 'exam',
        '/admission-processing-fee': 'admission-fee',
        '/reservation-fee': 'reservation-fee',
        '/faq': 'faq',
        '/follow-up-messages': 'followup',
        '/tuition-fee-calculator': 'tuition-calc',
    };

    function pageForHref(href) {
        try {
            var path = new URL(href, window.location.href).pathname;
            for (var key in PATH_TO_PAGE) {
                if (path === key || path.endsWith(key)) return PATH_TO_PAGE[key];
            }
        } catch (e) { /* ignore malformed hrefs */ }
        return null;
    }

    // Every real internal link (Logout, Sign Up/Sign In cross-links,
    // "Forgot Password", etc.) is blocked by default inside the sandbox —
    // only the 13 known preview pages above are ever actually navigated to.
    // This is deliberately an allow-list, not a deny-list: a link hitting
    // /logout (which calls session()->destroy() — wiping the ADMIN's own
    // session, not just an applicant one) is exactly the kind of thing this
    // guards against, along with anything else not explicitly mapped.
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href]');
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:')) return;

        var isSameOrigin = true;
        try { isSameOrigin = new URL(href, window.location.href).origin === window.location.origin; } catch (e) { /* relative href */ }
        if (!isSameOrigin) return; // external links (marketing nav, etc.) are harmless, leave them alone

        e.preventDefault();

        var page = pageForHref(href);
        if (!page || page === currentPreviewPage) return;

        if (window.parent) window.parent.postMessage({ type: 'preview-navigating' }, '*');
        window.location.href = '?page=' + page;
    }, true);

    // Tell the parent which page we're actually showing, so its
    // "Previewing:" dropdown stays in sync when navigation happens by
    // clicking inside the preview rather than via the dropdown itself.
    var params = new URLSearchParams(window.location.search);
    if (window.parent) {
        window.parent.postMessage({ type: 'preview-page-changed', page: params.get('page') || 'dashboard' }, '*');
    }

    // ── 3. Placeholder for authenticated step-content AJAX calls ───────────────
    // Applicant Form / Requirements load their actual field content from
    // authenticated endpoints (…/step/N) the admin session can't call. Rather
    // than a silently-blank content area, short-circuit those specific calls
    // with a friendly placeholder.
    var realFetch = window.fetch;
    window.fetch = function (input, init) {
        var url = typeof input === 'string' ? input : (input && input.url) || '';
        if (/\/(step|update)\/\d+/.test(url) || /\/requirements\/submit/.test(url)) {
            return Promise.resolve(new Response(JSON.stringify({
                title: 'Preview',
                form: '<div style="padding:60px 20px;text-align:center;color:#94a3b8;font-size:13px;">' +
                      'This section loads your live form fields in the real portal — not simulated here.' +
                      '</div>',
                formData: {},
                is_submitted: false,
                is_fast_track: false,
                allRequiredComplete: false,
            }), { status: 200, headers: { 'Content-Type': 'application/json' } }));
        }
        return realFetch.apply(this, arguments);
    };
})();
</script>
