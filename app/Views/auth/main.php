<!-- auth/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akademus | <?= $title ?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
    --primary: <?= $theme['primary_color'] ?? '#0040A6' ?>;
    --primary-dark: <?= $theme['primary_dark'] ?? '#003080' ?>;
    --accent: <?= $theme['accent_color'] ?? '#FFB200' ?>;
    --accent-text: <?= $theme['accent_text'] ?? '#002d7a' ?>;
    --radius: <?= $theme['border_radius'] ?? '12px' ?>;
    --font: '<?= $theme['font_family'] ?? 'Poppins' ?>', sans-serif;
}
</style>

<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') . '?v=' . time(); ?>">

</head>
<body>
    <div class="header">
        <a href="<?= base_url('/') ?>" class="brand">
            <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>">
        </a>

        <div class="nav-links">
            <a href="<?= base_url('admissions') ?>">
                <p>Admissions</p>
            </a>
            <a href="<?= base_url('academics') ?>">
                <p>Academics</p>
            </a>
            <a href="<?= base_url('about-us') ?>">
                <p>About Us</p>
            </a>
            <a href="<?= base_url('contact-us') ?>">
                <p>Contact Us</p>
            </a>
        </div>

        <div class="external-link-container">
            <a href="#">
                <img class="logo" src="<?= base_url('assets/images/logo_mentora.png') ?>" alt="">
                <div class="label-wrapper">
                    <span>Proceed to Mentora</span>
                    <img src="<?= base_url('assets/images/icons/arrow-right.svg') ?>" alt="">
                </div>
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="main-card">
            <div class="left">
                <div class="left-content-container">
                    <a href="<?= base_url('/') ?>" class="brand">
                        <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>">
                        <div class="header-wrapper">
                            <p>AKADEMUS</p>
                            <span>Admission System</span>
                        </div>
                    </a>

                    <div class="left-tagline-container">
                        <div class="tagline">
                            <p>Your Future</p>
                            <p>Starts <span>Here</span></p>
                        </div>
                        <span>Complete your admission entirely online — from document submission to enrollment.</span>

                        <div class="steps-container">
                            <div class="step">
                                <div class="step-number">1</div>
                                <span>Create your applicant account</span>
                            </div>

                            <div class="step">
                                <div class="step-number">2</div>
                                <span>Submit required documents</span>
                            </div>

                            <div class="step">
                                <div class="step-number">3</div>
                                <span>Track your application status</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="right">
                <!-- container for sign-in and sign-up cards -->
                <div id="auth-header" class="auth-header-container">
                    <h2><?= $auth_header ?></h2>
                    <span><?= $auth_sub_header ?></span>
                </div>

                <div class="switch-auth-toggle">
                    <div id="toggle-sign-in" class="toggle-btn">
                        Sign In
                    </div>

                    <div id="toggle-sign-up" class="toggle-btn">
                        Sign Up
                    </div>
                </div>

                <div id="auth-form-container">
                    <!-- sign-in and sign-up forms will be loaded here via AJAX -->
                    <?php if (isset($auth_field)) {
                        echo view($auth_field);
                    } ?>

                    <?php if (isset($success)) {
                        echo view($success);
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
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
            loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
        });

        toggleSignUp.addEventListener('click', () => {
            loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', () => {
            const path = window.location.pathname;
            if (path.includes('sign_up')) {
                loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
            } else {
                loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const path = window.location.pathname;

            /* if (path.includes('sign_up')) {
            //     loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
            // } else if (path.includes('success')) {
            //     loadAuth('<?= base_url('success') ?>', toggleSignUp);
            // } else {
            //     loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
            } */
            if (path.includes('sign_in') || path.includes('sign_up')) {
                if (path.includes('sign_up')) {
                    loadAuth('<?= base_url('sign_up') ?>', toggleSignUp);
                } else {
                    loadAuth('<?= base_url('sign_in') ?>', toggleSignIn);
                }

            }
        });
        
        // Handle if path.includes('success') is present (after registration) hide the switch-auth-toggle
        if (window.location.pathname.includes('success')) {
            document.querySelector('.switch-auth-toggle').style.display = 'none';
        }
    </script>
</body>
</html>