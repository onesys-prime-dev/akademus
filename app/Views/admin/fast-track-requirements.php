<?php
if (! session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme      = $theme      ?? [];
$categories = $categories ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Fast Track Requirements</title>
    <?= theme_favicon_tags() ?>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
            --accent:       <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
            --radius:       <?= esc($theme['border_radius']  ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']   ?? 'Sora') ?>';
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
        .page-intro { max-width: 720px; margin-bottom: 20px; }
        .page-intro p { font-size: 13px; color: #7a8aaa; margin-top: 4px; }
        .notice { max-width: 720px; margin-bottom: 20px; padding: 12px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 600; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .notice a { color: inherit; text-decoration: underline; font-weight: 700; white-space: nowrap; }
        .flash { max-width: 720px; margin-bottom: 20px; padding: 12px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 600; }
        .flash-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .card { background: #fff; border-radius: var(--radius); border: 1px solid #e2e8f0; margin-bottom: 20px; overflow: hidden; max-width: 720px; }
        .card-header { padding: 14px 22px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .card-header h4 { font-size: 14px; font-weight: 700; color: #0d1e38; margin: 0; }
        .card-body { padding: 6px 22px; }
        .setting-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f1f5f9; gap: 16px; }
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
        .save-row { max-width: 720px; margin-top: 8px; }
        .btn-save { background: var(--primary); color: #fff; border: none; padding: 9px 22px; border-radius: var(--radius); font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity .15s; }
        .btn-save:hover { opacity: .88; }
        .empty-state { max-width: 720px; padding: 40px; text-align: center; color: #7a8aaa; font-size: 13px; background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); }
    </style>
</head>
<body>
<div class="shell">

    <?= view('admin/_sidebar', ['theme' => $theme]) ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Fast Track Requirements</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-wrap">

            <div class="page-intro">
                <p>Pick which documents Fast Track applicants must upload right away. Anything left unchecked
                    is deferred — applicants can complete it later from their Dashboard.</p>
            </div>

            <?php if (!$fast_track_enabled): ?>
                <div class="notice">
                    <span>Fast Track is currently turned off system-wide — applicants won't see this option until it's enabled.</span>
                    <a href="<?= base_url('admin/settings') ?>">Enable it in System Settings</a>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (empty($categories)): ?>
                <div class="empty-state">No requirement documents have been set up yet.</div>
            <?php else: ?>
                <form method="post" action="<?= base_url('admin/fast-track-requirements/save') ?>">
                    <?= csrf_field() ?>

                    <?php foreach ($categories as $category): ?>
                        <div class="card">
                            <div class="card-header">
                                <h4><?= esc($category['category_name']) ?></h4>
                            </div>
                            <div class="card-body">
                                <?php foreach ($category['documents'] as $doc): ?>
                                    <div class="setting-row">
                                        <div class="setting-info">
                                            <div class="setting-label"><?= esc($doc['requirement_name']) ?></div>
                                            <?php if (!empty($doc['description'])): ?>
                                                <div class="setting-desc"><?= esc($doc['description']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="toggle-wrap">
                                            <input type="checkbox"
                                                   name="is_fast_track_required[]"
                                                   value="<?= (int) $doc['requirement_id'] ?>"
                                                   id="ft-doc-<?= (int) $doc['requirement_id'] ?>"
                                                   <?= !empty($doc['is_fast_track_required']) ? 'checked' : '' ?>>
                                            <label class="toggle-switch" for="ft-doc-<?= (int) $doc['requirement_id'] ?>"></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="save-row">
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
