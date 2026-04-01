<div class="row mb-4">
    <div class="col-md-8">
        <h3>Global Form Fields</h3>
        <small class="text-muted">Default fields used in all application forms</small>
    </div>
    <div class="col-md-4 text-end">
        <a href="/admin/forms/global/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Field
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 30px;">Order</th>
                    <th>Field Name</th>
                    <th>Label</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fields)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No global fields yet. <a href="/admin/forms/global/create">Create one</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fields as $field): ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary"><?= $field['display_order'] ?></span>
                            </td>
                            <td><code><?= esc($field['field_name']) ?></code></td>
                            <td><?= esc($field['label']) ?></td>
                            <td>
                                <span class="badge bg-info"><?= $field['field_type'] ?></span>
                            </td>
                            <td>
                                <?php if ($field['is_required']): ?>
                                    <span class="badge bg-danger">Required</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">Optional</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $field['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $field['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/forms/global/edit/<?= $field['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/admin/forms/delete/<?= $field['id'] ?>/global" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this field?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 p-3 bg-light rounded">
    <h6><i class="fas fa-info-circle"></i> Global Fields Information</h6>
    <p class="mb-0 small">These fields appear in <strong>all</strong> application forms. Course-specific fields can be added on top of these, and can override them if needed.</p>
</div>
