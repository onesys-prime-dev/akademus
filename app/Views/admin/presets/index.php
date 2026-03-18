<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme   = $theme   ?? [];
$presets = $presets ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Applicant Forms</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
            --primary-dark:  <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
            --primary-light: <?= esc($theme['primary_light']  ?? '#1a55c0') ?>;
            --primary-soft:  <?= esc($theme['primary_soft']   ?? '#e8f0ff') ?>;
            --sidebar:       <?= esc($theme['sidebar_color']  ?? '#002d7a') ?>;
            --accent:        <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
            --accent-text:   <?= esc($theme['accent_text']    ?? '#002d7a') ?>;
            --radius:        <?= esc($theme['border_radius']  ?? '8px') ?>;
            --font:          '<?= esc($theme['font_family']   ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; overflow: hidden; }
        .shell { display: flex; height: 100vh; overflow: hidden; }

        .icon-sidebar { width:64px; background:#0d1e38; display:flex; flex-direction:column; align-items:center; padding:14px 0; gap:4px; flex-shrink:0; z-index:10; }
        .isb-logo { width:36px; height:36px; background:var(--accent,#ffb200); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:var(--accent-text,#002d7a); margin-bottom:14px; overflow:hidden; }
        .isb-logo img { width:100%; height:100%; object-fit:cover; }
        .isb-item { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:18px; transition:all .18s; border:1px solid transparent; position:relative; text-decoration:none; color:inherit; }
        .isb-item:hover { background:rgba(255,255,255,.08); }
        .isb-item.on { background:rgba(0,64,166,.5); border-color:rgba(0,64,166,.6); }
        .isb-tip { position:absolute; left:52px; top:50%; transform:translateY(-50%); background:#1e293b; color:#fff; font-size:10px; font-weight:600; padding:4px 9px; border-radius:6px; white-space:nowrap; pointer-events:none; opacity:0; transition:opacity .18s; z-index:100; }
        .isb-item:hover .isb-tip { opacity:1; }
        .isb-spacer { flex:1; }

        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        .content-area { flex:1; overflow-y:auto; padding:28px; }
        .page-header { margin-bottom:24px; }
        .page-title { font-size:24px; font-weight:700; color:#0d1e38; margin-bottom:4px; }
        .page-desc { font-size:14px; color:#5a6a85; }

        .presets-grid { display:grid; grid-template-columns:1fr; gap:14px; }
        .preset-card { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); padding:18px; display:flex; justify-content:space-between; align-items:center; transition:all .2s; }
        .preset-card:hover { border-color:#0040a6; box-shadow:0 4px 12px rgba(0,64,166,.1); }
        .preset-info { flex:1; }
        .preset-name { font-size:15px; font-weight:700; color:#0d1e38; margin-bottom:4px; }
        .preset-desc { font-size:12px; color:#5a6a85; }
        .preset-badge { display:inline-block; font-size:10px; font-weight:700; padding:3px 8px; border-radius:20px; margin-right:8px; }
        .preset-badge.global { background:#d1fae5; color:#065f46; }
        .preset-badge.specific { background:#f3e8ff; color:#5b21b6; }

        .preset-actions { display:flex; gap:8px; }
        .btn { padding:7px 14px; border:none; border-radius:6px; cursor:pointer; font-size:12px; font-weight:700; transition:all .18s; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-edit { background:#e8f0ff; color:var(--primary); }
        .btn-edit:hover { background:#d0daf5; }
        .btn-delete { background:#fef2f2; color:#dc2626; }
        .btn-delete:hover { background:#fee2e2; }

        .empty-state { text-align:center; padding:48px; color:#5a6a85; }
        .empty-icon { font-size:48px; margin-bottom:12px; }

        .btn-new { background:var(--primary); color:#fff; padding:11px 20px; border-radius:var(--radius); font-size:13px; font-weight:700; border:none; cursor:pointer; }
        .btn-new:hover { background:var(--primary-dark); }

        svg { width:16px; height:16px; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme ?? []]); ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Applicant Forms</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Applicant Forms</h1>
                <p class="page-desc">Create and manage form presets for different programs</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:12px 16px; border-radius:6px; margin-bottom:16px;">
                    ✓ <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($presets)): ?>
                <button class="btn-new" onclick="window.location='<?= base_url('admin/presets/create') ?>'">
                    + New Form
                </button>
                <div class="presets-grid" style="margin-top:20px;">
                    <?php foreach ($presets as $preset): ?>
                        <div class="preset-card">
                            <div class="preset-info">
                                <div class="preset-name"><?= esc($preset['name']); ?></div>
                                <div style="margin-top:6px;">
                                    <span class="preset-badge <?= $preset['is_global'] ? 'global' : 'specific' ?>">
                                        <?= $preset['is_global'] ? '🌍 Global' : '📌 Course-Specific' ?>
                                    </span>
                                    <span class="preset-badge" style="background:#f3f4f6; color:#374151;">
                                        <?= $preset['is_active'] ? '✓ Active' : '○ Inactive' ?>
                                    </span>
                                </div>
                                <p class="preset-desc" style="margin-top:8px;"><?= esc($preset['description']); ?></p>
                            </div>
                            <div class="preset-actions">
                                <a href="<?= base_url('admin/presets/' . $preset['id'] . '/edit') ?>" class="btn btn-edit">
                                    ✎ Edit
                                </a>
                                <button class="btn btn-delete" onclick="if(confirm('Delete this form?')) window.location='<?= base_url('admin/presets/' . $preset['id'] . '/delete') ?>'">
                                    🗑
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <p>No applicant forms created yet</p>
                    <button class="btn-new" onclick="window.location='<?= base_url('admin/presets/create') ?>'">
                        Create Your First Form
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
