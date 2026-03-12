<!-- dashboard/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <?= theme_css_vars() ?>

    <link rel="stylesheet" href="<?= base_url('assets/css/dashboardcontainer.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/applicantform.css') . '?v=' . time(); ?>">
</head>
<body>
    <div class="sidebar-container">
        <?= view('partials/sidebar', ['theme' => $theme]) ?>
    </div>

    <main class="main-content">
        <div class="header">
            <h1><?= $title ?></h1>
            <div class="application-status">Approve</div>
        </div>

        <div class="content-container">
            <?= view($page_content) ?>
        </div>
    </main>

    <?php if (uri_string() !== 'faq'): ?>
        <?= view('partials/chat_widget', ['theme' => $theme]) ?>
    <?php endif; ?>
</body>
</html>