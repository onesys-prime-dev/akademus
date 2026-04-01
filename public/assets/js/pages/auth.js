// assets/js/pages/auth.js

function showErrorModal(message) {
    const existing = document.getElementById('auth-error-modal');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'auth-error-modal';
    overlay.className = 'auth-error-modal-overlay';
    overlay.innerHTML = `
        <div class="auth-error-modal-box">
            <div class="auth-error-modal-icon">✕</div>
            <h3>Registration Failed</h3>
            <p>${message}</p>
            <button class="auth-error-modal-close btn-primary">OK</button>
        </div>
    `;

    document.body.appendChild(overlay);
    requestAnimationFrame(() => overlay.classList.add('auth-error-modal--show'));

    const close = () => {
        overlay.classList.remove('auth-error-modal--show');
        setTimeout(() => overlay.remove(), 250);
    };

    overlay.querySelector('.auth-error-modal-close').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
}

const { baseUrl, signInUrl, signUpUrl } = window.authConfig;

// AUTH TOGGLE
    const toggleSignIn  = document.getElementById('toggle-sign-in');
    const toggleSignUp  = document.getElementById('toggle-sign-up');
    const formContainer = document.getElementById('auth-form-container');
    const headerTitle   = document.querySelector('#auth-header h2');
    const headerSubtitle = document.querySelector('#auth-header span');

    function loadAuth(url, activeBtn) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.title           = "Akademus | " + data.title;
            headerTitle.textContent  = data.header;
            headerSubtitle.textContent = data.subheader;
            formContainer.innerHTML  = data.form;

            if (data.programs) {
                const select = document.getElementById('program');
                if (select) {
                    select.innerHTML = '<option value="" disabled selected hidden>Select a Program</option>';
                    data.programs.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        select.appendChild(opt);
                    });
                }
            }

            toggleSignIn.classList.remove('active');
            toggleSignUp.classList.remove('active');
            activeBtn.classList.add('active');
            history.pushState(null, '', url);
        });
    }

    toggleSignIn.addEventListener('click', () => loadAuth(`${signInUrl}`, toggleSignIn));
    toggleSignUp.addEventListener('click', () => loadAuth(`${signUpUrl}`, toggleSignUp));

    window.addEventListener('popstate', () => {
        const path = window.location.pathname;
        if (path.includes('sign-up')) loadAuth(`${signUpUrl}`, toggleSignUp);
        else loadAuth(`${signInUrl}`, toggleSignIn);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const path = window.location.pathname;
        if (path.includes('sign-up')) loadAuth(`${signUpUrl}`, toggleSignUp);
        else if (path.includes('sign-in')) loadAuth(`${signInUrl}`, toggleSignIn);
    });

    if (window.location.pathname.includes('success')) {
        document.querySelector('.switch-auth-toggle').style.display = 'none';
    }

    // TURNSTILE MODAL
    let widgetId = null;

    document.addEventListener('click', function(e) {

        // Open modal
        if (e.target && e.target.id === 'sign-up-button') {
            const form = document.getElementById('sign-up-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const modal = document.getElementById('turnstile-modal');
            modal.style.display = 'flex';

            if (widgetId !== null) {
                turnstile.reset(widgetId);
            } else {
                widgetId = turnstile.render('#turnstile-widget', {
                    sitekey: '1x00000000000000000000AA',
                    theme: 'light',
                    callback: function(token) {
                        modal.style.display = 'none';
                        const form = document.getElementById('sign-up-form');
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.href = data.redirect;
                            } else {
                                showErrorModal(data.message || 'Something went wrong.');
                            }
                        });
                    },
                    'error-callback': function() {
                        turnstile.reset(widgetId);
                    }
                });
            }
        }

        // Cancel button
        if (e.target && e.target.id === 'turnstile-cancel') {
            document.getElementById('turnstile-modal').style.display = 'none';
            if (widgetId !== null) turnstile.reset(widgetId);
        }

        // Backdrop click
        if (e.target && e.target.id === 'turnstile-modal') {
            e.target.style.display = 'none';
            if (widgetId !== null) turnstile.reset(widgetId);
        }
    });

    // HAMBURGER MENU
    const hamburger      = document.getElementById('hamburger');
    const mobileMenu     = document.getElementById('mobile-menu');
    const overlay        = document.getElementById('mobile-overlay');
    const servicesTrigger = document.getElementById('mobile-services-trigger');
    const servicesItems  = document.getElementById('mobile-services-items');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
        overlay.classList.toggle('open');
    });

    overlay.addEventListener('click', () => {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
        overlay.classList.remove('open');
    });

    servicesTrigger.addEventListener('click', () => {
        servicesItems.classList.toggle('open');
        servicesTrigger.classList.toggle('open');
    });