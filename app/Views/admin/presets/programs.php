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
    <title>Akademus | Program Assignments</title>
    <?= theme_favicon_tags() ?>
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

        /* sidebar styles are in admin/_sidebar.php */

        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        .content-area { flex:1; overflow-y:auto; padding:28px; }
        .page-header { margin-bottom:24px; }
        .page-title { font-size:24px; font-weight:700; color:#0d1e38; margin-bottom:4px; }
        .page-sub { font-size:13px; color:#5a6a85; }

        .alert { padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; }
        .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; }
        .alert-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }

        .card { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { background:#f8fafc; font-size:11px; font-weight:700; color:#5a6a85; text-transform:uppercase; letter-spacing:.05em; padding:10px 16px; text-align:left; border-bottom:1px solid #e2e8f0; }
        td { padding:12px 16px; font-size:13px; color:#0d1e38; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafafa; }

        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-preset  { background:#dbeafe; color:#1d4ed8; }
        .badge-global  { background:#f3f4f6; color:#6b7280; }

        .btn { padding:7px 14px; border:none; border-radius:var(--radius); cursor:pointer; font-size:12px; font-weight:700; transition:all .18s; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-ghost { background:transparent; border:1px solid #dde3f0; color:#5a6a85; }
        .btn-ghost:hover { background:#f9fafb; }

        /* Modal */
        .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:200; align-items:center; justify-content:center; }
        .modal-bg.open { display:flex; }
        .modal { background:#fff; border-radius:var(--radius); padding:28px; width:100%; max-width:440px; }
        .modal-title { font-size:16px; font-weight:800; color:#0d1e38; margin-bottom:4px; }
        .modal-sub { font-size:12px; color:#5a6a85; margin-bottom:20px; }
        .form-label { display:block; font-size:12px; font-weight:700; color:#0d1e38; margin-bottom:6px; }
        .form-select { width:100%; padding:9px 12px; border:1px solid #dde3f0; border-radius:6px; font-size:13px; outline:none; }
        .form-select:focus { border-color:var(--primary); }
        .modal-actions { display:flex; gap:10px; margin-top:24px; justify-content:flex-end; }

        svg { width:16px; height:16px; }
        .empty { text-align:center; padding:40px; color:#5a6a85; font-size:13px; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme]); ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Program Assignments</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Program Assignments</h1>
                <p class="page-sub">Assign form presets to specific programs</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Code</th>
                            <th>Assigned Preset</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($programs)): ?>
                            <?php foreach ($programs as $program): ?>
                                <tr>
                                    <td><strong><?= esc($program['program_name']) ?></strong></td>
                                    <td style="color:#5a6a85;"><?= esc($program['program_code']) ?></td>
                                    <td>
                                        <?php if (!empty($program['preset_id']) && isset($presetMap[$program['preset_id']])): ?>
                                            <span class="badge badge-preset"><?= esc($presetMap[$program['preset_id']]['name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-global">Global / Default</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-ghost"
                                                onclick="openModal(<?= $program['program_id'] ?>, '<?= esc($program['program_name'], 'js') ?>', <?= (int)($program['preset_id'] ?? 0) ?>)">
                                            Assign Preset
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty">No programs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal-bg" id="modalBg">
    <div class="modal">
        <div class="modal-title">Assign Preset</div>
        <div class="modal-sub" id="modalProgramName"></div>
        <form method="POST" action="<?= base_url('admin/programs/assign-preset') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="program_id" id="modalProgramId">
            <label class="form-label">Select Preset</label>
            <select name="preset_id" id="modalPresetId" class="form-select">
                <option value="">— Use Global / Default —</option>
                <?php foreach ($presets as $preset): ?>
                    <?php if (!$preset['is_global']): ?>
                        <option value="<?= $preset['id'] ?>"><?= esc($preset['name']) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(programId, programName, currentPresetId) {
    document.getElementById('modalProgramId').value = programId;
    document.getElementById('modalProgramName').textContent = programName;
    const sel = document.getElementById('modalPresetId');
    sel.value = currentPresetId || '';
    document.getElementById('modalBg').classList.add('open');
}
function closeModal() {
    document.getElementById('modalBg').classList.remove('open');
}
document.getElementById('modalBg').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
</body>
</html>
