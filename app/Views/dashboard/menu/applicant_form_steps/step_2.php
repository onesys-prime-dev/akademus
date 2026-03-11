<!-- dashboard/menu/applicant_form_steps/step_2.php -->


<form 
    action=""
    method="post"
    id="applicant-form-2"
    class="applicant-form"    
>
    <div class="form-header-text">
        <h3>Personal Information</h3>
        <p>Please complete the fields below to ensure our records are accurate and up-to-date.</p>
    </div>

    <div class="program-sched-container">
        <div class="application-form-fields-container">
            <div class="application-form-input-field-row">
                <div class="application-form-input-field">
                    <label>First Name<span class="required-field">*</span></label>
                    <input type="text" name="firstname" placeholder="e.g., Juan" value="" required>
                </div>

                <div class="application-form-input-field">
                    <label>Middle Name</label>
                    <input type="text" name="middlename" placeholder="e.g., Sanchez" value="">
                </div>

                <div class="application-form-input-field">
                    <label>Last Name<span class="required-field">*</span></label>
                    <input type="text" name="lastname" placeholder="e.g., Dela Cruz" value="" required>
                </div>

                <div class="application-form-input-field small-field">
                    <label>Suffix</label>
                    <select name="suffix" id="suffix">
                        <option value="" selected>None</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                    </select>
                </div>
            </div>

            <div class="application-form-input-field-row">
                <div class="application-form-input-field medium-field">
                    <label>Gender<span class="required-field">*</span></label>
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Non-binary">Non-binary</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="application-form-input-field medium-field">
                    <label>Date of Birth<span class="required-field">*</span></label>
                    <input type="date" name="date_of_birth" placeholder="mm/dd/yy" value="" required>
                </div>

                <div class="application-form-input-field">
                    <label>Place of Birth<span class="required-field">*</span></label>
                    <input type="text" name="place_of_birth" placeholder="City, Province" value="" required>
                </div>
                
                <div class="application-form-input-field medium-field">
                    <label>Citizenship<span class="required-field">*</span></label>
                    <input type="text" name="citizenship" placeholder="e.g., Filipino" value="" required>
                </div>
            </div>

            <div class="application-form-input-field-row">
                <div class="application-form-input-field large-field">
                    <label>Home Address<span class="required-field">*</span></label>
                    <input type="text" name="home_address" placeholder="Street, Barangay, City, Province" value="" required>
                </div>
                
                <div class="application-form-input-field medium-field" style="position: relative;">
                    <label>Contact Number<span class="required-field">*</span></label>
                    <div class="phone-input-wrapper">
                        <select id="phone_code" name="phone_code">
                            <option value="+63" selected>🇵🇭 +63</option>
                            <option value="+1">🇺🇸 +1</option>
                            <option value="+44">🇬🇧 +44</option>
                            <option value="+81">🇯🇵 +81</option>
                            <option value="+65">🇸🇬 +65</option>
                            <option value="+61">🇦🇺 +61</option>
                            <option value="+82">🇰🇷 +82</option>
                            <option value="+86">🇨🇳 +86</option>
                            <option value="+91">🇮🇳 +91</option>
                            <option value="+971">🇦🇪 +971</option>
                        </select>
                    </div>
                    <input 
                        type="tel" 
                        id="contact_number"
                        name="contact_number"
                        placeholder="+63 9XX XXX XXXX" 
                        maxlength="15"
                        value="" 
                        required>
                    <small id="contact-error" class="contact-error-tooltip"></small>
                </div>
            </div>

            <div class="application-form-input-field-row">
                <div class="application-form-input-field large-field">
                    <label>Email Address<span class="required-field">*</span></label>
                    <input type="email" name="email_address" placeholder="e.g., sample@email.com" value="" required>
                </div>
            </div>
        </div>
    </div>
</form>
