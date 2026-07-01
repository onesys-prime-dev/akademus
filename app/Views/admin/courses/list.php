<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Courses</h3>
    <a href="/admin/courses/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Course
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No courses found. <a href="/admin/courses/create">Create one</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><strong><?= esc($course['code']) ?></strong></td>
                            <td><?= esc($course['name']) ?></td>
                            <td><?= esc(substr($course['description'], 0, 50)) ?>...</td>
                            <td>
                                <span class="badge bg-<?= $course['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/forms/course/<?= $course['id'] ?>" class="btn btn-outline-secondary" title="Manage fields">
                                        <i class="fas fa-wpforms"></i>
                                    </a>
                                    <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/admin/courses/delete/<?= $course['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this course?')">
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
