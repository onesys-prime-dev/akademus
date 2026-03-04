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
            <span id="save-changes-text-ui">Auto-saved as draft</span>
            
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

    let autoSaveTimeout;

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

            // If step 1, load programs first
            if (step === 1) {
                await loadPrograms(); // load options
            }

            // Populate all fields including dynamic selects
            populateForm(`applicant-form-${step}`, data.formData);

            setupAutoSave(step);

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
                    if (radio.value == value) radio.checked = true;
                });
            }
            // CHECKBOX
            else if (field.type === 'checkbox') {
                field.checked = !!value;
            }
            // SELECT
            else if (field.tagName === 'SELECT') {
                // If options are not loaded yet, wait for a tiny delay
                if (!field.querySelector(`option[value="${value}"]`)) {
                    const observer = new MutationObserver(() => {
                        if (field.querySelector(`option[value="${value}"]`)) {
                            field.value = value;
                            observer.disconnect();
                        }
                    });
                    observer.observe(field, { childList: true });
                } else {
                    field.value = value;
                }
            }
            // TEXT / DATE / EMAIL / TEXTAREA
            else {
                field.value = value;
            }
        });
    }

    async function loadPrograms() {
        try {
            const response = await fetch(`<?= base_url('get-programs') ?>`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();
            if (!data.programs) return;

            const select = document.getElementById('program');
            select.innerHTML = '<option value="" disabled selected>Select a Program</option>';

            data.programs.forEach(program => {
                const option = document.createElement('option');
                option.value = String(program.id); // force string
                option.textContent = program.name;
                select.appendChild(option);
            });
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

    function setupAutoSave(step) {
        const form = document.getElementById(`applicant-form-${step}`);
        if (!form) return;

        // Remove previous listeners if any
        form.querySelectorAll('[name]').forEach(field => {
            field.removeEventListener('input', autoSaveHandler);
            field.removeEventListener('change', autoSaveHandler);
            field.addEventListener('input', autoSaveHandler);
            field.addEventListener('change', autoSaveHandler);
        });
    }

    async function autoSaveHandler() {
        const saveTextUI = document.getElementById('save-changes-text-ui');
        if (!saveTextUI) return;

        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

        // Show "Saving..." while waiting for debounce
        saveTextUI.textContent = 'Saving...';

        // Wait 1 second after last change before saving
        autoSaveTimeout = setTimeout(async () => {
            const success = await processForm(currentStep, 'draft');

            if (success) {
                saveTextUI.textContent = 'Auto-saved changes';
                // Optional: fade out after 3s
                setTimeout(() => {
                    saveTextUI.textContent = '';
                }, 3000);
            } else {
                saveTextUI.textContent = 'Auto-save failed';
            }
        }, 1000);
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
        if (!checkAllFields(currentStep)) return;

        const saveTextUI = document.getElementById('save-changes-text-ui');
        if (!saveTextUI) return;

        // Show "Saving..." while waiting for debounce
        saveTextUI.textContent = 'Saving...';

        const success = await processForm(currentStep, 'continue');

        if (success && currentStep < maxSteps) {
            currentStep++;
            await loadForm(currentStep);
            updateButtons();
            
            saveTextUI.textContent = 'Auto-saved changes';
            setTimeout(() => {
                    saveTextUI.textContent = '';
                }, 3000);
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