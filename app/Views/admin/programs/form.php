<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme   = $theme   ?? [];
$errors  = $errors  ?? [];
$old     = $old     ?? ['program_name' => '', 'program_code' => ''];
$program = $program ?? null;
$isEdit  = $program !== null;
$pageTitle = $isEdit ? 'Edit Program' : 'Create Program';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= esc($pageTitle) ?></title>
    <?= theme_favicon_tags() ?>
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

        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        .topbar {
            height: 52px; background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; flex-shrink: 0;
        }
        .topbar-title { font-size: 20px; font-weight: 800; color: #0d1e38; }
        .topbar-admin { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #5a6a85; }
        .topbar-admin-dot {
            width: 30px; height: 30px; border-radius: 50%; background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800; color: #fff;
        }

        .content-area { flex: 1; overflow-y: auto; padding: 28px; }

        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #5a6a85; margin-bottom: 20px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb-sep { color: #c0cad8; }

        .page-title { font-size: 24px; font-weight: 700; color: #0d1e38; margin-bottom: 24px; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); padding: 28px; max-width: 560px; }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 700;
            color: #0d1e38; margin-bottom: 7px; text-transform: uppercase; letter-spacing: .04em;
        }
        .form-label span { color: #dc2626; margin-left: 2px; }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1px solid #dde3f0;
            border-radius: 6px; font-size: 13px; font-family: inherit;
            color: #0d1e38; outline: none; transition: border-color .18s;
        }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft, #e8f0ff); }
        .form-input.is-invalid { border-color: #dc2626; }
        .form-input.is-invalid:focus { box-shadow: 0 0 0 3px #fee2e2; }
        .form-hint { font-size: 11px; color: #5a6a85; margin-top: 5px; }
        .error-text { font-size: 12px; color: #dc2626; margin-top: 5px; font-weight: 600; }

        .form-actions { display: flex; gap: 10px; margin-top: 28px; }

        .btn {
            padding: 10px 20px; border: none; border-radius: var(--radius);
            cursor: pointer; font-size: 13px; font-weight: 700;
            transition: all .18s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #dde3f0; color: #5a6a85; }
        .btn-ghost:hover { background: #f9fafb; color: #0d1e38; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme, 'sidebarActive' => 'programs']); ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title"><?= esc($pageTitle) ?></div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="breadcrumb">
                <a href="<?= base_url('admin/programs') ?>">Programs</a>
                <span class="breadcrumb-sep">/</span>
                <span><?= esc($pageTitle) ?></span>
            </div>

            <h1 class="page-title"><?= esc($pageTitle) ?></h1>

            <?php if (!empty($errors['_general'])): ?>
                <div style="padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
                    <?= esc($errors['_general']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="<?= $isEdit
                    ? base_url('admin/programs/' . $program['program_id'] . '/edit')
                    : base_url('admin/programs/create') ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="program_name">
                            Program Name <span>*</span>
                        </label>
                        <input
                            type="text"
                            id="program_name"
                            name="program_name"
                            class="form-input <?= isset($errors['program_name']) ? 'is-invalid' : '' ?>"
                            value="<?= esc($old['program_name']) ?>"
                            placeholder="e.g., Bachelor of Science in Computer Science"
                            required
                            autocomplete="off"
                        >
                        <?php if (isset($errors['program_name'])): ?>
                            <div class="error-text"><?= esc($errors['program_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="program_code">
                            Program Code <span>*</span>
                        </label>
                        <input
                            type="text"
                            id="program_code"
                            name="program_code"
                            class="form-input <?= isset($errors['program_code']) ? 'is-invalid' : '' ?>"
                            value="<?= esc($old['program_code']) ?>"
                            placeholder="e.g., BSCS"
                            required
                            autocomplete="off"
                            style="text-transform:uppercase;"
                        >
                        <div class="form-hint">Short code shown on applicant records. Automatically uppercased.</div>
                        <?php if (isset($errors['program_code'])): ?>
                            <div class="error-text"><?= esc($errors['program_code']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?= $isEdit ? 'Save Changes' : 'Create Program' ?>
                        </button>
                        <a href="<?= base_url('admin/programs') ?>" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Force uppercase as user types in the code field
document.getElementById('program_code').addEventListener('input', function() {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});
</script>
</body>
</html>
