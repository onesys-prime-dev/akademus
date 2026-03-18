<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme = $theme ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Requirement Categories</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
            --accent:       <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
            --accent-text:  <?= esc($theme['accent_text']    ?? '#002d7a') ?>;
            --radius:       <?= esc($theme['border_radius']  ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']   ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; }
        .shell { display: flex; height: 100vh; overflow: hidden; }
        .icon-sidebar { width:64px; background:#0d1e38; display:flex; flex-direction:column; align-items:center; padding:14px 0; gap:4px; flex-shrink:0; }
        .isb-logo { width:36px; height:36px; background:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:var(--accent-text); margin-bottom:14px; }
        .isb-item { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .18s; position:relative; text-decoration:none; }
        .isb-item:hover { background:rgba(255,255,255,.08); }
        .isb-item.on { background:rgba(0,64,166,.5); }
        .isb-tip { position:absolute; left:52px; top:50%; transform:translateY(-50%); background:#1e293b; color:#fff; font-size:10px; padding:4px 9px; border-radius:6px; white-space:nowrap; pointer-events:none; opacity:0; z-index:100; }
        .isb-item:hover .isb-tip { opacity:1; }
        .isb-spacer { flex:1; }
        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }
        .content-area { flex:1; overflow-y:auto; padding:28px; }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
        .page-title { font-size:24px; font-weight:700; color:#0d1e38; }
        .btn { padding:10px 18px; border:none; border-radius:var(--radius); cursor:pointer; font-size:13px; font-weight:700; transition:all .18s; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-danger { background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; }
        .btn-danger:hover { background:#fecaca; }
        .btn-ghost { background:transparent; border:1px solid #dde3f0; color:#5a6a85; }
        .btn-ghost:hover { background:#f9fafb; }
        .table-card { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { background:#f8fafc; padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#5a6a85; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e2e8f0; }
        td { padding:14px 16px; font-size:13px; color:#374151; border-bottom:1px solid #f1f5f9; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafbfc; }
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-blue { background:#dbeafe; color:#1d4ed8; }
        .actions { display:flex; gap:6px; }
        .alert { padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13px; }
        .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; }
        .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
        .empty-state { padding:40px; text-align:center; color:#5a6a85; font-size:14px; }
        svg { width:16px; height:16px; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme]); ?>
    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Requirement Categories</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Requirement Categories</h1>
                <a href="<?= base_url('admin/requirement-categories/create') ?>" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Category
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="table-card">
                <?php if (!empty($categories)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?= esc($cat['category_name']) ?></strong></td>
                            <td style="color:#5a6a85"><?= esc($cat['description'] ?? '—') ?></td>
                            <td><span class="badge badge-blue"><?= (int)$cat['document_count'] ?> docs</span></td>
                            <td>
                                <div class="actions">
                                    <a href="<?= base_url('admin/requirement-categories/' . $cat['category_id'] . '/edit') ?>" class="btn btn-ghost">Edit</a>
                                    <a href="<?= base_url('admin/requirement-categories/' . $cat['category_id'] . '/delete') ?>"
                                       class="btn btn-danger"
                                       onclick="return confirm('Delete this category and all its documents?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-state">No requirement categories yet. <a href="<?= base_url('admin/requirement-categories/create') ?>">Create one</a>.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
