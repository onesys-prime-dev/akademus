<!-- dashboard/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Layouts CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboardcontainer.css') . '?v=' . time(); ?>">

    <!-- Components CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/components/header.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/sidebar.css') . '?v=' . time(); ?>">

    <!-- Pages CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/dashboard.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/applicantform.css') . '?v=' . time(); ?>">
</head>
<body>
    <div class="sidebar-container">
        <?= view('partials/sidebar') ?>
    </div>

    <main class="main-content">
        <div class="dashboard-header">
            <h1><?= $title ?></h1>
            <div class="application-status-badge">Approve</div>
        </div>

        <div class="content-container">
            <!-- reusable content goes here -->
            <?= $this->include($page_content) ?>
        </div>
    </main>
</body>
</html>