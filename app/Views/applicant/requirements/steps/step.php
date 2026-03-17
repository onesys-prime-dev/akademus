<!-- applicant/requirements/steps/step.php -->

<form 
    method="post"
    enctype="multipart/form-data"
    id="requirements-<?= $step_num ?>"
    class="requirements-step"
>

    <?php if (!empty($requirements)): ?>

        <!-- SECTION HEADER -->
        <div class="requirements-section__header">
            <h3><?= esc($category_name) ?> Documents</h3>
            <p><?= esc($category_description) ?></p>
        </div>

    <?php endif; ?>

    <div class="form-uploads">
        
        <?php foreach ($requirements as $requirement): ?>
            <?php
            $program_requirement_id = $requirement['program_requirement_id'];
            $requirement_name = $requirement['requirement_name'];
            $requirement_description = $requirement['requirement_description'];
            $is_mandatory = $requirement['is_mandatory'];
            ?>
            <div class="form-field">
                <label for="requirement_<?= $program_requirement_id ?>" class="upload-field">
                    <div class="requirement-upload-info">
                        <p class="requirement-title">
                            <?= esc($requirement_name) ?> 

                            <?php if ($is_mandatory): ?>
                                <span class="required">REQUIRED</span>
                            <?php endif; ?>
                        </p>
                        <p class="requirement-description"><?= esc($requirement_description) ?></p>
                    </div>
                    
                    <div id="upload-btn" class="requirement-upload-action btn-primary">Upload</div>
                </label>

                <input 
                    type="file" 
                    id="requirement_<?= $program_requirement_id ?>" 
                    name="documents[<?= $program_requirement_id ?>]" 
                    hidden 
                    <?= $is_mandatory ? 'required' : '' ?>>
            </div>
        <?php endforeach; ?>

    </div>

</form>