<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Page</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/applynowpage.css') ?>">
</head>
<body>
    <nav>
        <a href="<?= base_url('/') ?>" class="nav-brand">
            <div class="nav-logo-box">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo" class="nav-logo">
            </div>
            <span class="nav-title">AKADEMUS</span>
        </a>
        <ul class="nav-links">
            <li><a href="#">About Us</a></li>
            <li><a href="#">Admissions</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Login/Register</a></li>
            
        </ul>
    </nav>
    <canvas id="bgCanvas"></canvas>

    <!-- js boostrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- public/assets/js -->
    <script src="<?= base_url('assets/js/applynowpage.js') ?>"></script>
</body>
</html>