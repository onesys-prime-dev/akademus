<?php
helper('form_field');
$fields = get_field_configs(3);
?>

<form 
    action=""
    method="post"
    id="applicant-form-<?= $step_num ?>"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Family Background</h3>
        <p>Provide parent and guardian information.</p>
    </div>

    <div class="program-sched-container">
        <div class="application-form-fields-container">

            <!-- ── FATHER ── -->
            <?php $hasFather = field_visible($fields,'father_firstname') || field_visible($fields,'father_middlename') || field_visible($fields,'father_lastname') || field_visible($fields,'father_occupation') || field_visible($fields,'father_contact_number'); ?>
            <?php if ($hasFather): ?>
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Father</h5>
                </div>

                <div class="application-form-fields-category-row-container">

                    <?php $hasFatherRow1 = field_visible($fields,'father_firstname') || field_visible($fields,'father_middlename') || field_visible($fields,'father_lastname'); ?>
                    <?php if ($hasFatherRow1): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'father_firstname')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'father_firstname', "First Name")) ?>
                                <?php if (field_required($fields, 'father_firstname')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="father_firstname"
                                   placeholder="<?= esc(field_placeholder($fields, 'father_firstname', 'e.g., Juan')) ?>"
                                   value="<?= esc($data['father_firstname'] ?? '') ?>"
                                   <?= field_required($fields, 'father_firstname') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'father_middlename')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'father_middlename', "Middle Name")) ?>
                                <?php if (field_required($fields, 'father_middlename')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="father_middlename"
                                   placeholder="<?= esc(field_placeholder($fields, 'father_middlename', 'e.g., Sanchez')) ?>"
                                   value="<?= esc($data['father_middlename'] ?? '') ?>"
                                   <?= field_required($fields, 'father_middlename') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'father_lastname')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'father_lastname', "Last Name")) ?>
                                <?php if (field_required($fields, 'father_lastname')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="father_lastname"
                                   placeholder="<?= esc(field_placeholder($fields, 'father_lastname', 'e.g., Dela Cruz')) ?>"
                                   value="<?= esc($data['father_lastname'] ?? '') ?>"
                                   <?= field_required($fields, 'father_lastname') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <?php $hasFatherRow2 = field_visible($fields,'father_occupation') || field_visible($fields,'father_contact_number'); ?>
                    <?php if ($hasFatherRow2): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'father_occupation')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'father_occupation', "Occupation")) ?>
                                <?php if (field_required($fields, 'father_occupation')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="father_occupation"
                                   placeholder="<?= esc(field_placeholder($fields, 'father_occupation', 'e.g., Accountant')) ?>"
                                   value="<?= esc($data['father_occupation'] ?? '') ?>"
                                   <?= field_required($fields, 'father_occupation') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'father_contact_number')): ?>
                        <div class="application-form-input-field medium-field">
                            <label>
                                <?= esc(field_label($fields, 'father_contact_number', "Contact Number")) ?>
                                <?php if (field_required($fields, 'father_contact_number')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="father_contact_number"
                                   placeholder="<?= esc(field_placeholder($fields, 'father_contact_number', '+63 9XX XXX XXXX')) ?>"
                                   value="<?= esc($data['father_contact_number'] ?? '') ?>"
                                   <?= field_required($fields, 'father_contact_number') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- ── MOTHER ── -->
            <?php $hasMother = field_visible($fields,'mother_firstname') || field_visible($fields,'mother_middlename') || field_visible($fields,'mother_lastname') || field_visible($fields,'mother_occupation') || field_visible($fields,'mother_contact_number'); ?>
            <?php if ($hasMother): ?>
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Mother</h5>
                </div>

                <div class="application-form-fields-category-row-container">

                    <?php $hasMotherRow1 = field_visible($fields,'mother_firstname') || field_visible($fields,'mother_middlename') || field_visible($fields,'mother_lastname'); ?>
                    <?php if ($hasMotherRow1): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'mother_firstname')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'mother_firstname', "First Name")) ?>
                                <?php if (field_required($fields, 'mother_firstname')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="mother_firstname"
                                   placeholder="<?= esc(field_placeholder($fields, 'mother_firstname', 'e.g., Mary')) ?>"
                                   value="<?= esc($data['mother_firstname'] ?? '') ?>"
                                   <?= field_required($fields, 'mother_firstname') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'mother_middlename')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'mother_middlename', "Middle Name")) ?>
                                <?php if (field_required($fields, 'mother_middlename')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="mother_middlename"
                                   placeholder="<?= esc(field_placeholder($fields, 'mother_middlename', 'e.g., Alcantara')) ?>"
                                   value="<?= esc($data['mother_middlename'] ?? '') ?>"
                                   <?= field_required($fields, 'mother_middlename') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'mother_lastname')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'mother_lastname', "Maiden Last Name")) ?>
                                <?php if (field_required($fields, 'mother_lastname')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="mother_lastname"
                                   placeholder="<?= esc(field_placeholder($fields, 'mother_lastname', 'e.g., Delos Santos')) ?>"
                                   value="<?= esc($data['mother_lastname'] ?? '') ?>"
                                   <?= field_required($fields, 'mother_lastname') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <?php $hasMotherRow2 = field_visible($fields,'mother_occupation') || field_visible($fields,'mother_contact_number'); ?>
                    <?php if ($hasMotherRow2): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'mother_occupation')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'mother_occupation', "Occupation")) ?>
                                <?php if (field_required($fields, 'mother_occupation')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="mother_occupation"
                                   placeholder="<?= esc(field_placeholder($fields, 'mother_occupation', 'e.g., Accountant')) ?>"
                                   value="<?= esc($data['mother_occupation'] ?? '') ?>"
                                   <?= field_required($fields, 'mother_occupation') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'mother_contact_number')): ?>
                        <div class="application-form-input-field medium-field">
                            <label>
                                <?= esc(field_label($fields, 'mother_contact_number', "Contact Number")) ?>
                                <?php if (field_required($fields, 'mother_contact_number')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="mother_contact_number"
                                   placeholder="<?= esc(field_placeholder($fields, 'mother_contact_number', '+63 9XX XXX XXXX')) ?>"
                                   value="<?= esc($data['mother_contact_number'] ?? '') ?>"
                                   <?= field_required($fields, 'mother_contact_number') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- ── EMERGENCY CONTACT / GUARDIAN ── -->
            <?php $hasGuardian = field_visible($fields,'guardian_name') || field_visible($fields,'guardian_relationship') || field_visible($fields,'guardian_contact_number'); ?>
            <?php if ($hasGuardian): ?>
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Emergency Contact</h5>
                </div>

                <div class="application-form-fields-category-row-container">

                    <?php $hasGuardianRow1 = field_visible($fields,'guardian_name') || field_visible($fields,'guardian_relationship'); ?>
                    <?php if ($hasGuardianRow1): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'guardian_name')): ?>
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'guardian_name', 'Guardian Name')) ?>
                                <?php if (field_required($fields, 'guardian_name')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="guardian_name"
                                   placeholder="<?= esc(field_placeholder($fields, 'guardian_name', 'If Different from Parents')) ?>"
                                   value="<?= esc($data['guardian_name'] ?? '') ?>"
                                   <?= field_required($fields, 'guardian_name') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'guardian_relationship')): ?>
                        <div class="application-form-input-field medium-field">
                            <label>
                                <?= esc(field_label($fields, 'guardian_relationship', 'Relationship')) ?>
                                <?php if (field_required($fields, 'guardian_relationship')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <select name="guardian_relationship" <?= field_required($fields, 'guardian_relationship') ? 'required' : '' ?>>
                                <option value="" disabled selected>Select relationship</option>
                                <?php
                                $relOpts = ['Parent','Spouse','Sibling','Child','Grandparent','Relative'=>'Other Relative','Guardian'=>'Legal Guardian','Partner','Friend','Other'];
                                foreach ($relOpts as $val => $label):
                                    $v = is_string($val) ? $val : $label;
                                    $l = $label;
                                    $sel = ($data['guardian_relationship'] ?? '') === $v ? 'selected' : '';
                                ?>
                                <option value="<?= esc($v) ?>" <?= $sel ?>><?= esc($l) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <?php if (field_visible($fields, 'guardian_contact_number')): ?>
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field medium-field">
                            <label>
                                <?= esc(field_label($fields, 'guardian_contact_number', 'Contact Number')) ?>
                                <?php if (field_required($fields, 'guardian_contact_number')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="guardian_contact_number"
                                   placeholder="<?= esc(field_placeholder($fields, 'guardian_contact_number', '+63 9XX XXX XXXX')) ?>"
                                   value="<?= esc($data['guardian_contact_number'] ?? '') ?>"
                                   <?= field_required($fields, 'guardian_contact_number') ? 'required' : '' ?>>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- ── ANNUAL FAMILY INCOME ── -->
            <?php if (field_visible($fields, 'annual_family_income')): ?>
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color"></h5>
                </div>

                <div class="application-form-fields-category-row-container">
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>
                                <?= esc(field_label($fields, 'annual_family_income', 'Annual Family Income')) ?>
                                <?php if (field_required($fields, 'annual_family_income')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <select name="annual_family_income" <?= field_required($fields, 'annual_family_income') ? 'required' : '' ?>>
                                <option value="" disabled selected>Select income range</option>
                                <?php
                                $incomeOpts = [
                                    'Below 100000'    => 'Below ₱100,000',
                                    '100000-249999'   => '₱100,000 – ₱249,999',
                                    '250000-499999'   => '₱250,000 – ₱499,999',
                                    '500000-749999'   => '₱500,000 – ₱749,999',
                                    '750000-999999'   => '₱750,000 – ₱999,999',
                                    '1000000-1499999' => '₱1,000,000 – ₱1,499,999',
                                    '1500000-1999999' => '₱1,500,000 – ₱1,999,999',
                                    '2000000+'        => '₱2,000,000 and above',
                                    'Prefer not to say' => 'Prefer not to say',
                                ];
                                foreach ($incomeOpts as $val => $lbl):
                                    $sel = ($data['annual_family_income'] ?? '') === $val ? 'selected' : '';
                                ?>
                                <option value="<?= esc($val) ?>" <?= $sel ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>