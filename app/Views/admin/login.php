<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Sora', sans-serif;
            background: #f0f4f8;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .brand {
            display: flex; align-items: center; gap: 12px; margin-bottom: 28px;
        }
        .brand-icon {
            width: 44px; height: 44px; background: #0040a6; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 800; color: #ffb200;
        }
        .brand-name { font-size: 18px; font-weight: 800; color: #0d1e38; }
        .brand-sub  { font-size: 11px; color: #5a6a85; }
        h2 { font-size: 22px; font-weight: 800; color: #0d1e38; margin-bottom: 6px; }
        p  { font-size: 13px; color: #5a6a85; margin-bottom: 24px; }
        label {
            display: block; font-size: 11px; font-weight: 700;
            color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px;
            margin-bottom: 6px;
        }
        input {
            width: 100%; padding: 11px 14px; border: 1.5px solid #dde3f0;
            border-radius: 9px; font-family: 'Sora', sans-serif;
            font-size: 13px; color: #0d1e38; outline: none;
            transition: border-color 0.18s; margin-bottom: 16px;
        }
        input:focus { border-color: #0040a6; box-shadow: 0 0 0 3px rgba(0,64,166,0.08); }
        button {
            width: 100%; padding: 12px; background: #0040a6; color: #fff;
            border: none; border-radius: 9px; font-family: 'Sora', sans-serif;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: background 0.18s; margin-top: 4px;
        }
        button:hover { background: #002d7a; }
        .error {
            background: #fef2f2; border: 1px solid #fecaca; color: #c0392b;
            padding: 10px 14px; border-radius: 8px; font-size: 12px;
            margin-bottom: 16px;
        }
        .hint {
            margin-top: 16px; padding: 12px 14px;
            background: #f0f4f8; border-radius: 8px;
            font-size: 11px; color: #5a6a85; line-height: 1.6;
        }
        .hint strong { color: #0d1e38; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="brand-icon">AK</div>
            <div>
                <div class="brand-name">AKADEMUS</div>
                <div class="brand-sub">Admin Panel</div>
            </div>
        </div>

        <h2>Admin Sign In</h2>
        <p>Enter your credentials to access the admin panel.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="post">
            <?= csrf_field() ?>
            <label>Email Address</label>
            <input type="email" name="email" placeholder="admin@akademus.edu.ph" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
            <button type="submit">Sign In to Admin</button>
        </form>

        <div class="hint">
            <strong>Default credentials:</strong><br>
            Email: admin@akademus.edu.ph<br>
            Password: Admin@123
        </div>
    </div>
</body>
</html>