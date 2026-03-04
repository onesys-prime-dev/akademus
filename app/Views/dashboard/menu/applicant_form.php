<!-- dashboard/menu/applicant_form.php  -->

<div class="applicant-form-container">
    <!-- Hello, this is the APPLICANT FORM. -->

    <div class="applicant-form-progress-container">
        <h4 id="form-progress-h4-text"></h4>

        <div class="progress-timeline-container">
            <div class="progress-box">
                <div class="progress-circle">1</div>
                <span>App. Type</span>
            </div>

            <div class="progress-line"></div>

            <div class="progress-box">
                <div class="progress-circle">2</div>
                <span>Personal</span>
            </div>

            <div class="progress-line"></div>

            <div class="progress-box">
                <div class="progress-circle">3</div>
                <span>Family</span>
            </div>

            <div class="progress-line"></div>

            <div class="progress-box">
                <div class="progress-circle">4</div>
                <span>Education</span>
            </div>
        </div>
    </div>

    <div class="applicant-form-content-container">
        <div id="form-fillup-container" class="applicant-form-fillup-container">
            <!-- sign-in and sign-up forms will be loaded here via AJAX -->
            <!-- the pages that will be loaded will be coming from: dashboard/menu/applicant_form_steps/step_{step}.php -->
             
        </div>

        <div class="applicant-form-lower-actions">
            <span>Auto-saved as draft</span>
            
            <div class="applicant-form-lower-actions-btn">
                <button type="button" id="back-btn" class="btn-primary">Back</button>
                <button type="button" id="save-and-continue-btn" class="btn-primary">Save & Continue</button>
                <button type="button" id="submit-form" class="btn-primary">Submit Form</button>
            </div>
        </div>
    </div>
</div>

<script>
    const formProgressH4Text = document.getElementById('form-progress-h4-text');
    const backBtn = document.getElementById('back-btn');
    const saveAndContinueBtn = document.getElementById('save-and-continue-btn');
    const submitFormBtn = document.getElementById('submit-form');
    const formFillupContainer = document.getElementById('form-fillup-container');

    let currentStep = <?= isset($initial_step) ? $initial_step : 1 ?>;
    const maxSteps = <?= isset($max_steps) ? $max_steps : 4 ?>;

    async function loadForm(step) {
        try {
            const response = await fetch(`<?= base_url('applicant-form/step/') ?>${step}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            document.title = "Akademus | " + data.title;
            formProgressH4Text.textContent = `Form Progress — Step ${step} of ${maxSteps}`;
            formFillupContainer.innerHTML = data.form;

            populateForm(`applicant-form-${step}`, data.formData);

            // Only load programs if step 1
            if (step === 1) {
                loadPrograms(data.selected_program_id);
            }

            const base = '<?= base_url() ?>';
            history.replaceState(null, '', `${base}applicant-form/step/${step}`);

        } catch (error) {
            console.error("Load form error:", error);
        }
    }

    function populateForm(formId, data) {
        const form = document.getElementById(formId);
        if (!form || !data) return;

        Object.keys(data).forEach(key => {
            const value = data[key];

            if (value === null || value === undefined) return;

            const field = form.querySelector(`[name="${key}"]`);
            if (!field) return;

            // RADIO
            if (field.type === 'radio') {
                const radios = form.querySelectorAll(`[name="${key}"]`);
                radios.forEach(radio => {
                    if (radio.value == value) {
                        radio.checked = true;
                    }
                });
            }

            // CHECKBOX
            else if (field.type === 'checkbox') {
                field.checked = !!value;
            }

            // SELECT
            else if (field.tagName === 'SELECT') {
                field.value = value;
            }

            // TEXT / DATE / EMAIL / TEXTAREA
            else {
                field.value = value;
            }
        });
    }

    async function loadPrograms(selectedId = null) {
        try {
            const response = await fetch(`<?= base_url('get-programs') ?>`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            if (data.programs) {
                const select = document.getElementById('program');
                select.innerHTML = '<option value="" disabled>Select a Program</option>';

                data.programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.id;
                    option.textContent = program.name;

                    // ✅ Auto-select if matches saved data
                    if (selectedId && selectedId == program.id) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load programs:', error);
        }
    }

    async function processForm(step, action = 'draft') {
        const applicantForm = document.getElementById(`applicant-form-${step}`);
        if (!applicantForm) return;

        const formData = new FormData(applicantForm);
        formData.append('action', action);

        try {
            const response = await fetch(
                `<?= base_url('applicant-form/update/') ?>${step}`, 
                {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }    
                }
            );

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            if (data.status === 'success') {
                return true;
            } else {
                alert(data.message || 'Something went wrong.');
                return false;
            }

        } catch (error) {
            console.error('Form submission error:', error);
            alert('An error occurred while saving.');
            return false;
        }
    }

    function checkAllFields(step) {
        const form = document.getElementById(`applicant-form-${step}`);
        if (!form) return false;

        let isValid = true;

        // Remove previous error highlights
        form.querySelectorAll('.input-error').forEach(el => {
            el.classList.remove('input-error');
        });

        // Get all required inputs, selects, and textareas
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {

            // RADIO GROUP VALIDATION
            if (field.type === 'radio') {
                const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                const isChecked = [...radioGroup].some(radio => radio.checked);

                if (!isChecked) {
                    isValid = false;
                    radioGroup.forEach(radio => {
                        radio.closest('label')?.classList.add('input-error');
                    });
                }
            }

            // CHECKBOX (if you add later)
            else if (field.type === 'checkbox') {
                if (!field.checked) {
                    isValid = false;
                    field.classList.add('input-error');
                }
            }

            // SELECT
            else if (field.tagName === 'SELECT') {
                if (!field.value) {
                    isValid = false;
                    field.classList.add('input-error');
                }
            }

            // TEXT / DATE / EMAIL / ETC
            else {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('input-error');
                }
            }
        });

        if (!isValid) {
            alert('Please fill out all required fields before proceeding.');
        }

        return isValid;
    }

    function updateButtons() {
        backBtn.classList.toggle('hidden', currentStep === 1);
        saveAndContinueBtn.classList.toggle('hidden', currentStep === maxSteps);
        submitFormBtn.classList.toggle('hidden', currentStep !== maxSteps);
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await loadForm(currentStep);
        updateButtons();
    });

    // Buttons
    // UNCOMMENT THIS IF YOU WILL PROCEED TO FORM SUBMISSION. For now, use the saveAndContinueBtn listeners below if only for demo.
    saveAndContinueBtn.addEventListener('click', async () => {
        const success = await processForm(currentStep, 'continue');

        if (success && currentStep < maxSteps) {
            currentStep++;
            await loadForm(currentStep);
            updateButtons();
        }
    });

    submitFormBtn.addEventListener('click', async () => {
        if (!checkAllFields(currentStep)) return;

        const success = await processForm(currentStep, 'submit');

        if (success) {
            alert('Application submitted successfully!');
            window.location.href = "<?= base_url('dashboard') ?>";
        }
    });
    // until here

    /* USE THIS FOR DEMO
    saveAndContinueBtn.addEventListener('click', () => {
        if (currentStep < maxSteps) {
            currentStep++;
            loadForm(currentStep);
            updateSaveAndContinueButton();
            updateBackButton();
            updateSubmitFormButton();
        }
    });
    */

    backBtn.addEventListener('click', async () => {
        if (currentStep > 1) {
            currentStep--;
            await loadForm(currentStep);
            updateButtons();
        }
    });
</script>