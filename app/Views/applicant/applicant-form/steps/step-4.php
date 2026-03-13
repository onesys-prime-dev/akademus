<?php
helper('form_field');
$fields = get_field_configs(4);
?>

<form 
    action=""
    method="post"
    id="applicant-form-<?= $step_num ?>"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Educational Background</h3>
        <p>Provide your most recent educational history.</p>
    </div>

    <div class="program-sched-container">
        <div class="application-form-fields-container">
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-span">SENIOR HIGH SCHOOL</h5>
                </div>

                <div class="application-form-fields-category-row-container">

                    <?php if (field_visible($fields, 'school_name')): ?>
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label class="category-text-color">
                                <?= esc(field_label($fields, 'school_name', 'SCHOOL NAME')) ?>
                                <?php if (field_required($fields, 'school_name')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="school_name"
                                   placeholder="<?= esc(field_placeholder($fields, 'school_name', 'Full Name of your Senior High School')) ?>"
                                   value="<?= esc($data['school_name'] ?? '') ?>"
                                   <?= field_required($fields, 'school_name') ? 'required' : '' ?>>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php $hasRow2 = field_visible($fields,'school_location') || field_visible($fields,'strand') || field_visible($fields,'school_year_start') || field_visible($fields,'school_year_end'); ?>
                    <?php if ($hasRow2): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'school_location')): ?>
                        <div class="application-form-input-field">
                            <label class="category-text-color">
                                <?= esc(field_label($fields, 'school_location', 'LOCATION')) ?>
                                <?php if (field_required($fields, 'school_location')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="school_location"
                                   placeholder="<?= esc(field_placeholder($fields, 'school_location', 'City, Province')) ?>"
                                   value="<?= esc($data['school_location'] ?? '') ?>"
                                   <?= field_required($fields, 'school_location') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'strand')): ?>
                        <div class="application-form-input-field">
                            <label class="category-text-color">
                                <?= esc(field_label($fields, 'strand', 'TRACK / STRAND')) ?>
                                <?php if (field_required($fields, 'strand')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <select name="strand" <?= field_required($fields, 'strand') ? 'required' : '' ?>>
                                <option value="" disabled selected>Select track / strand</option>
                                <?php
                                $strands = [
                                    'STEM'        => 'Academic – STEM (Science, Technology, Engineering & Mathematics)',
                                    'ABM'         => 'Academic – ABM (Accountancy, Business & Management)',
                                    'HUMSS'       => 'Academic – HUMSS (Humanities & Social Sciences)',
                                    'GAS'         => 'Academic – GAS (General Academic Strand)',
                                    'TVL-ICT'     => 'TVL – ICT (Information & Communications Technology)',
                                    'TVL-HE'      => 'TVL – Home Economics',
                                    'TVL-IA'      => 'TVL – Industrial Arts',
                                    'TVL-AFA'     => 'TVL – Agri-Fishery Arts',
                                    'Arts and Design' => 'Arts & Design Track',
                                    'Sports'      => 'Sports Track',
                                    'Other'       => 'Other',
                                ];
                                foreach ($strands as $val => $lbl):
                                    $sel = ($data['strand'] ?? '') === $val ? 'selected' : '';
                                ?>
                                <option value="<?= esc($val) ?>" <?= $sel ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'school_year_start') || field_visible($fields, 'school_year_end')): ?>
                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">
                                SCHOOL YEAR
                                <?php if (field_required($fields, 'school_year_start')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <div style="display:flex; gap:6px; align-items:center;">
                                <?php if (field_visible($fields, 'school_year_start')): ?>
                                <input type="number" name="school_year_start"
                                       placeholder="<?= esc(field_placeholder($fields, 'school_year_start', 'e.g. 2024')) ?>"
                                       value="<?= esc($data['school_year_start'] ?? '') ?>"
                                       min="2000" max="2099" style="width:90px;"
                                       <?= field_required($fields, 'school_year_start') ? 'required' : '' ?>>
                                <?php endif; ?>
                                <?php if (field_visible($fields, 'school_year_start') && field_visible($fields, 'school_year_end')): ?>
                                <span style="color:#aaa;">–</span>
                                <?php endif; ?>
                                <?php if (field_visible($fields, 'school_year_end')): ?>
                                <input type="number" name="school_year_end"
                                       placeholder="<?= esc(field_placeholder($fields, 'school_year_end', 'e.g. 2025')) ?>"
                                       value="<?= esc($data['school_year_end'] ?? '') ?>"
                                       min="2000" max="2099" style="width:90px;"
                                       <?= field_required($fields, 'school_year_end') ? 'required' : '' ?>>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <?php $hasRow3 = field_visible($fields,'gwa') || field_visible($fields,'school_type'); ?>
                    <?php if ($hasRow3): ?>
                    <div class="application-form-input-field-row">

                        <?php if (field_visible($fields, 'gwa')): ?>
                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">
                                <?= esc(field_label($fields, 'gwa', 'GENERAL AVERAGE (GWA)')) ?>
                                <?php if (field_required($fields, 'gwa')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <input type="text" name="gwa"
                                   placeholder="<?= esc(field_placeholder($fields, 'gwa', 'e.g., 92.3')) ?>"
                                   value="<?= esc($data['gwa'] ?? '') ?>"
                                   <?= field_required($fields, 'gwa') ? 'required' : '' ?>>
                        </div>
                        <?php endif; ?>

                        <?php if (field_visible($fields, 'school_type')): ?>
                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">
                                <?= esc(field_label($fields, 'school_type', 'SCHOOL TYPE')) ?>
                                <?php if (field_required($fields, 'school_type')): ?><span class="required-field">*</span><?php endif; ?>
                            </label>
                            <select name="school_type" <?= field_required($fields, 'school_type') ? 'required' : '' ?>>
                                <option value="" disabled selected>Select school type</option>
                                <?php
                                $schoolTypes = [
                                    'Public'                  => 'Public',
                                    'Private'                 => 'Private',
                                    'International'           => 'International',
                                    'State University/College'=> 'State University / College (SUC)',
                                    'Local University/College'=> 'Local University / College (LUC)',
                                    'Technical/Vocational'    => 'Technical / Vocational School',
                                    'Homeschool'              => 'Homeschool',
                                    'Other'                   => 'Other',
                                ];
                                foreach ($schoolTypes as $val => $lbl):
                                    $sel = ($data['school_type'] ?? '') === $val ? 'selected' : '';
                                ?>
                                <option value="<?= esc($val) ?>" <?= $sel ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</form>