<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Page</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/applicationform.css') ?>">
</head>
<body>
    <a href="<?= base_url('/') ?>" class="brand-link">
        <img src="<?= base_url('assets/images/logo_akademus.png') ?>" 
             alt="Akademus Logo" 
             class="brand-logo">
        <span class="brand-text">AKADEMUS</span>    
    </a>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    
    <section class="form-section">
        <h2>Sign In</h2>

        <form action="<?= base_url('applicationform/submit') ?>" method="post">

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Contact Number</label>
                <input type="text" name="contactnum" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Submit 
            </button>

        </form>
    </section>

    <canvas id="bgCanvas"></canvas>

    <!-- js boostrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- public/assets/js -->
    <script src="<?= base_url('assets/js/applynowpage.js') ?>"></script>
</body>
</html>



