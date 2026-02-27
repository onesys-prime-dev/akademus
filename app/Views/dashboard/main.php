<!-- auth/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= $title ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboardcontainer.css') . '?v=' . time(); ?>">
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