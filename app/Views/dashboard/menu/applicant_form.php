<!-- dashboard/menu/applicant_form.php -->

<div class="applicant-form-container">

    <div class="applicant-form-progress-container">
        <h4 id="form-progress-h4-text"></h4>

        <div id="progress-timeline-container" class="progress-timeline-container">
            <?php
            
            helper('form_field');
            $enabledPages = get_enabled_pages();
            $totalPages   = count($enabledPages);
            foreach ($enabledPages as $i => $pg):
                $stepNum = $i + 1;
                $isLast  = ($stepNum === $totalPages);
            ?>
            <div class="progress-box">
                <div class="progress-circle" data-step="<?= $stepNum ?>">
                    <?= $stepNum ?>
                </div>
                <span><?= esc(mb_strimwidth($pg['title'], 0, 10, '…')) ?></span>
            </div>
            <?php if (!$isLast): ?>
            <div class="progress-line" data-step="<?= $stepNum ?>"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="applicant-form-content-container">
        <div id="form-fillup-container" class="applicant-form-fillup-container"></div>

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
    const formProgressH4Text    = document.getElementById('form-progress-h4-text');
    const backBtn               = document.getElementById('back-btn');
    const saveAndContinueBtn    = document.getElementById('save-and-continue-btn');
    const submitFormBtn         = document.getElementById('submit-form');
    const formFillupContainer   = document.getElementById('form-fillup-container');

    
    const enabledPages = <?= json_encode(array_values($enabledPages)) ?>;
    const maxSteps     = enabledPages.length;

    let currentStep    = <?= isset($initial_step) ? (int)$initial_step : 1 ?>;
    let autoSaveTimeout;

    async function loadForm(step) {
        try {
            const pageId   = enabledPages[step - 1]?.id;
            const response = await fetch(`<?= base_url('applicant-form/step/') ?>${step}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            console.log('loadForm response:', data);         
        console.log('form HTML:', data.form);             

            document.title = "Akademus | " + data.title;
            formProgressH4Text.textContent = `Form Progress — Step ${step} of ${maxSteps}`;
            formFillupContainer.innerHTML  = data.form;

            updateProgress(step);

if (document.getElementById('program')) {
    await loadPrograms();
}

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
            if (field.type === 'radio') {
                form.querySelectorAll(`[name="${key}"]`).forEach(r => { r.checked = r.value == value; });
            } else if (field.type === 'checkbox') {
                field.checked = !!value;
            } else if (field.tagName === 'SELECT') {
                if (!field.querySelector(`option[value="${value}"]`)) {
                    const observer = new MutationObserver(() => {
                        if (field.querySelector(`option[value="${value}"]`)) { field.value = value; observer.disconnect(); }
                    });
                    observer.observe(field, { childList: true });
                } else {
                    field.value = value;
                }
            } else {
                field.value = value;
            }
        });
    }

    async function loadPrograms() {
        try {
            const response = await fetch(`<?= base_url('get-programs') ?>`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) return;
            const data = await response.json();
            if (!data.programs) return;
            const select = document.getElementById('program');
            if (!select) return;
            select.innerHTML = '<option value="" disabled selected>Select a Program</option>';
            data.programs.forEach(program => {
                const option = document.createElement('option');
                option.value = String(program.id);
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
            const response = await fetch(`<?= base_url('applicant-form/update/') ?>${step}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success') return true;
            alert(data.message || 'Something went wrong.');
            return false;
        } catch (error) {
            console.error('Form submission error:', error);
            alert('An error occurred while saving.');
            return false;
        }
    }

    function setupAutoSave(step) {
        const form = document.getElementById(`applicant-form-${step}`);
        if (!form) return;
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
        saveTextUI.textContent = 'Saving...';
        autoSaveTimeout = setTimeout(async () => {
            const success = await processForm(currentStep, 'draft');
            saveTextUI.textContent = success ? 'Auto-saved changes' : 'Auto-save failed';
            if (success) setTimeout(() => { saveTextUI.textContent = ''; }, 3000);
        }, 1000);
    }

    function checkAllFields(step) {
    const form = document.getElementById(`applicant-form-${step}`);
    if (!form) {
        console.log('FORM NOT FOUND: applicant-form-' + step);
        return false;
    }

    let isValid = true;
    form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

    const requiredFields = form.querySelectorAll('[required]');
    console.log('Total required fields found:', requiredFields.length);

    requiredFields.forEach(field => {
        let fieldValid = true;

        if (field.type === 'radio') {
            const group   = form.querySelectorAll(`input[name="${field.name}"]`);
            const checked = [...group].some(r => r.checked);
            if (!checked) {
                fieldValid = false;
                group.forEach(r => r.closest('label')?.classList.add('input-error'));
            }
        } else if (field.type === 'checkbox') {
            if (!field.checked) fieldValid = false;
        } else if (field.tagName === 'SELECT') {
            if (!field.value) fieldValid = false;
        } else {
            if (!field.value.trim()) fieldValid = false;
        }

        if (!fieldValid) {
            console.log('FAILING FIELD:', field.name, '| type:', field.type, '| value:', field.value);
            isValid = false;
            if (field.type !== 'radio') field.classList.add('input-error');
        }
    });

    if (!isValid) alert('Please fill out all required fields before proceeding.');
    return isValid;
}

    function updateProgress(step) {
        document.querySelectorAll('.progress-circle').forEach(circle => {
            const num = +circle.dataset.step;
            circle.classList.toggle('progress-circle-done',   num < step);
            circle.classList.toggle('progress-circle-active', num === step);
        });
        document.querySelectorAll('.progress-line').forEach(line => {
            const num = +line.dataset.step + 1;
            line.classList.toggle('progress-line-done',   num < step);
            line.classList.toggle('progress-line-active', num === step);
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

    saveAndContinueBtn.addEventListener('click', async () => {
    console.log('--- Save & Continue clicked ---');
    console.log('currentStep:', currentStep);
    console.log('maxSteps:', maxSteps);
    console.log('enabledPages:', enabledPages);

    const valid = checkAllFields(currentStep);
    console.log('checkAllFields result:', valid);
    if (!valid) return;

    const saveTextUI = document.getElementById('save-changes-text-ui');
    saveTextUI.textContent = 'Saving...';

    const success = await processForm(currentStep, 'continue');
    console.log('processForm result:', success);

    if (success && currentStep < maxSteps) {
        currentStep++;
        await loadForm(currentStep);
        updateButtons();
        saveTextUI.textContent = 'Auto-saved changes';
        setTimeout(() => { saveTextUI.textContent = ''; }, 3000);
    } else {
        console.log('Did not advance. success:', success, 'currentStep:', currentStep, 'maxSteps:', maxSteps);
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

    backBtn.addEventListener('click', async () => {
        if (currentStep > 1) {
            currentStep--;
            await loadForm(currentStep);
            updateButtons();
        }
    });
</script>