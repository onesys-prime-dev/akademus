<?php
if (! session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme        = $theme        ?? [];
$exam_enabled = $exam_enabled ?? true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | System Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
            --primary-soft: <?= esc($theme['primary_soft']  ?? '#e8f0ff') ?>;
            --accent:       <?= esc($theme['accent_color']  ?? '#ffb200') ?>;
            --radius:       <?= esc($theme['border_radius'] ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']  ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; }
        .shell { display: flex; height: 100vh; overflow: hidden; }
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .topbar { height: 52px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; flex-shrink: 0; }
        .topbar-title { font-size: 20px; font-weight: 800; color: #0d1e38; letter-spacing: -.4px; }
        .topbar-admin { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #5a6a85; }
        .topbar-admin-dot { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; }
        .content-wrap { flex: 1; overflow-y: auto; padding: 28px 32px; }
        .card { background: #fff; border-radius: var(--radius); border: 1px solid #e2e8f0; margin-bottom: 28px; overflow: hidden; max-width: 640px; }
        .card-header { padding: 16px 22px; border-bottom: 1px solid #e2e8f0; }
        .card-header h4 { font-size: 15px; font-weight: 700; color: #0d1e38; margin: 0; }
        .card-header p  { font-size: 12px; color: #7a8aaa; margin: 4px 0 0; }
        .card-body { padding: 20px 22px; }
        .setting-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
        .setting-row:last-child { border-bottom: none; }
        .setting-info { flex: 1; }
        .setting-label { font-size: 13px; font-weight: 700; color: #2d3a4f; }
        .setting-desc  { font-size: 12px; color: #7a8aaa; margin-top: 2px; }
        /* Toggle switch */
        .toggle-wrap { position: relative; flex-shrink: 0; }
        .toggle-wrap input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .toggle-switch {
            display: inline-block;
            width: 44px; height: 24px;
            background: #cbd5e0;
            border-radius: 999px;
            cursor: pointer;
            transition: background .2s;
            position: relative;
        }
        .toggle-wrap input:checked + .toggle-switch { background: #22c55e; }
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px; left: 3px;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s;
            box-shadow: 0 1px 3px rgba(0,0,0,.15);
        }
        .toggle-wrap input:checked + .toggle-switch::after { transform: translateX(20px); }
        .toggle-state { font-size: 11px; font-weight: 700; margin-top: 4px; text-align: center; color: #7a8aaa; }
        .toggle-wrap input:checked ~ .toggle-state { color: #15803d; }
        .save-row { margin-top: 20px; }
        .btn-save { background: var(--primary); color: #fff; border: none; padding: 9px 22px; border-radius: var(--radius); font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity .15s; }
        .btn-save:hover { opacity: .88; }
        .flash { margin-bottom: 20px; padding: 12px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 600; max-width: 640px; }
        .flash-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
<div class="shell">

    <?= view('admin/_sidebar', ['theme' => $theme]) ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">System Settings</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-wrap">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('admin/settings/save') ?>">
                <?= csrf_field() ?>

                <div class="card">
                    <div class="card-header">
                        <h4>Enrollment Features</h4>
                        <p>Enable or disable optional features across the admissions system.</p>
                    </div>
                    <div class="card-body">

                        <div class="setting-row">
                            <div class="setting-info">
                                <div class="setting-label">Enable Entrance Exam</div>
                                <div class="setting-desc">
                                    When ON, exam scheduling is visible to Admission Officers and applicants.<br>
                                    When OFF, all exam-related UI is hidden system-wide.
                                </div>
                            </div>
                            <div class="toggle-wrap" style="margin-left:24px;display:flex;flex-direction:column;align-items:center;gap:4px;">
                                <input type="checkbox" name="exam_enabled" id="toggle-exam"
                                    <?= $exam_enabled ? 'checked' : '' ?>>
                                <label class="toggle-switch" for="toggle-exam"></label>
                                <span class="toggle-state" id="toggle-exam-label"><?= $exam_enabled ? 'ON' : 'OFF' ?></span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="save-row">
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.getElementById('toggle-exam').addEventListener('change', function () {
    document.getElementById('toggle-exam-label').textContent = this.checked ? 'ON' : 'OFF';
});
</script>
</body>
</html>
