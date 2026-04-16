<?php
helper('form_field');
$fields = get_field_configs(1);

$program = session()->get('program_name') ?? '';

$programName = 'BS ' . preg_replace(
    '/^Bachelor of Science in (.+)$/i',
    '$1',
    $program
);
?>

<form 
    action=""
    method="post"
    id="applicant-form-<?= $step_num ?>"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Application Type</h3>
        <p>Select the category that best describes your applicant status, then confirm your program.</p>
    </div>

    <?php if (field_visible($fields, 'applicant_type')): ?>
    <div class="education-level-container">
        <label class="education-level-box">
            <input 
                type="radio"
                name="applicant_type"
                value="freshmen"
                <?= field_required($fields, 'applicant_type') ? 'required' : '' ?>
            >
            <div class="radio-label-container">
                <h5>Freshmen</h5>
                <span>Senior high school graduate</span>
            </div>
        </label>

        <label class="education-level-box">
            <input 
                type="radio"
                name="applicant_type"
                value="transferee"
                <?= field_required($fields, 'applicant_type') ? 'required' : '' ?>
            >
            <div class="radio-label-container">
                <h5>Transferee</h5>
                <span>From another college or university</span>
            </div>
        </label>

        <label class="education-level-box">
            <input 
                type="radio"
                name="applicant_type"
                value="ladderized"
                <?= field_required($fields, 'applicant_type') ? 'required' : '' ?>
            >
            <div class="radio-label-container">
                <h5>Ladderized</h5>
                <span>TESDA or vocational to degree</span>
            </div>
        </label>
    </div>
    <?php endif; ?>

    <div class="program-sched-container">
        <div class="program-sched-header">
            <h5>Program & Schedule</h5>
        </div>

        <div class="note-and-input-container">
            <div class="program-sched-note">
                <h6>Your Initial Preferences (from sign-up)</h6>     
                
                <div class="program-choice-box">
                    <div class="program-choice">
                        <span>1ST CHOICE</span>
                        <p><?= $programName ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="application-form-fields-container">

            <?php if (field_visible($fields, 'program_id')): ?>
            <div class="application-form-input-field">
                <label class="category-text-color">
                    <?= esc(field_label($fields, 'program_id', 'Confirmed Program')) ?>
                    <?php if (field_required($fields, 'program_id')): ?><span class="required-field">*</span><?php endif; ?>
                </label>
                <select name="program_id" id="program" class="high-select" <?= field_required($fields, 'program_id') ? 'required' : '' ?>>
                </select>
            </div>
            <?php endif; ?>

            <?php if (field_visible($fields, 'school_year')): ?>
            <div class="application-form-input-field">
                <label class="category-text-color">
                    <?= esc(field_label($fields, 'school_year', 'School Year')) ?>
                    <?php if (field_required($fields, 'school_year')): ?><span class="required-field">*</span><?php endif; ?>
                </label>
                <select name="school_year" id="school-year" class="high-select" <?= field_required($fields, 'school_year') ? 'required' : '' ?>>
                    <option value="" disabled selected>Select School Year</option>
                    <option value="2025-2026">2025-2026</option>
                    <option value="2026-2027">2026-2027</option>
                </select>
            </div>
            <?php endif; ?>

            <?php if (field_visible($fields, 'semester')): ?>
            <div class="application-form-input-field">
                <label class="category-text-color">
                    <?= esc(field_label($fields, 'semester', 'Semester')) ?>
                    <?php if (field_required($fields, 'semester')): ?><span class="required-field">*</span><?php endif; ?>
                </label>
                <select name="semester" id="semester" class="high-select" <?= field_required($fields, 'semester') ? 'required' : '' ?>>
                    <option value="" disabled selected>Select Semester</option>
                    <option value="1st Semester">1st Semester</option>
                    <option value="2nd Semester">2nd Semester</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>