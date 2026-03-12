<!-- auth/sign_in.php -->

<div>
    <form 
        id="sign-in-form"
        class="form-container"
        action="<?= base_url('sign-in/login') ?>" 
        method="post"
    >
        <div class="input-container">
            <div class="input-field">
                <label for="email">EMAIL ADDRESS</label>
                <input type="email" name="email" placeholder="yourname@email.com" required>
            </div>

            <div class="input-field">
                <label for="password">PASSWORD</label>
                <input type="password" name="password" placeholder="********" required>
            </div>
        </div>

        <div class="forgot-pass">
            <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" id="sign-in-button" class="submit-btn">Sign In</button>

        <div class="switch-to-sign-up">
            <span>Don't have an account? <a href="<?= base_url('/sign-up') ?>">Click here</a></span>
        </div>
    </form>
</div>