<!-- applicant/requirements/steps/step.php -->

<form
    method="post"
    enctype="multipart/form-data"
    id="requirements-<?= $step_num ?>"
    class="requirements-step"
>
    <?= csrf_field() ?>

    <?php if (!empty($requirements)): ?>

        <!-- SECTION HEADER -->
        <div class="requirements-section__header">
            <div class="requirements-section__header-top">
                <h3><?= esc($category_name) ?></h3>
                <?php if (($total_required ?? 0) > 0): ?>
                    <span class="upload-counter <?= ($uploaded_required ?? 0) >= ($total_required ?? 0) ? 'upload-counter--done' : '' ?>">
                        <?= (int)($uploaded_required ?? 0) ?>/<?= (int)($total_required ?? 0) ?> required uploaded
                    </span>
                <?php endif; ?>
            </div>
            <?php if (!empty($category_description)): ?>
                <p><?= esc($category_description) ?></p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <div class="form-uploads">
        
        <?php foreach ($requirements as $requirement): ?>
            <?php
            $program_requirement_id = $requirement['program_requirement_id'];
            $requirement_name = $requirement['requirement_name'];
            $requirement_description = $requirement['requirement_description'];
            $is_required = (bool) ($requirement['is_required'] ?? false);
            ?>
            <div class="form-field">
                <label for="requirement_<?= $program_requirement_id ?>" class="upload-field">
                    <div class="requirement-upload-info">
                        <p class="requirement-title">
                            <?= esc($requirement_name) ?>

                            <?php if ($is_required): ?>
                                <span class="required">REQUIRED</span>
                            <?php endif; ?>
                        </p>
                        <p class="requirement-description"><?= esc($requirement_description) ?></p>
                    </div>

                    <div class="requirement-upload-actions">
                        <a href="#"
                           class="requirement-view-action hidden"
                           data-req-id="<?= $program_requirement_id ?>"
                           data-doc-name=""
                           onclick="event.preventDefault(); event.stopPropagation(); openDocumentModal(this.dataset.reqId, this.dataset.docName)">View</a>
                        <div class="requirement-upload-action btn-primary">Upload</div>
                    </div>
                </label>

                <input
                    type="file"
                    id="requirement_<?= $program_requirement_id ?>"
                    name="documents[<?= $program_requirement_id ?>]"
                    accept=".pdf,.png,.jpg,.jpeg,.doc,.docx"
                    hidden
                    <?= $is_required ? 'required' : '' ?>>
            </div>
        <?php endforeach; ?>

    </div>

</form>