<div class="row mb-4">
    <div class="col-md-8">
        <h3>Fields for <?= esc($course['name']) ?></h3>
        <small class="text-muted">Manage course-specific and global fields</small>
    </div>
    <div class="col-md-4 text-end">
        <a href="/admin/forms/course/<?= $courseId ?>/field/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Custom Field
        </a>
    </div>
</div>

<!-- MERGED/PREVIEW section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-eye"></i> Form Preview (All Fields)
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Total fields:</strong> <?= count($mergedFields) ?> | 
            <strong>Global:</strong> <?= count($globalFields) ?> | 
            <strong>Course-specific:</strong> <?= count($fields) ?>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Field</th>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mergedFields as $field): ?>
                        <tr>
                            <td><?= $field['display_order'] ?></td>
                            <td><code><?= esc($field['field_name']) ?></code></td>
                            <td><?= esc($field['label']) ?></td>
                            <td><span class="badge bg-info"><?= $field['field_type'] ?></span></td>
                            <td>
                                <?php if ($field['is_required']): ?>
                                    <span class="badge bg-danger">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $isCourseField = in_array(
                                        $field['field_name'],
                                        array_column($fields, 'field_name')
                                    );
                                ?>
                                <span class="badge bg-<?= $isCourseField ? 'warning' : 'secondary' ?>">
                                    <?= $isCourseField ? 'Course-Specific' : 'Global' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- COURSE-SPECIFIC FIELDS section -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-star"></i> Course-Specific Fields Only
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order</th>
                    <th>Field Name</th>
                    <th>Label</th>
                    <th>Type</th>
                    <th>Override</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fields)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No custom fields added yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fields as $field): ?>
                        <tr>
                            <td><?= $field['display_order'] ?></td>
                            <td><code><?= esc($field['field_name']) ?></code></td>
                            <td><?= esc($field['label']) ?></td>
                            <td><span class="badge bg-info"><?= $field['field_type'] ?></span></td>
                            <td>
                                <?php if ($field['override_global']): ?>
                                    <span class="badge bg-warning">Overrides Global</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/forms/course/<?= $courseId ?>/field/<?= $field['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/admin/forms/delete/<?= $field['id'] ?>/course?course_id=<?= $courseId ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this field?')">
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
