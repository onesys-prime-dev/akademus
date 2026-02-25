<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/homepage.css') ?>">
</head>
<body>
    <?= view('partials/navbar') ?>
    
    <!-- the big chunk of square in the center -->
    <section class="hero">

        <!-- bg (animated) -->
        <canvas id="bgCanvas"></canvas>

        <!-- Hero Card -->
        <div class="hero-card">
            <p class="eyebrow">Welcome to Akademus</p>
            <h1>Lorem ipsum dolor sit amet</h1>
            <a href="#" class="btn-learn">Learn More</a>
        </div>

        <!-- goto MENTORA -->
        <a href="#" class="mentora-link">
            <span class="label">Proceed to Mentora</span>
            <span class="mentora-arrow">&#8594;</span>
            <div class="mentora-logo-box">
                <img src="assets/images/logo_mentora.png" alt="Mentora Logo" class="mentora-logo">
            </div>
        </a>

    </section>
    <section class="section 2">
        <div>
            <h1>addtional sources</h1>
            <p>(may dagdag details)</p>
            <p>(dito e lagay mga other systems created by the company)</p>
        </div>

        <article>
            blog
        </article>
    </section>
    <!-- js boostrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- public/assets/js -->
    <script src="<?= base_url('assets/js/homepage.js') ?>"></script>
</body>
</html>