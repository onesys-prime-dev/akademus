<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme    = $theme    ?? [];
$preset   = $preset   ?? [];
$formPages = $formPages ?? [];
$action   = $action   ?? 'create';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Applicant Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
            --primary-dark:  <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
            --accent:        <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
            --accent-text:   <?= esc($theme['accent_text']    ?? '#002d7a') ?>;
            --radius:        <?= esc($theme['border_radius']  ?? '8px') ?>;
            --font:          '<?= esc($theme['font_family']   ?? 'Sora') ?>';
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
        .page-header { margin-bottom:24px; }
        .page-title { font-size:24px; font-weight:700; color:#0d1e38; margin-bottom:4px; }

        .form-card { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); padding:28px; max-width:600px; }
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:700; color:#0d1e38; margin-bottom:6px; }
        .form-input, .form-textarea {
            width:100%; padding:10px 12px; border:1px solid #dde3f0; border-radius:6px;
            font-size:13px; font-family:var(--font); outline:none; transition:all .18s;
        }
        .form-input:focus, .form-textarea:focus {
            border-color:var(--primary); background:#f0f4f8;
        }
        .form-textarea { min-height:80px; resize:vertical; }
        .form-check { display:flex; align-items:center; gap:8px; margin-bottom:20px; }
        .form-check-input { width:18px; height:18px; cursor:pointer; accent-color:var(--primary); }
        .form-check-label { cursor:pointer; font-size:13px; }
        .form-help { font-size:11px; color:#5a6a85; margin-top:4px; }

        .section-list { margin:20px 0; }
        .section-item { padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
        .section-check { width:18px; height:18px; cursor:pointer; accent-color:var(--primary); }
        .section-info { flex:1; }
        .section-name { font-size:13px; font-weight:600; color:#0d1e38; }
        .section-count { font-size:11px; color:#5a6a85; }

        .form-actions { display:flex; gap:10px; margin-top:28px; }
        .btn { padding:11px 20px; border:none; border-radius:var(--radius); cursor:pointer; font-size:13px; font-weight:700; transition:all .18s; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-ghost { background:transparent; border:1px solid #dde3f0; color:#5a6a85; }
        .btn-ghost:hover { background:#f9fafb; }

        svg { width:16px; height:16px; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme ?? []]); ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title"><?= ($action === 'edit') ? 'Edit Form' : 'Create Form' ?></div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title"><?= ($action === 'edit') ? 'Edit Applicant Form' : 'Create New Applicant Form' ?></h1>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;">
                    <?php foreach ($errors as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="<?= ($action === 'edit') ? base_url('admin/presets/' . ($preset['id'] ?? '') . '/update') : base_url('admin/presets/store') ?>"
                  class="form-card">
                <?= csrf_field(); ?>

                <div class="form-group">
                    <label for="name" class="form-label">Form Name *</label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="<?= esc($oldInput['name'] ?? $preset['name'] ?? '') ?>"
                           placeholder="e.g., Computer Science Admission" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea"
                              placeholder="What is this form for?"><?= esc($oldInput['description'] ?? $preset['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_global" name="is_global" value="1" class="form-check-input"
                               <?= (!empty($oldInput['is_global']) || !empty($preset['is_global'])) ? 'checked' : '' ?>>
                        <label for="is_global" class="form-check-label">
                            Make this a <strong>global form</strong> (default for all programs)
                        </label>
                    </div>
                    <p class="form-help">If unchecked, you can assign this form to specific programs only.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Sections *</label>
                    <div class="section-list">
                        <?php if (!empty($formPages)): ?>
                            <?php foreach ($formPages as $page):
                                $isSelected = (!empty($oldInput['form_pages']) && in_array($page['id'], (array)$oldInput['form_pages']))
                                           || (!empty($selectedIds) && in_array($page['id'], $selectedIds));
                            ?>
                                <div class="section-item">
                                    <input type="checkbox" name="form_pages[]" value="<?= $page['id']; ?>"
                                           class="section-check" id="page_<?= $page['id']; ?>"
                                           <?= $isSelected ? 'checked' : '' ?>>
                                    <div class="section-info">
                                        <div class="section-name"><?= esc($page['title']); ?></div>
                                        <?php if (!empty($page['description'])): ?>
                                            <div class="section-count"><?= esc($page['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#5a6a85; font-size:13px; padding:12px;">No sections available. Create sections in Form Fields first.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= ($action === 'edit') ? 'Update Form' : 'Create Form' ?>
                    </button>
                    <a href="<?= base_url('admin/presets') ?>" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
