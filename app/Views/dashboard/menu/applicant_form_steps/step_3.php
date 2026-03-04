<!-- dashboard/menu/applicant_form_steps/step_3.php -->

<form 
    action=""
    method="post"
    id="applicant-form-3"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Family Background</h3>
        <p>Provide parent and guardian information.</p>
    </div>

    <div class="program-sched-container">
        <div class="application-form-fields-container">
            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Father</h5>
                </div>

                <div class="application-form-fields-category-row-container">
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>First Name<span class="required-field">*</span></label>
                            <input type="text" name="father_firstname" placeholder="e.g., Juan" value="<?= esc($data['father_firstname'] ?? '') ?>" required>
                        </div>

                        <div class="application-form-input-field">
                            <label>Middle Name</label>
                            <input type="text" name="father_middlename" placeholder="e.g., Sanchez" value="<?= esc($data['father_middlename'] ?? '') ?>">
                        </div>

                        <div class="application-form-input-field">
                            <label>Last Name<span class="required-field">*</span></label>
                            <input type="text" name="father_lastname" placeholder="e.g., Dela Cruz" value="<?= esc($data['father_lastname'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>Occupation</label>
                            <input type="text" name="father_occupation" placeholder="e.g., Accountant" value="<?= esc($data['father_occupation'] ?? '') ?>">
                        </div>

                        <div class="application-form-input-field medium-field">
                            <label>Contact Number</label>
                            <input type="text" name="father_contact_number" placeholder="+63 9XX XXX XXXX" value="<?= esc($data['father_contact_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Mother</h5>
                </div>

                <div class="application-form-fields-category-row-container">
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>First Name<span class="required-field">*</span></label>
                            <input type="text" name="mother_firstname" placeholder="e.g., Mary" value="<?= esc($data['mother_firstname'] ?? '') ?>" required>
                        </div>

                        <div class="application-form-input-field">
                            <label>Middle Name</label>
                            <input type="text" name="mother_middlename" placeholder="e.g., Alcantara" value="<?= esc($data['mother_middlename'] ?? '') ?>">
                        </div>

                        <div class="application-form-input-field">
                            <label>Maiden Last Name<span class="required-field">*</span></label>
                            <input type="text" name="mother_lastname" placeholder="e.g., Delos Santos" value="<?= esc($data['mother_lastname'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>Occupation</label>
                            <input type="text" name="mother_occupation" placeholder="e.g., Accountant" value="<?= esc($data['mother_occupation'] ?? '') ?>">
                        </div>

                        <div class="application-form-input-field medium-field">
                            <label>Contact Number</label>
                            <input type="text" name="mother_contact_number" placeholder="+63 9XX XXX XXXX" value="<?= esc($data['mother_contact_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color">Emergency Contact</h5>
                </div>

                <div class="application-form-fields-category-row-container">
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>Guardian Name</label>
                            <input type="text" name="guardian_name" placeholder="If Different from Parents" value="<?= esc($data['guardian_name'] ?? '') ?>" required>
                        </div>

                        <div class="application-form-input-field medium-field">
                            <label>Relationship</label>
                            <select name="guardian_relationship">
                                <option value="" disabled selected>Select relationship</option>
                                <option value="Parent">Parent</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Child">Child</option>
                                <option value="Grandparent">Grandparent</option>
                                <option value="Relative">Other Relative</option>
                                <option value="Guardian">Legal Guardian</option>
                                <option value="Partner">Partner</option>
                                <option value="Friend">Friend</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field medium-field">
                            <label>Contact Number</label>
                            <input type="text" name="guardian_contact_number" placeholder="+63 9XX XXX XXXX" value="<?= esc($data['guardian_contact_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="application-form-fields-category">
                <div class="fields-category-header">
                    <h5 class="category-text-color"></h5>
                </div>

                <div class="application-form-fields-category-row-container">
                    <div class="application-form-input-field-row">
                        <div class="application-form-input-field">
                            <label>Annual Family Income</label>
                            <select name="annual_family_income" required>
                                <option value="" disabled selected>Select income range</option>
                                <option value="Below 100000">Below ₱100,000</option>
                                <option value="100000-249999">₱100,000 – ₱249,999</option>
                                <option value="250000-499999">₱250,000 – ₱499,999</option>
                                <option value="500000-749999">₱500,000 – ₱749,999</option>
                                <option value="750000-999999">₱750,000 – ₱999,999</option>
                                <option value="1000000-1499999">₱1,000,000 – ₱1,499,999</option>
                                <option value="1500000-1999999">₱1,500,000 – ₱1,999,999</option>
                                <option value="2000000+">₱2,000,000 and above</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>