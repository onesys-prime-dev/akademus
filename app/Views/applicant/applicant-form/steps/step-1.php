<!-- dashboard/menu/applicant_form_steps/step_1.php -->

<?php 
$program_name = session()->get('program_name');
$program_name = preg_replace('/^Bachelor of Science\s*/', 'BS ', $program_name);
?>

<form 
    method="post"
    id="applicant-form-1"
    class="applicant-form-step"
>

    <!-- SECTION HEADER -->
    <div class="form-section__header">
        <h3>Application Type</h3>
        <p>Select the category that best describes your applicant status, then confirm your program.</p>
    </div>


    <!-- EDUCATION TYPE -->
    <div class="education-options">
        <label class="education-option">
            <input
                type="radio"
                name="applicant_type"
                value="freshmen"
                required
            >
            <div class="education-option__content">
                <h5>Freshmen</h5>
                <span>Senior high school graduate</span>
            </div>
        </label>

        <label class="education-option">
            <input
                type="radio"
                name="applicant_type"
                value="transferee"
                required
            >
            <div class="education-option__content">
                <h5>Transferee</h5>
                <span>From another college or university</span>
            </div>
        </label>

        <label class="education-option">
            <input
                type="radio"
                name="applicant_type"
                value="ladderized"
                required
            >
            <div class="education-option__content">
                <h5>Ladderized</h5>
                <span>TESDA or vocational to degree</span>
            </div>
        </label>
    </div>

    <!-- PROGRAM SECTION -->
    <div class="program-section">
        <div class="program-section__header">
            <h5>Program & Schedule</h5>
        </div>

        <!-- USER INITIAL PROGRAM -->
        <div class="program-note-container">
            <div class="program-note">
                <h6>Your Initial Preferences (from sign-up)</h6>

                <div class="program-choice">
                    <span>1ST CHOICE</span>
                    <p><?= $program_name ?></p>
                </div>
            </div>
        </div>


        <!-- FORM FIELDS -->
        <div class="form-fields">
            <div class="form-field">
                <label class="category-text-color">
                    Confirmed Program
                </label>

                <select
                    name="program_id"
                    id="program"
                    class="high-select"
                    required
                ></select>
            </div>


            <div class="form-field">
                <label class="category-text-color">
                    School Year & Semester
                </label>

                <select name="sy_semester" id="sy-semester" class="high-select" required>
                    <option value="" disabled selected>Select School Year & Semester</option>
                    <option value="SY 2025-2026, 1st Semester">SY 2025-2026, 1st Semester</option>
                    <option value="SY 2025-2026, 2nd Semester">SY 2025-2026, 2nd Semester</option>
                    <option value="SY 2025-2026, Summer">SY 2025-2026, Summer</option>
                </select>

            </div>
        </div>
    </div>

</form>