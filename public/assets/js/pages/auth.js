// assets/js/pages/auth.js

const { baseUrl, signInUrl, signUpUrl } = window.authConfig;

const toggleSignIn = document.getElementById('toggle-sign-in');
const toggleSignUp = document.getElementById('toggle-sign-up');
const formContainer = document.getElementById('auth-form-container');
const headerTitle = document.querySelector('#auth-header h2');
const headerSubtitle = document.querySelector('#auth-header span');

function loadAuth(url, activeBtn) {
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {

        // Update page title
        document.title = "Akademus | " + data.title;

        // Update header
        headerTitle.textContent = data.header;
        headerSubtitle.textContent = data.subheader;

        // Inject form FIRST
        formContainer.innerHTML = data.form;

        // ✅ Append programs if they exist (only for sign up)
        if (data.programs) {
            const select = document.getElementById('program');

            if (select) {
                // Reset select (prevent duplicate options)
                select.innerHTML = '<option value="" disabled selected hidden>Select a Program</option>';

                data.programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.id;
                    option.textContent = program.name;
                    select.appendChild(option);
                });
            }
        }

        // Update active button
        toggleSignIn.classList.remove('active');
        toggleSignUp.classList.remove('active');
        activeBtn.classList.add('active');

        // Update URL without reload
        history.pushState(null, '', url);
    });
}

toggleSignIn.addEventListener('click', () => {
    loadAuth(`${signInUrl}`, toggleSignIn);
});

toggleSignUp.addEventListener('click', () => {
    loadAuth(`${signUpUrl}`, toggleSignUp);
});

// Handle browser back/forward buttons
window.addEventListener('popstate', () => {
    const path = window.location.pathname;
    if (path.includes('sign-up')) {
        loadAuth(`${signUpUrl}`, toggleSignUp);
    } else {
        loadAuth(`${signInUrl}`, toggleSignIn);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;

    /* if (path.includes('sign-up')) {
    //     loadAuth('<?= base_url('sign-up') ?>', toggleSignUp);
    // } else if (path.includes('success')) {
    //     loadAuth('<?= base_url('success') ?>', toggleSignUp);
    // } else {
    //     loadAuth('<?= base_url('sign-in') ?>', toggleSignIn);
    } */
    if (path.includes('sign-in') || path.includes('sign-up')) {
        if (path.includes('sign-up')) {
            loadAuth(`${signUpUrl}`, toggleSignUp);
        } else {
            loadAuth(`${signInUrl}`, toggleSignIn);
        }

    }
});

// Handle if path.includes('success') is present (after registration) hide the switch-auth-toggle
if (window.location.pathname.includes('success')) {
    document.querySelector('.switch-auth-toggle').style.display = 'none';
}