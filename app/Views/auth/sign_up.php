<!-- auth/sign_up.php -->

<!-- auth/sign_up.php -->

<div>
    <form 
        id="sign-up-form"
        class="form-container"
        action="<?= base_url('sign_up/register') ?>" 
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
                <label for="program">1ST CHOICE OF PROGRAM</label>
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

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="note-container">
            <span>Note:</span>
            <p>Your final program will be confirmed in the Applicant Information Form. These preferences help us process your application.</p>
        </div>

        <button type="submit" id="sign-up-button" class="submit-btn">CREATE ACCOUNT</button>
    </form>
</div>