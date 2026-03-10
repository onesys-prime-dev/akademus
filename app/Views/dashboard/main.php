<!-- dashboard/main.php -->

<!-- dashboard/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ↓ ADD THIS BLOCK HERE ↓ -->
    <style>
    :root {
        --primary:       <?= $theme['primary_color'] ?? '#0040A6' ?>;
        --primary-dark:  <?= $theme['primary_dark']  ?? '#002d7a' ?>;
        --primary-light: <?= $theme['primary_light'] ?? '#1a55c0' ?>;
        --accent:        <?= $theme['accent_color']  ?? '#FFB200' ?>;
        --accent-text:   <?= $theme['accent_text']   ?? '#002d7a' ?>;
        --radius:        <?= $theme['border_radius'] ?? '12px'    ?>;
        --font:          '<?= $theme['font_family']  ?? 'Poppins' ?>', sans-serif;
    }
    </style>

    <link rel="stylesheet" href="<?= base_url('assets/css/dashboardcontainer.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/applicantform.css') . '?v=' . time(); ?>">
</head>
<body>
    <div class="sidebar-container">
        <?= view('partials/sidebar') ?>
    </div>

    <main class="main-content">
        <div class="header">
            <h1><?= $title ?></h1>
            <div class="application-status">Approve</div>
        </div>

        <div class="content-container">
            <!-- reusable content goes here -->
            <?= view($page_content) ?>
        </div>
    </main>
</body>
</html>