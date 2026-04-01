<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Course Name *</label>
                <input type="text" class="form-control <?= (!empty($errors) && isset($errors['name']) ? 'is-invalid' : '') ?>"
                    id="name" name="name" value="<?= esc($name ?? '') ?>" required>
                <?php if (!empty($errors) && isset($errors['name'])): ?>
                    <div class="invalid-feedback d-block"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Course Code *</label>
                <input type="text" class="form-control <?= (!empty($errors) && isset($errors['code']) ? 'is-invalid' : '') ?>"
                    id="code" name="code" value="<?= esc($code ?? '') ?>" required>
                <small class="d-block text-muted mt-1">e.g., BSIT, BSN</small>
                <?php if (!empty($errors) && isset($errors['code'])): ?>
                    <div class="invalid-feedback d-block"><?= $errors['code'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= esc($description ?? '') ?></textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                    <?= (isset($is_active) && !$is_active ? '' : 'checked') ?>>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Course
                </button>
                <a href="/admin/courses" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
