<?php echo doctype('html5'); ?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .form-container {
            max-width: 700px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        .form-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #003d7a;
            padding-bottom: 20px;
        }
        .form-header h1 {
            color: #003d7a;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .form-header p {
            color: #666;
            margin: 0;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner-border {
            color: #003d7a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1><?= esc($course['name']) ?></h1>
                <p><?= esc($course['description']) ?></p>
            </div>

            <form id="applicationForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

                <div id="formFields">
                    <!-- Fields will be rendered here by JavaScript -->
                </div>

                <div class="loading" id="loading">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>

            <div id="successMessage" class="alert alert-success d-none mt-3">
                <i class="fas fa-check-circle"></i> Application submitted successfully!
                <a href="/applicant/dashboard" class="alert-link">View your applications</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const courseId = document.querySelector('input[name="course_id"]').value;

        // Render form fields dynamically
        async function loadFormFields() {
            try {
                const response = await fetch(`/application/get-fields/${courseId}`);
                const data = await response.json();

                if (!data.success) {
                    showError('Failed to load form fields');
                    return;
                }

                const fieldsContainer = document.getElementById('formFields');
                fieldsContainer.innerHTML = '';

                data.fields.forEach((field, index) => {
                    const formGroup = createFormField(field);
                    fieldsContainer.appendChild(formGroup);
                });
            } catch (error) {
                showError('Error loading form: ' + error.message);
            }
        }

        // Create individual form field HTML
        function createFormField(field) {
            const wrapper = document.createElement('div');
            wrapper.className = 'mb-3';

            const label = document.createElement('label');
            label.htmlFor = field.field_name;
            label.className = field.is_required ? 'form-label required' : 'form-label';
            label.textContent = field.label;
            wrapper.appendChild(label);

            let input;

            switch (field.field_type) {
                case 'text':
                case 'email':
                case 'number':
                    input = document.createElement('input');
                    input.type = field.field_type;
                    input.className = 'form-control';
                    input.name = field.field_name;
                    input.id = field.field_name;
                    input.placeholder = field.placeholder || '';
                    input.required = field.is_required;
                    wrapper.appendChild(input);
                    break;

                case 'textarea':
                    input = document.createElement('textarea');
                    input.className = 'form-control';
                    input.name = field.field_name;
                    input.id = field.field_name;
                    input.rows = 4;
                    input.placeholder = field.placeholder || '';
                    input.required = field.is_required;
                    wrapper.appendChild(input);
                    break;

                case 'file':
                    input = document.createElement('input');
                    input.type = 'file';
                    input.className = 'form-control';
                    input.name = field.field_name;
                    input.id = field.field_name;
                    input.required = field.is_required;
                    wrapper.appendChild(input);

                    const hint = document.createElement('small');
                    hint.className = 'd-block text-muted mt-1';
                    hint.textContent = 'Accepted formats: PDF, DOC, DOCX, JPG, PNG';
                    wrapper.appendChild(hint);
                    break;

                case 'select':
                    input = document.createElement('select');
                    input.className = 'form-select';
                    input.name = field.field_name;
                    input.id = field.field_name;
                    input.required = field.is_required;

                    // Add blank option
                    const blankOption = document.createElement('option');
                    blankOption.value = '';
                    blankOption.textContent = '-- Select an option --';
                    input.appendChild(blankOption);

                    // Add options
                    const options = JSON.parse(field.options || '[]');
                    options.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt;
                        option.textContent = opt;
                        input.appendChild(option);
                    });

                    wrapper.appendChild(input);
                    break;

                case 'checkbox':
                case 'radio':
                    const options2 = JSON.parse(field.options || '[]');
                    options2.forEach(opt => {
                        const div = document.createElement('div');
                        div.className = field.field_type === 'checkbox' ? 'form-check' : 'form-check';

                        const inputEl = document.createElement('input');
                        inputEl.type = field.field_type;
                        inputEl.className = 'form-check-input';
                        inputEl.name = field.field_name;
                        inputEl.id = `${field.field_name}_${opt}`.replace(/\s/g, '_');
                        inputEl.value = opt;
                        inputEl.required = field.is_required && options2.indexOf(opt) === 0;
                        div.appendChild(inputEl);

                        const labelEl = document.createElement('label');
                        labelEl.className = 'form-check-label';
                        labelEl.htmlFor = inputEl.id;
                        labelEl.textContent = opt;
                        div.appendChild(labelEl);

                        wrapper.appendChild(div);
                    });
                    break;
            }

            return wrapper;
        }

        // Handle form submission
        document.getElementById('applicationForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(document.getElementById('applicationForm'));

            try {
                const response = await fetch(`/application/submit/${courseId}`, {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('applicationForm').style.display = 'none';
                    document.getElementById('successMessage').classList.remove('d-none');
                } else {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, message]) => {
                            const fieldEl = document.getElementById(field);
                            if (fieldEl) {
                                fieldEl.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.textContent = message;
                                fieldEl.parentElement.appendChild(feedback);
                            }
                        });
                    } else {
                        showError(data.error || 'Submission failed');
                    }
                }
            } catch (error) {
                showError('Error submitting form: ' + error.message);
            }
        });

        function showError(message) {
            alert(message); // Or use a better notification system
        }

        // Load fields on page load
        loadFormFields();
    </script>
</body>
</html>
