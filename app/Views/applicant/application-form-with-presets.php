<?php $this->extend('layouts/app_layout'); ?>

<?php $this->section('content'); ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clipboard"></i> Application Form
                    </h4>
                </div>

                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Program Selection -->
                    <form id="presetForm">
                        <div class="mb-4">
                            <label for="program_id" class="form-label">
                                Select Your Program <span class="text-danger">*</span>
                            </label>
                            <select id="program_id" class="form-select form-select-lg" required>
                                <option value="">-- Choose a Program --</option>
                                <?php if (!empty($programs)): ?>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['program_id']; ?>">
                                            <?= esc($program['program_name']); ?>
                                            <?php if (!empty($program['program_code'])): ?>
                                                <small>(<?= esc($program['program_code']); ?>)</small>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i>
                                The form sections will appear based on your selected program.
                            </small>
                        </div>
                    </form>

                    <!-- Divider -->
                    <hr>

                    <!-- Dynamic Form Sections Container -->
                    <div id="presetFormContainer" data-preset-loader 
                         data-program-select="#program_id" 
                         data-form-container="#presetFormContainer"
                         data-api-endpoint="/api/presets/program">
                        <div class="alert alert-info">
                            <i class="fas fa-arrow-up"></i>
                            Please select a program to begin the application.
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" form="applicationForm" class="btn btn-primary" 
                                <?= empty($programs) ? 'disabled' : ''; ?>>
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                        <a href="/dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-question-circle"></i> Application Tips
                    </h6>
                    <ul class="small mb-0">
                        <li>Fill out all required (*) fields to proceed</li>
                        <li>You can save your progress and come back later</li>
                        <li>Each program has different requirements - select carefully!</li>
                        <li>For assistance, contact our admissions office</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Preset Loader JavaScript -->
<script src="/assets/js/preset-loader.js"></script>

<?php $this->endSection(); ?>
