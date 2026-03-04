<!-- dashboard/menu/applicant_form.php  -->

<div class="applicant-form-container">
    <!-- Hello, this is the APPLICANT FORM. -->

    <div class="applicant-form-progress-container">
        <h4 id="form-progress-h4-text">Form Progress — Step 1 of 4</h4>

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

    // Use PHP variables to initialize step & max steps
    let currentStep = <?= isset($initial_step) ? $initial_step : 1 ?>;
    const maxSteps = <?= isset($max_steps) ? $max_steps : 4 ?>;

    async function loadForm(step) {
        const response = await fetch(`<?= base_url('applicant-form/step/') ?>${step}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();

        document.title = "Akademus | " + data.title;
        formProgressH4Text.textContent = `Form Progress — Step ${step} of 4`;
        formFillupContainer.innerHTML = data.form;

        // Update browser URL to match current step
        const base = '<?= base_url() ?>';
        history.replaceState(null, '', `${base}applicant-form/step/${step}`);
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
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }    
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

    function updateSaveAndContinueButton() {
        if (currentStep === 4) saveAndContinueBtn.classList.add('hidden');
        else saveAndContinueBtn.classList.remove('hidden');
    }

    function updateBackButton() {
        if (currentStep === 1) backBtn.classList.add('hidden');
        else backBtn.classList.remove('hidden');
    }

    function updateSubmitFormButton() {
        if (currentStep === 4) submitFormBtn.classList.remove('hidden');
        else submitFormBtn.classList.add('hidden');

    }

    document.addEventListener('DOMContentLoaded', () => {
        loadForm(currentStep);
        updateSaveAndContinueButton();
        updateBackButton();
        updateSubmitFormButton();
    });

    // Buttons
    /* UNCOMMENT THIS IF YOU WILL PROCEED TO FORM SUBMISSION. For now, use the saveAndContinueBtn listeners below if only for demo.
    saveAndContinueBtn.addEventListener('click', async () => {
        const success = await processForm(currentStep, 'continue');

        if (success && currentStep < maxSteps) {
            currentStep++;
            await loadForm(currentStep);
            updateSaveAndContinueButton();
            updateBackButton();
            updateSubmitFormButton();
        }
    });

    submitFormBtn.addEventListener('click', async () => {
        const success = await processForm(currentStep, 'submit');

        if (success) {
            alert('Application submitted successfully!');
            window.location.href = "<?= base_url('dashboard') ?>";
        }
    });
    */

    saveAndContinueBtn.addEventListener('click', () => {
        if (currentStep < maxSteps) {
            currentStep++;
            loadForm(currentStep);
            updateSaveAndContinueButton();
            updateBackButton();
            updateSubmitFormButton();
        }
    });

    backBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            loadForm(currentStep);
            updateSaveAndContinueButton();
            updateBackButton();
            updateSubmitFormButton();
        }
    });
</script>