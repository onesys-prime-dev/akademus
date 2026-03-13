<?php
helper('form_field');
$fields = get_field_configs(2);
?>

<form 
    action=""
    method="post"
    id="applicant-form-<?= $step_num ?>"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Personal Information</h3>
        <p>Please complete the fields below to ensure our records are accurate and up-to-date.</p>
    </div>

    <div class="program-sched-container">
        <div class="application-form-fields-container">

            <!-- ROW: Name fields -->
            <?php $hasNameField = field_visible($fields,'firstname') || field_visible($fields,'middlename') || field_visible($fields,'lastname') || field_visible($fields,'suffix'); ?>
            <?php if ($hasNameField): ?>
            <div class="application-form-input-field-row">

                <?php if (field_visible($fields, 'firstname')): ?>
                <div class="application-form-input-field">
                    <label>
                        <?= esc(field_label($fields, 'firstname', 'First Name')) ?>
                        <?php if (field_required($fields, 'firstname')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="firstname"
                           placeholder="<?= esc(field_placeholder($fields, 'firstname', 'e.g., Juan')) ?>"
                           value="<?= esc($data['firstname'] ?? '') ?>"
                           <?= field_required($fields, 'firstname') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'middlename')): ?>
                <div class="application-form-input-field">
                    <label>
                        <?= esc(field_label($fields, 'middlename', 'Middle Name')) ?>
                        <?php if (field_required($fields, 'middlename')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="middlename"
                           placeholder="<?= esc(field_placeholder($fields, 'middlename', 'e.g., Sanchez')) ?>"
                           value="<?= esc($data['middlename'] ?? '') ?>"
                           <?= field_required($fields, 'middlename') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'lastname')): ?>
                <div class="application-form-input-field">
                    <label>
                        <?= esc(field_label($fields, 'lastname', 'Last Name')) ?>
                        <?php if (field_required($fields, 'lastname')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="lastname"
                           placeholder="<?= esc(field_placeholder($fields, 'lastname', 'e.g., Dela Cruz')) ?>"
                           value="<?= esc($data['lastname'] ?? '') ?>"
                           <?= field_required($fields, 'lastname') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'suffix')): ?>
                <div class="application-form-input-field small-field">
                    <label>
                        <?= esc(field_label($fields, 'suffix', 'Suffix')) ?>
                        <?php if (field_required($fields, 'suffix')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <select name="suffix" id="suffix" <?= field_required($fields, 'suffix') ? 'required' : '' ?>>
                        <option value="" selected>None</option>
                        <option value="Jr." <?= ($data['suffix'] ?? '') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                        <option value="Sr." <?= ($data['suffix'] ?? '') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                        <option value="II"  <?= ($data['suffix'] ?? '') === 'II'  ? 'selected' : '' ?>>II</option>
                        <option value="III" <?= ($data['suffix'] ?? '') === 'III' ? 'selected' : '' ?>>III</option>
                        <option value="IV"  <?= ($data['suffix'] ?? '') === 'IV'  ? 'selected' : '' ?>>IV</option>
                        <option value="V"   <?= ($data['suffix'] ?? '') === 'V'   ? 'selected' : '' ?>>V</option>
                    </select>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <!-- ROW: Gender, DOB, Place of Birth, Citizenship -->
            <?php $hasDetailsRow1 = field_visible($fields,'gender') || field_visible($fields,'date_of_birth') || field_visible($fields,'place_of_birth') || field_visible($fields,'citizenship'); ?>
            <?php if ($hasDetailsRow1): ?>
            <div class="application-form-input-field-row">

                <?php if (field_visible($fields, 'gender')): ?>
                <div class="application-form-input-field medium-field">
                    <label>
                        <?= esc(field_label($fields, 'gender', 'Gender')) ?>
                        <?php if (field_required($fields, 'gender')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <select name="gender" id="gender" <?= field_required($fields, 'gender') ? 'required' : '' ?>>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male"              <?= ($data['gender'] ?? '') === 'Male'              ? 'selected' : '' ?>>Male</option>
                        <option value="Female"            <?= ($data['gender'] ?? '') === 'Female'            ? 'selected' : '' ?>>Female</option>
                        <option value="Non-binary"        <?= ($data['gender'] ?? '') === 'Non-binary'        ? 'selected' : '' ?>>Non-binary</option>
                        <option value="Prefer not to say" <?= ($data['gender'] ?? '') === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                        <option value="Other"             <?= ($data['gender'] ?? '') === 'Other'             ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'date_of_birth')): ?>
                <div class="application-form-input-field medium-field">
                    <label>
                        <?= esc(field_label($fields, 'date_of_birth', 'Date of Birth')) ?>
                        <?php if (field_required($fields, 'date_of_birth')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="date" name="date_of_birth"
                           value="<?= esc($data['date_of_birth'] ?? '') ?>"
                           <?= field_required($fields, 'date_of_birth') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'place_of_birth')): ?>
                <div class="application-form-input-field">
                    <label>
                        <?= esc(field_label($fields, 'place_of_birth', 'Place of Birth')) ?>
                        <?php if (field_required($fields, 'place_of_birth')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="place_of_birth"
                           placeholder="<?= esc(field_placeholder($fields, 'place_of_birth', 'City, Province')) ?>"
                           value="<?= esc($data['place_of_birth'] ?? '') ?>"
                           <?= field_required($fields, 'place_of_birth') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'citizenship')): ?>
                <div class="application-form-input-field medium-field">
                    <label>
                        <?= esc(field_label($fields, 'citizenship', 'Citizenship')) ?>
                        <?php if (field_required($fields, 'citizenship')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="citizenship"
                           placeholder="<?= esc(field_placeholder($fields, 'citizenship', 'e.g., Filipino')) ?>"
                           value="<?= esc($data['citizenship'] ?? '') ?>"
                           <?= field_required($fields, 'citizenship') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <!-- ROW: Home Address, Contact Number -->
            <?php $hasDetailsRow2 = field_visible($fields,'home_address') || field_visible($fields,'contact_number'); ?>
            <?php if ($hasDetailsRow2): ?>
            <div class="application-form-input-field-row">

                <?php if (field_visible($fields, 'home_address')): ?>
                <div class="application-form-input-field large-field">
                    <label>
                        <?= esc(field_label($fields, 'home_address', 'Home Address')) ?>
                        <?php if (field_required($fields, 'home_address')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="home_address"
                           placeholder="<?= esc(field_placeholder($fields, 'home_address', 'Street, Barangay, City, Province')) ?>"
                           value="<?= esc($data['home_address'] ?? '') ?>"
                           <?= field_required($fields, 'home_address') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

                <?php if (field_visible($fields, 'contact_number')): ?>
                <div class="application-form-input-field medium-field">
                    <label>
                        <?= esc(field_label($fields, 'contact_number', 'Contact Number')) ?>
                        <?php if (field_required($fields, 'contact_number')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="text" name="contact_number"
                           placeholder="<?= esc(field_placeholder($fields, 'contact_number', '+63 9XX XXX XXXX')) ?>"
                           value="<?= esc($data['contact_number'] ?? '') ?>"
                           <?= field_required($fields, 'contact_number') ? 'required' : '' ?>>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <!-- ROW: Email Address -->
            <?php if (field_visible($fields, 'email_address')): ?>
            <div class="application-form-input-field-row">
                <div class="application-form-input-field large-field">
                    <label>
                        <?= esc(field_label($fields, 'email_address', 'Email Address')) ?>
                        <?php if (field_required($fields, 'email_address')): ?><span class="required-field">*</span><?php endif; ?>
                    </label>
                    <input type="email" name="email_address"
                           placeholder="<?= esc(field_placeholder($fields, 'email_address', 'e.g., sample@email.com')) ?>"
                           value="<?= esc($data['email_address'] ?? '') ?>"
                           <?= field_required($fields, 'email_address') ? 'required' : '' ?>>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>