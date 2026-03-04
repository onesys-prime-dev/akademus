<!-- dashboard/menu/applicant_form_steps/step_1.php -->

<form 
    action=""
    method="post"
    id="applicant-form-1"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Application Type</h3>
        <p>Select the category that best describes your applicant status, then confirm your program.</p>
    </div>

    <div class="education-level-container">
        <label class="education-level-box">
            <input 
                type="radio"
                name="applicant_type"
                value="freshmen"
                required
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
                required
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
                value="transferee"
                required
            >
            <div class="radio-label-container">
                <h5>Ladderized</h5>
                <span>TESDA or vocational to degree</span>
            </div>
        </label>
    </div>

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
                        <p>BS Computer Science</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="application-form-fields-container">
            <div class="application-form-input-field">
                <label class="category-text-color">Confirmed Program</label>
                <select name="program_id" id="program" class="high-select" required>
                    <option value="" disabled>Select a Program</option>
                </select>
            </div>

            <div class="application-form-input-field">
                <label class="category-text-color">School Year & Semester</label>
                <select name="sy_semester" id="sy-semester" class="high-select" required>
                    <option value="" disabled>Select School Year &amp; Semester</option>
                    <option value="SY 2025-2026, 1st Semester">SY 2025-2026, 1st Semester</option>
                    <option value="SY 2025-2026, 2nd Semester">SY 2025-2026, 2nd Semester</option>
                    <option value="SY 2025-2026, Summer">SY 2025-2026, Summer</option>
                </select>
            </div>
        </div>
    </div>
</form>