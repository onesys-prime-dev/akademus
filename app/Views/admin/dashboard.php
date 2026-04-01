<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Courses</h6>
                        <h3 class="mb-0"><?= $stats['total_courses'] ?></h3>
                    </div>
                    <i class="fas fa-book fa-2x" style="color: #003d7a; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Pending Applications</h6>
                        <h3 class="mb-0"><?= $stats['pending_applications'] ?></h3>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x" style="color: #ff9800; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Approved</h6>
                        <h3 class="mb-0"><?= $stats['approved_applications'] ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x" style="color: #4caf50; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <a href="/admin/courses/create" class="btn btn-primary me-2">
            <i class="fas fa-plus"></i> Add Course
        </a>
        <a href="/admin/forms/global" class="btn btn-secondary me-2">
            <i class="fas fa-wpforms"></i> Manage Global Fields
        </a>
        <a href="/admin/applicants" class="btn btn-info">
            <i class="fas fa-glasses"></i> Review Applications
        </a>
    </div>
</div>
