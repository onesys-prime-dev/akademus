<!-- auth/sign_in.php -->

<div>
    <form
        id="sign-in-form"
        class="form-container"
        action="<?= base_url('sign-in/login') ?>"
        method="post">
        <div class="input-container">
            <div class="input-field">
                <label for="email">EMAIL ADDRESS</label>
                <input type="email" name="email" placeholder="yourname@email.com" required>
            </div>

            <div class="input-field password-field">
                <label for="password">PASSWORD</label>

                <div class="password-input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="********"
                        required>

                    <button
                        type="button"
                        class="toggle-password-btn"
                        onclick="togglePassword(this)">

                        <!-- Eye Open -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="eye-open">

                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        <!-- Eye Closed -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="eye-closed"
                            style="display:none;">

                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>

                    </button>
                </div>
            </div>
        </div>

        <div class="forgot-pass">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="remember_me" value="1">
                <span style="font-size:13px;">Remember Me</span>
            </label>

            <a href="#" onclick="openForgotModal(); return false;">Forgot Password?</a>
        </div>

        <button type="submit" id="sign-in-button" class="submit-btn">Sign In</button>

        <div class="switch-to-sign-up">
            <span>Don't have an account? <a href="<?= base_url('/sign-up') ?>">Click here</a></span>
        </div>
    </form>
</div>

<script>
    function togglePassword(button) {

        const input = button.parentElement.querySelector('input');

        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');

        if (input.type === 'password') {

            input.type = 'text';

            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';

        } else {

            input.type = 'password';

            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>