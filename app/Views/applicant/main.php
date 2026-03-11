<!-- dashboard/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <!-- Base CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/base/typography.css') . '?v=' . time(); ?>">

    <!-- Tokens CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/colors.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/borders.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/shadows.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens/spacing.css') . '?v=' . time(); ?>">

    <!-- Layouts CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboard-layout.css') . '?v=' . time(); ?>">

    <!-- Components CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/components/header.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/sidebar.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/buttons.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/card.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/form-field.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/form-progress.css') . '?v=' . time(); ?>">

    <!-- Layouts CSS Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/utilities/utilities.css') . '?v=' . time(); ?>">

    <!-- Pages CSS Styles -->
    <?php
    $pageName = basename($page_content);
    $cssPath = FCPATH . "assets/css/pages/{$pageName}.css";

    if (file_exists($cssPath)):
    ?>
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/{$pageName}.css?v=" . time()) ?>">
    <?php endif; ?>
    
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar-container">
            <?= view('applicant/components/sidebar') ?>
        </div>

        <main class="main-content">
            <?= $this->include('applicant/components/header') ?>

            <div class="content-container">
                <!-- reusable content goes here -->
                <?= $this->include($page_content) ?>
            </div>
        </main>
    </div>
</body>
</html>