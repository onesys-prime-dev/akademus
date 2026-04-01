<?php echo doctype('html5'); ?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> - Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicons/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/favicons/favicon-180x180.png') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #003d7a;
            --secondary-color: #f4f7fa;
        }

        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            left: 0;
            top: 0;
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin: 0;
        }

        .sidebar-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav a.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid white;
            padding-left: 17px;
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .admin-container {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .admin-content {
            flex: 1;
            padding: 30px;
            background: var(--secondary-color);
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Alert messages */
        .alert {
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            --sidebar-width: 0;

            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-container {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <span>Akademus</span>
        </div>

        <ul class="sidebar-nav">
            <li>
                <a href="/admin/dashboard" class="<?= ($sidebarActive === 'dashboard' ? 'active' : '') ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/admin/courses" class="<?= ($sidebarActive === 'courses' ? 'active' : '') ?>">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
            </li>
            <li>
                <a href="/admin/forms" class="<?= ($sidebarActive === 'forms' ? 'active' : '') ?>">
                    <i class="fas fa-wpforms"></i>
                    <span>Application Forms</span>
                </a>
            </li>
            <li>
                <a href="/admin/presets" class="<?= ($sidebarActive === 'presets' ? 'active' : '') ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Form Presets</span>
                </a>
            </li>
            <li>
                <a href="/admin/programs/presets" class="<?= ($sidebarActive === 'program-presets' ? 'active' : '') ?>">
                    <i class="fas fa-link"></i>
                    <span>Program Assignments</span>
                </a>
            </li>
            <li>
                <a href="/admin/applicants" class="<?= ($sidebarActive === 'applicants' ? 'active' : '') ?>">
                    <i class="fas fa-users"></i>
                    <span>Applicants</span>
                </a>
            </li>
            <li>
                <a href="/admin/settings" class="<?= ($sidebarActive === 'settings' ? 'active' : '') ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="admin-container">
        <div class="admin-topbar">
            <h2><?= esc($pageTitle) ?></h2>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> Admin
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/admin/profile">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>

        <div class="admin-content">
            <!-- Breadcrumbs -->
            <?php if (!empty($breadcrumbs)): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="margin-bottom: 20px;">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumb-item <?= ($crumb === end($breadcrumbs) ? 'active' : '') ?>">
                                <?php if ($crumb['url']): ?>
                                    <a href="<?= esc($crumb['url']) ?>"><?= esc($crumb['label']) ?></a>
                                <?php else: ?>
                                    <?= esc($crumb['label']) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <!-- Alert Messages -->
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="page-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
