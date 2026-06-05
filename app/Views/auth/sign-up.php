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

            <!-- SEARCHABLE PROGRAM DROPDOWN -->
            <div class="input-field">
                <label>CHOICE OF PROGRAM</label>

                <!-- Hidden input carries the selected value on submit -->
                <input type="hidden" name="program" id="program-value">

                <div id="prog-wrap" style="position:relative;">

                    <!-- Trigger button (styled to match the existing select) -->
                    <div id="prog-trigger" style="
                        display:flex;align-items:center;justify-content:space-between;
                        padding:0 16px;height:45px;
                        border:1px solid #D9D9D9;
                        border-radius:22px;cursor:pointer;
                        background:rgba(255, 255, 255, 0.6);
                        user-select:none;font-size:14px;
                        opacity:60%;
                    ">
                        <span id="prog-display" style="color:rgb(104, 104, 104, 0.65);font-weight:500;font-size:14px;">Select a Program</span>
                        <svg id="prog-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="rgba(37, 37, 37, 0.55)" stroke-width="2.5"
                             style="flex-shrink:0;transition:transform .2s;">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>

                    <!-- Dropdown panel -->
                    <div id="prog-list" style="
                        display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;
                        background:#fff;border-radius:12px;z-index:9999;
                        box-shadow:0 12px 36px rgba(0,0,0,0.18);
                        border:1.5px solid rgba(0,0,0,0.08);overflow:hidden;
                    ">
                        <!-- Search box -->
                        <div style="padding:10px 10px 8px;border-bottom:1px solid #f0f0f0;background:#fafafa;">
                            <input
                                type="text"
                                id="prog-search"
                                placeholder="Search program..."
                                autocomplete="off"
                                style="
                                    width:100%;padding:8px 12px;box-sizing:border-box;
                                    border:1.5px solid #e0e0e0;border-radius:7px;
                                    font-size:13px;font-family:inherit;outline:none;
                                    color:#1a202c;background:#fff;
                                "
                            >
                        </div>

                        <!-- Options -->
                        <div id="prog-options" style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($programs as $p): ?>
                                <div class="prog-opt"
                                     data-value="<?= $p['id'] ?>"
                                     data-name="<?= esc($p['name']) ?>"
                                     style="padding:11px 16px;cursor:pointer;font-size:14px;color:#1a202c;transition:background .12s;">
                                    <?= esc($p['name']) ?>
                                </div>
                            <?php endforeach; ?>
                            <div id="prog-no-results" style="display:none;padding:14px;text-align:center;color:#999;font-size:13px;">
                                No programs found
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Validation hint shown when empty on submit -->
                <span id="prog-error" style="display:none;color:#f87171;font-size:12px;margin-top:4px;">
                    Please select a program.
                </span>
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
