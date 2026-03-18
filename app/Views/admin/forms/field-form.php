<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="field_name" class="form-label">Field Name (code) *</label>
                    <input type="text" class="form-control" id="field_name" name="field_name"
                        value="<?= esc($field['field_name'] ?? '') ?>" required>
                    <small class="d-block text-muted mt-1">e.g., first_name, email, essay (no spaces)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="label" class="form-label">Label (display name) *</label>
                    <input type="text" class="form-control" id="label" name="label"
                        value="<?= esc($field['label'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="field_type" class="form-label">Field Type *</label>
                    <select class="form-select" id="field_type" name="field_type" required>
                        <option value="">-- Select Type --</option>
                        <?php foreach ($fieldTypes as $type): ?>
                            <option value="<?= $type ?>" <?= ($field['field_type'] ?? '') === $type ? 'selected' : '' ?>>
                                <?= ucfirst($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" class="form-control" id="display_order" name="display_order"
                        value="<?= $field['display_order'] ?? 0 ?>">
                    <small class="d-block text-muted mt-1">Lower numbers appear first</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="placeholder" class="form-label">Placeholder</label>
                    <input type="text" class="form-control" id="placeholder" name="placeholder"
                        value="<?= esc($field['placeholder'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="options" class="form-label">Options (for select/radio/checkbox)</label>
                    <input type="text" class="form-control" id="options" name="options"
                        value="<?= esc($field['options'] ?? '') ?>"
                        placeholder='["Option 1", "Option 2"]'>
                    <small class="d-block text-muted mt-1">JSON array format</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="validation_rules" class="form-label">Validation Rules</label>
                    <textarea class="form-control" id="validation_rules" name="validation_rules" rows="2"
                        placeholder="min_length:5&#10;max_length:100&#10;email"><?= esc($field['validation_rules'] ?? '') ?></textarea>
                    <small class="d-block text-muted mt-1">One rule per line. Common: email, min_length:X, max_length:X, numeric, url</small>
                </div>

                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_required" name="is_required"
                            <?= (isset($field['is_required']) && $field['is_required'] ? 'checked' : '') ?>>
                        <label class="form-check-label" for="is_required">
                            Required Field
                        </label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                            <?= (isset($field['is_active']) && !$field['is_active'] ? '' : 'checked') ?>>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Field
                </button>
                <a href="<?= isset($courseId) ? "/admin/forms/course/{$courseId}" : '/admin/forms/global' ?>" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
