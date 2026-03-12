<div>
    <form
        id="sign-up-form"
        class="form-container"
        action="<?= base_url('sign-up/register') ?>"
        method="post"
    >
        <?= csrf_field() ?>

        <div class="input-container">
            <div class="two-input-fields">
                <div class="input-field">
                    <label for="firstname">FIRST NAME</label>
                    <input type="text" name="firstname" placeholder="John" value="<?= old('firstname') ?>" required>
                </div>
                <div class="input-field">
                    <label for="lastname">LAST NAME</label>
                    <input type="text" name="lastname" placeholder="Doe" value="<?= old('lastname') ?>" required>
                </div>
            </div>

            <div class="input-field">
                <label for="email">EMAIL ADDRESS</label>
                <input type="email" name="email" placeholder="yourname@email.com" value="<?= old('email') ?>" required>
            </div>

            <div class="input-field">
                <label for="mobile">MOBILE NUMBER</label>
                <input type="tel" name="mobile" placeholder="09XXXXXXXXX" value="<?= old('mobile') ?>" required>
            </div>

            <div class="input-field">
                <label for="program">CHOICE OF PROGRAM</label>
                <select name="program" id="program" required>
                    <option value="" disabled selected hidden>Select a Program</option>
                    <?php foreach ($programs as $program): ?>
                        <option value="<?= $program['id'] ?>" <?= old('program') == $program['id'] ? 'selected' : '' ?>>
                            <?= esc($program['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="note-container">
            <span>Note:</span>
            <p>Your final program will be confirmed in the Applicant Information Form. These preferences help us process your application.</p>
        </div>

        <button type="button" id="sign-up-button" class="submit-btn">CREATE ACCOUNT</button>
    </form>
</div>

<!-- TURNSTILE MODAL -->
<div id="turnstile-modal" class="turnstile-modal-overlay" style="display:none;">
    <div class="turnstile-modal-box">
        <div class="turnstile-modal-header">
            <p>Security Check</p>
            <span>Please verify you're human to continue.</span>
        </div>
        <div id="turnstile-widget"></div>
        <button type="button" id="turnstile-cancel" class="turnstile-cancel-btn">Cancel</button>
    </div>
</div>