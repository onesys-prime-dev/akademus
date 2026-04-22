<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme    = $theme    ?? [];
$programs = $programs ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Manage Programs</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-header-left {}
        .page-title { font-size: 24px; font-weight: 700; color: #0d1e38; margin-bottom: 4px; }
        .page-sub { font-size: 13px; color: #5a6a85; }

        .btn {
            padding: 9px 18px; border: none; border-radius: var(--radius);
            cursor: pointer; font-size: 13px; font-weight: 700;
            transition: all .18s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #dde3f0; color: #5a6a85; }
        .btn-ghost:hover { background: #f9fafb; color: #0d1e38; }
        .btn-danger-ghost { background: transparent; border: 1px solid #fca5a5; color: #dc2626; }
        .btn-danger-ghost:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
        .alert-error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); overflow: hidden; }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f8fafc; font-size: 11px; font-weight: 700; color: #5a6a85;
            text-transform: uppercase; letter-spacing: .05em;
            padding: 10px 20px; text-align: left; border-bottom: 1px solid #e2e8f0;
        }
        td { padding: 14px 20px; font-size: 13px; color: #0d1e38; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .badge-code {
            display: inline-flex; align-items: center; padding: 3px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 700;
            background: var(--primary-soft, #e8f0ff); color: var(--primary);
        }

        .actions { display: flex; gap: 8px; }

        .empty { text-align: center; padding: 48px; color: #5a6a85; font-size: 13px; }
        .empty-icon { font-size: 32px; margin-bottom: 12px; opacity: .4; }

        /* Delete confirm modal */
        .modal-bg {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 200;
            align-items: center; justify-content: center;
        }
        .modal-bg.open { display: flex; }
        .modal {
            background: #fff; border-radius: var(--radius);
            padding: 28px; width: 100%; max-width: 420px;
        }
        .modal-title { font-size: 16px; font-weight: 800; color: #0d1e38; margin-bottom: 6px; }
        .modal-sub { font-size: 13px; color: #5a6a85; margin-bottom: 22px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme, 'sidebarActive' => 'programs']); ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Manage Programs</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <div class="page-header-left">
                    <h1 class="page-title">Programs</h1>
                    <p class="page-sub">Add, edit, or remove programs offered by the institution</p>
                </div>
                <a href="<?= base_url('admin/programs/create') ?>" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Program
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="card">
                <?php if (empty($programs)): ?>
                    <div class="empty">
                        <div class="empty-icon">🎓</div>
                        No programs yet. <a href="<?= base_url('admin/programs/create') ?>">Add the first one</a>.
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Program Name</th>
                                <th style="width:160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($programs as $program): ?>
                                <tr>
                                    <td><span class="badge-code"><?= esc($program['program_code']) ?></span></td>
                                    <td><strong><?= esc($program['program_name']) ?></strong></td>
                                    <td>
                                        <div class="actions">
                                            <a href="<?= base_url('admin/programs/' . $program['program_id'] . '/edit') ?>"
                                               class="btn btn-ghost btn-sm">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <button class="btn btn-danger-ghost btn-sm"
                                                    onclick="confirmDelete(<?= $program['program_id'] ?>, '<?= esc($program['program_name'], 'js') ?>')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-bg" id="deleteModal">
    <div class="modal">
        <div class="modal-title">Delete Program?</div>
        <div class="modal-sub" id="deleteModalText">
            This action cannot be undone. Programs with active applicants cannot be deleted.
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <a id="deleteConfirmLink" href="#" class="btn btn-primary" style="background:#dc2626;">Delete</a>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteModalText').textContent =
        'Are you sure you want to delete "' + name + '"? This cannot be undone. Programs with enrolled applicants cannot be deleted.';
    document.getElementById('deleteConfirmLink').href = '<?= base_url('admin/programs/') ?>' + id + '/delete';
    document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
</body>
</html>
