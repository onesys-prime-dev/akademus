<!-- dashboard/menu/applicant_form_steps/step_2.php -->


<form 
    action=""
    method="post"
    id="applicant-form-2"
    class="applicant-form-step"    
>

    <!-- SECTION HEADER -->
    <div class="form-section__header">
        <h3>Personal Information</h3>
        <p>Please complete the fields below to ensure our records are accurate and up-to-date.</p>
    </div>

    <!-- FORM FIELDS -->
    <div class="form-fields">
        <div class="form-row">
            <div class="form-field">
                <label>First Name<span class="required">*</span></label>
                <input type="text" name="firstname" placeholder="e.g., Juan" value="" required>
            </div>

            <div class="form-field">
                <label>Middle Name</label>
                <input type="text" name="middlename" placeholder="e.g., Sanchez" value="">
            </div>

            <div class="form-field">
                <label>Last Name<span class="required">*</span></label>
                <input type="text" name="lastname" placeholder="e.g., Dela Cruz" value="" required>
            </div>

            <div class="form-field field-sm">
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

        <div class="form-row">
            <div class="form-field field-md">
                <label>Gender<span class="required">*</span></label>
                <select name="gender" id="gender" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Non-binary">Non-binary</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                    <option value="Other">Other</option>
                </select>
            </div>
                
            <div class="form-field field-md">
                <label>Date of Birth<span class="required">*</span></label>
                <input type="date" name="date_of_birth" placeholder="mm/dd/yy" value="" required>
            </div>

            <div class="form-field">
                <label>Place of Birth<span class="required">*</span></label>
                <input type="text" name="place_of_birth" placeholder="City, Province" value="" required>
            </div>
                
            <div class="form-field field-md">
                <label>Citizenship<span class="required">*</span></label>
                <input type="text" name="citizenship" placeholder="e.g., Filipino" value="" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-field field-lg">
                <label>Home Address<span class="required">*</span></label>
                <input type="text" name="home_address" placeholder="Street, Barangay, City, Province" value="" required>
            </div>
                
            <div class="form-field field-md">
                <label>Contact Number<span class="required">*</span></label>
                <input type="text" name="contact_number" placeholder="+63 9XX XXX XXXX" value="" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-field field-lg">
                <label>Email Address<span class="required">*</span></label>
                <input type="email" name="email_address" placeholder="e.g., sample@email.com" value="" required>
            </div>
        </div>
    </div>
</form>