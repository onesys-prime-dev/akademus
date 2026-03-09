<!-- auth/sign_up.php -->

<div>
    <form 
        id="sign-up-form"
        class="form-container"
        action="<?= base_url('sign-up/register') ?>" 
        method="post"
    >
        <div class="input-container">
            <div class="two-input-fields">
                <div class="input-field">
                    <label for="firstname">FIRST NAME</label>
                    <input type="text" name="firstname" placeholder="John" required>
                </div>

                <div class="input-field">
                    <label for="lastname">LAST NAME</label>
                    <input type="text" name="lastname" placeholder="Doe" required>
                </div>
            </div>
            <div class="input-field">
                <label for="email">EMAIL ADDRESS</label>
                <input type="email" name="email" placeholder="yourname@email.com" required>
            </div>

            <div class="input-field">
                <label for="mobile">MOBILE NUMBER</label>
                <input type="tel" name="mobile" placeholder="+63 9XX XXX XXXX" required>
            </div>

            <div class="input-field">
                <label for="program">1ST CHOICE OF PROGRAM</label>
                <select name="program" id="program" required>
                    <option value="" disabled selected hidden>Select a Program</option>
                    <option value="Computer Engineering">Computer Engineering</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Information System">Information System</option>
                    <option value="Information Technology">Information Technology</option>
                </select>
            </div>
        </div>

        <div class="note-container">
            <span>Note:</span>
            <p>Your final program will be confirmed in the Applicant Information Form. These preferences help us process your application.</p>
        </div>

        <button type="submit" id="sign-up-button" class="auth-btn btn-primary">CREATE ACCOUNT</button>
    </form>
</div>