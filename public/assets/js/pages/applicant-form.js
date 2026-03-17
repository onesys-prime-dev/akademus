// assets/js/pages/applicant-form.js

const { initialStep, maxSteps, baseUrl, stepUrl, updateUrl, programsUrl, dashboardUrl } = window.applicantFormConfig;

let currentStep = initialStep;

const formProgressH4Text = document.getElementById('form-progress-text');
    const backBtn = document.getElementById('btn-back');
    const saveAndContinueBtn = document.getElementById('btn-save');
    const submitFormBtn = document.getElementById('btn-submit');
    const formFillupContainer = document.getElementById('applicant-form-body');
    const autosaveText = document.getElementById('autosave-text');

    let autoSaveTimeout;

    async function loadForm(step) {
        try {
            const response = await fetch(`${stepUrl}${step}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            document.title = "Akademus | " + data.title;
            formProgressH4Text.textContent = `Form Progress — Step ${step} of ${maxSteps}`;
            formFillupContainer.innerHTML = data.form;

            updateProgress(step);

            // If step 1, load programs first
            if (step === 1) {
                await loadPrograms(); // load options
            }

            // Populate all fields including dynamic selects
            populateForm(`applicant-form-${step}`, data.formData);

            setupAutoSave(step);

            history.replaceState(null, '', `${stepUrl}${step}`);

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
            const response = await fetch(`${programsUrl}`, {
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
            const response = await fetch(`${updateUrl}${step}`, 
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
        if (!autosaveText) return;

        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

        // Show "Saving..." while waiting for debounce
        autosaveText.textContent = 'Saving...';

        // Wait 1 second after last change before saving
        autoSaveTimeout = setTimeout(async () => {
            const success = await processForm(currentStep, 'draft');

            if (success) {
                autosaveText.textContent = 'Auto-saved changes';
                // Optional: fade out after 3s
                setTimeout(() => {
                    autosaveText.textContent = '';
                }, 3000);
            } else {
                autosaveText.textContent = 'Auto-save failed';
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

    function updateProgress(step) {
        const circles = document.querySelectorAll('.form-progress__circle');
        const lines = document.querySelectorAll('.form-progress__line');

        circles.forEach(circle => {
            const num = Number(circle.dataset.step);
            circle.classList.toggle('form-progress__circle-done', num < step);
            circle.classList.toggle('form-progress__circle-active', num === step);
            circle.textContent = num < step ? '✔' : num;
        });

        lines.forEach(line => {
            const num = Number(line.dataset.step);
            line.classList.toggle('form-progress__line-done', num < step);
        });
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
    saveAndContinueBtn.addEventListener('click', async () => {
        if (!checkAllFields(currentStep)) return;

        if (!autosaveText) return;

        // Show "Saving..." while waiting for debounce
        autosaveText.textContent = 'Saving...';

        const success = await processForm(currentStep, 'continue');

        if (success && currentStep < maxSteps) {
            currentStep++;
            await loadForm(currentStep);
            updateButtons();
            
            autosaveText.textContent = 'Auto-saved changes';
            setTimeout(() => {
                    autosaveText.textContent = '';
                }, 3000);
        }
    });

    submitFormBtn.addEventListener('click', async () => {
        if (!checkAllFields(currentStep)) return;

        const success = await processForm(currentStep, 'submit');

        if (success) {
            alert('Application submitted successfully!');
            window.location.href = `${dashboardUrl}`;
        }
    });

    backBtn.addEventListener('click', async () => {
        if (currentStep > 1) {
            currentStep--;
            await loadForm(currentStep);
            updateButtons();
        }
    });
