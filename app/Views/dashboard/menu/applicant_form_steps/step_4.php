<!-- dashboard/menu/applicant_form_steps/step_4.php -->

<form 
    action=""
    method="post"
    id="applicant-form-4"
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
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label class="category-text-color">SCHOOL NAME</label>
                            <input type="text" name="school_name" placeholder="Full Name of your Senior High School" value="<?= esc($data['school_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label class="category-text-color">LOCATION</label>
                            <input type="text" name="school_location" placeholder="City, Province" value="<?= esc($data['school_location'] ?? '') ?>" required>
                        </div>

                        <div class="application-form-input-field">
                            <label class="category-text-color">TRACK / STRAND</label>
                            <select name="strand" required>
                                <option value="" disabled selected>Select track / strand</option>

                                <!-- Academic Track -->
                                <option value="STEM">Academic – STEM (Science, Technology, Engineering & Mathematics)</option>
                                <option value="ABM">Academic – ABM (Accountancy, Business & Management)</option>
                                <option value="HUMSS">Academic – HUMSS (Humanities & Social Sciences)</option>
                                <option value="GAS">Academic – GAS (General Academic Strand)</option>

                                <!-- TVL Track -->
                                <option value="TVL-ICT">TVL – ICT (Information & Communications Technology)</option>
                                <option value="TVL-HE">TVL – Home Economics</option>
                                <option value="TVL-IA">TVL – Industrial Arts</option>
                                <option value="TVL-AFA">TVL – Agri-Fishery Arts</option>

                                <!-- Other Tracks -->
                                <option value="Arts and Design">Arts & Design Track</option>
                                <option value="Sports">Sports Track</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">SCHOOL YEAR</label>
                            <input type="text" name="school_year" placeholder="e.g., 2025 - 2026" value="<?= esc($data['school_year'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">GENERAL AVERAGE (GWA)</label>
                            <input type="text" name="gwa" placeholder="e.g., 92.3" value="<?= esc($data['gwa'] ?? '') ?>" required>
                        </div>

                        <div class="application-form-input-field medium-field">
                            <label class="category-text-color">SCHOOL TYPE</label>
                            <select name="school_type" required>
                                <option value="" disabled selected>Select school type</option>
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                                <option value="International">International</option>
                                <option value="State University/College">State University / College (SUC)</option>
                                <option value="Local University/College">Local University / College (LUC)</option>
                                <option value="Technical/Vocational">Technical / Vocational School</option>
                                <option value="Homeschool">Homeschool</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>