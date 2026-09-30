<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme   = $theme   ?? [];
$pages   = $pages   ?? [];
$grouped = $grouped ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Akademus | Form Field Manager</title>
    <?= theme_favicon_tags() ?>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        /* sidebar styles are in admin/_sidebar.php */

        /* ── MAIN ── */
        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; letter-spacing:-.4px; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary,#0040a6); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        /* ── TWO-PANEL LAYOUT ── */
        .content-wrap { flex:1; display:flex; overflow:hidden; }

        /* Left: page list */
        .pages-panel { width:260px; flex-shrink:0; background:#fff; border-right:1px solid #e2e8f0; display:flex; flex-direction:column; overflow:hidden; }
        .pages-panel-head { padding:16px 18px 12px; border-bottom:1px solid #eee; }
        .pages-panel-head h3 { font-size:13px; font-weight:700; color:#222; margin-bottom:2px; }
        .pages-panel-head p { font-size:11px; color:#aaa; }
        .pages-list { flex:1; overflow-y:auto; padding:8px 0; }
        .page-item { display:flex; align-items:center; gap:10px; padding:9px 14px; cursor:pointer; border-left:3px solid transparent; transition:all .15s; user-select:none; }
        .page-item:hover { background:#f5f7ff; }
        .page-item.active { background:var(--primary-soft,#e8f0ff); border-left-color:var(--primary,#0040a6); }
        .page-item.disabled-page { opacity:.5; }
        .page-drag-handle { color:#ccc; cursor:grab; font-size:13px; flex-shrink:0; }
        .page-drag-handle:active { cursor:grabbing; }
        .page-item-info { flex:1; min-width:0; }
        .page-item-title { font-size:13px; font-weight:600; color:#222; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center; gap:6px; }
        .page-item-title .badge-sys { font-size:9px; font-weight:700; padding:1px 5px; border-radius:3px; background:#e8f0ff; color:var(--primary,#0040a6); }
        .page-item-title .badge-off { font-size:9px; font-weight:700; padding:1px 5px; border-radius:3px; background:#f0f0f0; color:#999; }
        .page-item-count { font-size:11px; color:#aaa; margin-top:1px; }
        .page-item-actions { display:flex; gap:2px; opacity:0; transition:opacity .15s; flex-shrink:0; }
        .page-item:hover .page-item-actions { opacity:1; }
        .pi-btn { width:24px; height:24px; border-radius:5px; border:1px solid #eee; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#888; transition:all .15s; }
        .pi-btn:hover { background:#f0f0f0; color:#333; }
        .pi-btn.danger:hover { background:#fff0f0; color:#c0392b; border-color:#f5c6c6; }
        .pages-add-btn { margin:10px 14px; display:flex; align-items:center; gap:7px; padding:9px 14px; border-radius:var(--radius,8px); font-size:12px; font-weight:600; background:var(--primary-soft,#e8f0ff); color:var(--primary,#0040a6); border:1px dashed var(--primary,#0040a6); cursor:pointer; transition:all .15s; font-family:var(--font,'Sora'),sans-serif; }
        .pages-add-btn:hover { background:var(--primary,#0040a6); color:#fff; }

        /* Right: fields panel */
        .fields-panel { flex:1; overflow-y:auto; padding:24px 28px; display:flex; flex-direction:column; gap:0; }

        /* ── SHARED BUTTON STYLES ── */
        .ff-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 20px; border-radius:var(--radius,8px); font-size:13px; font-weight:600; border:none; cursor:pointer; transition:all .18s ease; font-family:var(--font,'Sora'),sans-serif; }
        .ff-btn-primary { background:var(--primary,#0040a6); color:#fff; }
        .ff-btn-primary:hover { background:var(--primary-dark,#002d7a); }
        .ff-btn-ghost { background:#fff; color:#444; border:1px solid #ddd; }
        .ff-btn-ghost:hover { background:#f5f5f5; }

        /* ── PAGE HEADER ── */
        .ff-page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #eee; }
        .ff-page-header-left h2 { font-size:18px; font-weight:700; color:#111; display:flex; align-items:center; gap:10px; }
        .ff-page-header-left p { font-size:13px; color:#888; margin-top:3px; }
        .page-status-badge { font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; }
        .page-status-badge.enabled { background:#e8f5e9; color:#2e7d32; }
        .page-status-badge.disabled { background:#f0f0f0; color:#999; }

        /* ── SECTION CARD ── */
       .ff-section-card { background:#fff; border:1px solid rgba(0,0,0,.09); border-radius:var(--radius,8px); box-shadow:0 1px 6px rgba(0,0,0,.05); overflow:visible; margin-bottom:16px; }
        .ff-section-head { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; background:#fafafa; border-bottom:1px solid #eee; }
        .ff-section-head h4 { font-size:14px; font-weight:700; color:#222; display:flex; align-items:center; gap:8px; }
        .ff-section-head h4 span { font-size:11px; font-weight:500; color:#aaa; padding:2px 8px; background:#f0f0f0; border-radius:20px; }

        /* ── FIELD ROW ── */
        .ff-col-headers { display:grid; grid-template-columns:24px 1fr 120px 140px 100px 80px; gap:12px; padding:8px 20px; background:#f7f8fa; border-bottom:1px solid #eee; }
        .ff-col-headers span { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#aaa; }
        .ff-field-row { display:grid; grid-template-columns:24px 1fr 120px 140px 100px 80px; align-items:center; gap:12px; padding:12px 20px; border-bottom:1px solid #f5f5f5; transition:background .15s; }
        .ff-field-row:last-child { border-bottom:none; }
        .ff-field-row:hover { background:#fafcff; }
        .ff-field-row.disabled-row { opacity:.45; }
        .ff-drag-handle { color:#ccc; cursor:grab; display:flex; align-items:center; }
        .ff-drag-handle:active { cursor:grabbing; }
        .ff-field-name { display:flex; flex-direction:column; gap:2px; }
        .ff-field-label { font-size:13px; font-weight:600; color:#222; display:flex; align-items:center; gap:6px; }
        .ff-field-key { font-size:11px; color:#aaa; font-family:'Courier New',monospace; }
        .badge-system { font-size:10px; font-weight:700; padding:1px 6px; border-radius:4px; background:#e8f0ff; color:var(--primary,#0040a6); }
        .badge-custom { font-size:10px; font-weight:700; padding:1px 6px; border-radius:4px; background:#fff3e0; color:#e65100; }
        .ff-field-type { display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; padding:3px 10px; border-radius:6px; background:#f0f0f0; color:#555; text-transform:uppercase; letter-spacing:.4px; }
        .ff-field-placeholder { font-size:12px; color:#999; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
        .ff-toggle-wrap { display:flex; align-items:center; gap:6px; }
        .ff-toggle { position:relative; width:38px; height:20px; flex-shrink:0; }
        .ff-toggle input { display:none; }
        .ff-toggle-track { position:absolute; inset:0; background:#ddd; border-radius:20px; transition:background .2s; cursor:pointer; }
        .ff-toggle input:checked + .ff-toggle-track { background:var(--primary,#0040a6); }
        .ff-toggle-track::after { content:''; position:absolute; top:2px; left:2px; width:16px; height:16px; border-radius:50%; background:#fff; transition:left .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
        .ff-toggle input:checked + .ff-toggle-track::after { left:20px; }
        .ff-toggle-label { font-size:12px; font-weight:600; color:#999; min-width:28px; }
        .ff-row-actions { display:flex; gap:4px; justify-content:flex-end; }
        .ff-icon-btn { width:28px; height:28px; border-radius:6px; border:1px solid #eee; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#888; transition:all .15s; }
        .ff-icon-btn:hover { background:#f0f0f0; color:#333; }
        .ff-icon-btn.delete:hover { background:#fff0f0; color:#c0392b; border-color:#f5c6c6; }

        /* ── ADD ROW ── */
        .ff-add-row { padding:16px 20px; background:#fafbff; border-top:1px dashed #d0daf5; display:none; }
        .ff-add-row.open { display:block; }
        .ff-add-row-title { font-size:12px; font-weight:700; color:var(--primary,#0040a6); text-transform:uppercase; letter-spacing:.5px; margin-bottom:12px; }
        .ff-add-grid { display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:10px; align-items:end; }
        .ff-add-field { display:flex; flex-direction:column; gap:4px; }
        .ff-add-field label { font-size:11px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.4px; }
        .ff-add-field input, .ff-add-field select { height:36px; border:1px solid #ddd; border-radius:6px; padding:0 10px; font-size:13px; font-family:var(--font,'Sora'),sans-serif; background:#fff; outline:none; transition:border-color .15s; }
        .ff-add-field input:focus, .ff-add-field select:focus { border-color:var(--primary,#0040a6); background:var(--primary-soft,#e8f0ff); }

        /* ── SAVE BAR ── */
        .ff-save-bar { position:sticky; bottom:0; background:#fff; border-top:2px solid var(--primary,#0040a6); padding:14px 20px; display:flex; justify-content:space-between; align-items:center; z-index:10; box-shadow:0 -4px 20px rgba(0,0,0,.08); }
        .ff-save-bar-msg { font-size:13px; color:#666; }
        .ff-save-bar-msg strong { color:var(--primary,#0040a6); }
        .ff-save-actions { display:flex; gap:10px; }

        /* ── TOAST ── */
        .ff-toast { position:fixed; bottom:80px; right:30px; background:#1a1a2e; color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; font-weight:500; z-index:999; opacity:0; transform:translateY(10px); transition:all .25s ease; pointer-events:none; }
        .ff-toast.show { opacity:1; transform:translateY(0); }
        .ff-toast.success { border-left:3px solid #4caf50; }
        .ff-toast.error   { border-left:3px solid #f44336; }

        /* ── EDIT MODAL ── */
        .ff-modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; display:none; align-items:center; justify-content:center; }
        .ff-modal-bg.open { display:flex; }
        .ff-modal { background:#fff; border-radius:12px; padding:28px 32px; width:480px; max-width:92vw; box-shadow:0 20px 60px rgba(0,0,0,.2); }
        .ff-modal h3 { font-size:16px; font-weight:700; margin-bottom:4px; }
        .ff-modal p { font-size:13px; color:#777; margin-bottom:20px; }
        .ff-modal-fields { display:flex; flex-direction:column; gap:14px; margin-bottom:24px; }
        .ff-modal-field { display:flex; flex-direction:column; gap:4px; }
        .ff-modal-field label { font-size:12px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.4px; }
        .ff-modal-field input, .ff-modal-field select, .ff-modal-field textarea { border:1px solid #ddd; border-radius:7px; padding:9px 12px; font-size:13px; font-family:var(--font,'Sora'),sans-serif; outline:none; transition:border-color .15s; resize:vertical; }
        .ff-modal-field input:focus, .ff-modal-field select:focus, .ff-modal-field textarea:focus { border-color:var(--primary,#0040a6); background:var(--primary-soft,#e8f0ff); }
        .ff-modal-actions { display:flex; justify-content:flex-end; gap:10px; }
        .ff-req-check { width:16px; height:16px; accent-color:var(--primary,#0040a6); cursor:pointer; }

        /* ── PAGE MODAL ── */
        .page-modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; display:none; align-items:center; justify-content:center; }
        .page-modal-bg.open { display:flex; }
        .page-modal { background:#fff; border-radius:12px; padding:28px 32px; width:440px; max-width:92vw; box-shadow:0 20px 60px rgba(0,0,0,.2); }
        .page-modal h3 { font-size:16px; font-weight:700; margin-bottom:4px; }
        .page-modal p { font-size:13px; color:#777; margin-bottom:20px; }
        .pm-field { display:flex; flex-direction:column; gap:4px; margin-bottom:14px; }
        .pm-field label { font-size:12px; font-weight:700; color:#555; text-transform:uppercase; }
        .pm-field input, .pm-field textarea { border:1px solid #ddd; border-radius:7px; padding:9px 12px; font-size:13px; font-family:var(--font,'Sora'),sans-serif; outline:none; transition:border-color .15s; resize:vertical; }
        .pm-field input:focus, .pm-field textarea:focus { border-color:var(--primary,#0040a6); background:var(--primary-soft,#e8f0ff); }
        .pm-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:4px; }

        /* ── EMPTY STATE ── */
        .ff-empty { padding:48px; text-align:center; color:#bbb; font-size:13px; }
        .ff-empty svg { opacity:.3; margin-bottom:10px; }

        /* ── DISABLED PAGE OVERLAY ── */
        .disabled-page-notice { background:#fff8e1; border:1px solid #ffe082; border-radius:var(--radius,8px); padding:14px 18px; display:flex; align-items:center; gap:12px; font-size:13px; color:#795548; margin-bottom:16px; }
    </style>
</head>
<body>
<div class="shell">

    <?php echo view('admin/_sidebar', ['theme' => $theme ?? []]); ?>

    <!-- MAIN -->
    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Form Field Manager</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-wrap">

            <!-- LEFT: PAGES PANEL -->
            <div class="pages-panel">
                <div class="pages-panel-head">
                    <h3>Form Pages</h3>
                    <p>Drag to reorder. Click to manage fields.</p>
                </div>

                <div class="pages-list" id="pages-sortable">
                    <?php foreach ($pages as $pg):
                        $fieldCount = 0;
                        if (isset($grouped[$pg['id']])) {
                            foreach ($grouped[$pg['id']] as $section) $fieldCount += count($section);
                        }
                    ?>
                    <div class="page-item <?= $pg['is_enabled'] ? '' : 'disabled-page' ?> <?= $pg['id'] === ($pages[0]['id'] ?? 0) ? 'active' : '' ?>"
                         data-page-id="<?= $pg['id'] ?>"
                         onclick="ffSelectPage(<?= $pg['id'] ?>, this)">
                        <div class="page-drag-handle">
                            <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor">
                                <circle cx="3" cy="2"  r="1.3"/><circle cx="7" cy="2"  r="1.3"/>
                                <circle cx="3" cy="7"  r="1.3"/><circle cx="7" cy="7"  r="1.3"/>
                                <circle cx="3" cy="12" r="1.3"/><circle cx="7" cy="12" r="1.3"/>
                            </svg>
                        </div>
                        <div class="page-item-info">
                            <div class="page-item-title">
                                <?= esc($pg['title']) ?>
                                <?php if ($pg['is_system']): ?><span class="badge-sys">SYS</span><?php endif; ?>
                                <?php if (!$pg['is_enabled']): ?><span class="badge-off">OFF</span><?php endif; ?>
                            </div>
                            <div class="page-item-count"><?= $fieldCount ?> field<?= $fieldCount !== 1 ? 's' : '' ?></div>
                        </div>
                        <div class="page-item-actions">
                            <button class="pi-btn" onclick="ffEditPageModal(<?= $pg['id'] ?>, <?= htmlspecialchars(json_encode($pg['title']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($pg['description'] ?? ''), ENT_QUOTES) ?>); event.stopPropagation();" title="Rename">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button class="pi-btn" onclick="ffTogglePage(<?= $pg['id'] ?>, this); event.stopPropagation();" title="<?= $pg['is_enabled'] ? 'Disable' : 'Enable' ?>">
                                <?php if ($pg['is_enabled']): ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <?php else: ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <?php endif; ?>
                            </button>
                            <?php if (!$pg['is_system']): ?>
                            <button class="pi-btn danger" onclick="ffDeletePage(<?= $pg['id'] ?>); event.stopPropagation();" title="Delete">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="pages-add-btn" onclick="ffAddPageModal()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add New Page
                </button>
            </div>

            <!-- RIGHT: FIELDS PANEL -->
            <div class="fields-panel" id="fields-panel">
                <?php
                $firstPage = $pages[0] ?? null;
                if ($firstPage):
                    $pGrouped = $grouped[$firstPage['id']] ?? [];
                    $fieldCount = 0;
                    foreach ($pGrouped as $s) $fieldCount += count($s);
                ?>

                <div class="ff-page-header">
                    <div class="ff-page-header-left">
                        <h2>
                            <?= esc($firstPage['title']) ?>
                            <span class="page-status-badge <?= $firstPage['is_enabled'] ? 'enabled' : 'disabled' ?>">
                                <?= $firstPage['is_enabled'] ? 'Enabled' : 'Disabled' ?>
                            </span>
                        </h2>
                        <p><?= esc($firstPage['description'] ?? '') ?></p>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button class="ff-btn ff-btn-ghost" onclick="ffResetAll()" style="font-size:12px;padding:7px 16px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                            Reset All
                        </button>
                        <button class="ff-btn ff-btn-primary" onclick="ffSaveAll()" style="font-size:12px;padding:7px 16px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>

                <?php if (!$firstPage['is_enabled']): ?>
                <div class="disabled-page-notice">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    This page is currently <strong>&nbsp;disabled&nbsp;</strong>. Applicants will skip it. You can still edit its fields here.
                </div>
                <?php endif; ?>

                <?php foreach ($pGrouped as $sectionId => $sectionFields): ?>
                <div class="ff-section-card">
                    <div class="ff-section-head">
                        <h4><?= esc(ucwords(str_replace(['-','_'], ' ', preg_replace('/^step\d+-/', '', $sectionId)))) ?> <span>Page <?= $firstPage['id'] ?></span></h4>
                        <button class="ff-btn ff-btn-ghost" onclick="ffOpenAddRow('<?= esc($sectionId) ?>-add')" style="padding:6px 14px;font-size:12px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Field
                        </button>
                    </div>
                    <div class="ff-col-headers">
                        <span></span><span>Field</span><span>Type</span><span>Placeholder</span><span>Visible</span><span>Actions</span>
                    </div>
                    <div class="ff-fields-list" id="<?= esc($sectionId) ?>">
                        <?php foreach ($sectionFields as $f): ?>
                        <div class="ff-field-row <?= $f['is_visible'] ? '' : 'disabled-row' ?>"
                             data-key="<?= esc($f['field_key']) ?>"
                             data-page="<?= (int)$f['page_id'] ?>"
                             data-section="<?= esc($sectionId) ?>"
                             data-system="<?= $f['is_system'] ? '1' : '0' ?>">
                            <div class="ff-drag-handle">
                                <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><circle cx="4" cy="3" r="1.5"/><circle cx="8" cy="3" r="1.5"/><circle cx="4" cy="8" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="4" cy="13" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg>
                            </div>
                            <div class="ff-field-name">
                                <div class="ff-field-label">
                                    <?= esc($f['label']) ?>
                                    <span class="<?= $f['is_system'] ? 'badge-system' : 'badge-custom' ?>"><?= $f['is_system'] ? 'SYSTEM' : 'CUSTOM' ?></span>
                                </div>
                                <div class="ff-field-key"><?= esc($f['field_key']) ?></div>
                            </div>
                            <div class="ff-field-type"><?= strtoupper(esc($f['field_type'])) ?></div>
                            <div class="ff-field-placeholder"><?= esc($f['placeholder'] ?: '—') ?></div>
                            <div class="ff-toggle-wrap">
                                <label class="ff-toggle">
                                    <input type="checkbox" <?= $f['is_visible'] ? 'checked' : '' ?> onchange="ffToggleVisible(this)">
                                    <div class="ff-toggle-track"></div>
                                </label>
                                <span class="ff-toggle-label"><?= $f['is_visible'] ? 'On' : 'Off' ?></span>
                            </div>
                            <div class="ff-row-actions">
                                <button class="ff-icon-btn" onclick="ffEditField(this)" title="Edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <?php if (!$f['is_system']): ?>
                                <button class="ff-icon-btn delete" onclick="ffDeleteField(this)" title="Delete">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ff-add-row" id="<?= esc($sectionId) ?>-add">
                        <div class="ff-add-row-title">+ New Custom Field</div>
                        <div class="ff-add-grid">
                            <div class="ff-add-field"><label>Label</label><input type="text" id="add-label-<?= esc($sectionId) ?>" placeholder="e.g. Religion"></div>
                            <div class="ff-add-field"><label>Key</label><input type="text" id="add-key-<?= esc($sectionId) ?>" placeholder="auto-generated"></div>
                            <div class="ff-add-field"><label>Type</label>
                                <select id="add-type-<?= esc($sectionId) ?>">
                                    <option value="text">Text</option><option value="number">Number</option>
                                    <option value="email">Email</option><option value="tel">Phone</option>
                                    <option value="date">Date</option><option value="select">Select</option>
                                    <option value="textarea">Textarea</option>
                                </select>
                            </div>
                            <div class="ff-add-field"><label>Placeholder</label><input type="text" id="add-placeholder-<?= esc($sectionId) ?>" placeholder="Optional"></div>
                            <div style="display:flex;gap:6px;">
                                <button class="ff-btn ff-btn-primary" onclick="ffAddCustomField('<?= esc($sectionId) ?>', <?= (int)$firstPage['id'] ?>)" style="padding:9px 14px;">Add</button>
                                <button class="ff-btn ff-btn-ghost" onclick="ffCloseAddRow('<?= esc($sectionId) ?>-add')" style="padding:9px 14px;">✕</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($pGrouped)): ?>
                <div class="ff-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    <p>No fields on this page yet.<br>Use "Add Field" in a section, or add sections via the field config.</p>
                </div>
                <?php endif; ?>

                <?php endif; ?>

                <!-- SAVE BAR -->
                <div class="ff-save-bar">
                    <div class="ff-save-bar-msg"><strong id="ff-change-count">0 changes</strong> pending — don't forget to save!</div>
                    <div class="ff-save-actions">
                        <button class="ff-btn ff-btn-ghost" onclick="ffDiscardChanges()">Discard</button>
                        <button class="ff-btn ff-btn-primary" onclick="ffSaveAll()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

        </div><!-- end content-wrap -->
    </div><!-- end main-area -->
</div><!-- end shell -->

<!-- EDIT FIELD MODAL -->
<div class="ff-modal-bg" id="ff-edit-modal">
    <div class="ff-modal">
        <h3>Edit Field</h3>
        <p>Update label, placeholder, required status, or visibility.</p>
        <input type="hidden" id="modal-field-key">
        <div class="ff-modal-fields">
            <div class="ff-modal-field"><label>Field Label</label><input type="text" id="modal-label" placeholder="Display label"></div>
            <div class="ff-modal-field"><label>Placeholder / Helper Text</label><input type="text" id="modal-placeholder" placeholder="Optional hint"></div>
            <div style="display:flex;gap:24px;align-items:center;padding:4px 0;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:#444;">
                    <input type="checkbox" id="modal-required" class="ff-req-check"> Mark as Required
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:#444;">
                    <input type="checkbox" id="modal-visible" class="ff-req-check" checked> Visible on Form
                </label>
            </div>
        </div>
        <div class="ff-modal-actions">
            <button class="ff-btn ff-btn-ghost" onclick="ffCloseModal()">Cancel</button>
            <button class="ff-btn ff-btn-primary" onclick="ffApplyModal()">Apply Changes</button>
        </div>
    </div>
</div>

<!-- ADD / EDIT PAGE MODAL -->
<div class="page-modal-bg" id="page-modal">
    <div class="page-modal">
        <h3 id="page-modal-title">Add New Page</h3>
        <p>This creates a new step in the applicant registration form.</p>
        <input type="hidden" id="page-modal-id" value="">
        <div class="pm-field"><label>Page Title</label><input type="text" id="page-modal-name" placeholder="e.g. Additional Requirements"></div>
        <div class="pm-field"><label>Description <span style="font-weight:400;color:#aaa;">(optional)</span></label><textarea id="page-modal-desc" rows="2" placeholder="Brief description shown in the form header"></textarea></div>
        <div class="pm-actions">
            <button class="ff-btn ff-btn-ghost" onclick="ffClosePageModal()">Cancel</button>
            <button class="ff-btn ff-btn-primary" id="page-modal-submit" onclick="ffSubmitPageModal()">Create Page</button>
        </div>
    </div>
</div>

<div class="ff-toast" id="ff-toast"></div>

<script>
// ── STATE ────────────────────────────────────────────────────
let ffChanges = 0;
let editRowEl = null;
let currentPageId = <?= (int)(($pages[0]['id'] ?? 1)) ?>;
const ffCsrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── PAGE SELECTION ───────────────────────────────────────────
function ffSelectPage(pageId, el) {
    document.querySelectorAll('.page-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    currentPageId = pageId;

    fetch('<?= base_url('admin/form-fields/page-data') ?>?page_id=' + pageId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('fields-panel').innerHTML = data.html;
        }
    })
    .catch(() => ffToast('Failed to load page fields.', 'error'));
}

// ── PAGE: TOGGLE ENABLED ─────────────────────────────────────
function ffTogglePage(id, btn) {
    fetch('<?= base_url('admin/form-fields/pages') ?>/' + id + '/toggle', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': ffCsrf }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            const item = btn.closest('.page-item');
            const isEnabled = data.is_enabled;
            item.classList.toggle('disabled-page', !isEnabled);
            // swap icon
            btn.innerHTML = isEnabled
                ? `<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
                : `<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
            // Update badge in title
            const badgeOff = item.querySelector('.badge-off');
            if (!isEnabled) {
                if (!badgeOff) {
                    const titleEl = item.querySelector('.page-item-title');
                    titleEl.insertAdjacentHTML('beforeend', '<span class="badge-off">OFF</span>');
                }
            } else {
                if (badgeOff) badgeOff.remove();
            }
            ffToast(isEnabled ? 'Page enabled.' : 'Page disabled. Applicants will skip it.', 'success');
        }
    });
}

// ── PAGE: DELETE ─────────────────────────────────────────────
function ffDeletePage(id) {
    if (!confirm('Delete this page and all its custom fields? System fields will be unaffected.')) return;
    fetch('<?= base_url('admin/form-fields/pages') ?>/' + id + '/delete', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': ffCsrf }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            const item = document.querySelector(`.page-item[data-page-id="${id}"]`);
            if (item) item.remove();
            ffToast('Page deleted.', 'success');
        } else {
            ffToast(data.message || 'Could not delete page.', 'error');
        }
    });
}

// ── PAGE MODAL ───────────────────────────────────────────────
function ffAddPageModal() {
    document.getElementById('page-modal-id').value    = '';
    document.getElementById('page-modal-name').value  = '';
    document.getElementById('page-modal-desc').value  = '';
    document.getElementById('page-modal-title').textContent = 'Add New Page';
    document.getElementById('page-modal-submit').textContent = 'Create Page';
    document.getElementById('page-modal').classList.add('open');
}

function ffEditPageModal(id, title, desc) {
    document.getElementById('page-modal-id').value    = id;
    document.getElementById('page-modal-name').value  = title;
    document.getElementById('page-modal-desc').value  = desc;
    document.getElementById('page-modal-title').textContent = 'Rename Page';
    document.getElementById('page-modal-submit').textContent = 'Save Changes';
    document.getElementById('page-modal').classList.add('open');
}

function ffClosePageModal() {
    document.getElementById('page-modal').classList.remove('open');
}

function ffSubmitPageModal() {
    const id    = document.getElementById('page-modal-id').value;
    const title = document.getElementById('page-modal-name').value.trim();
    const desc  = document.getElementById('page-modal-desc').value.trim();
    if (!title) { ffToast('Please enter a page title.', 'error'); return; }

    const isNew = !id;
    const url   = isNew
        ? '<?= base_url('admin/form-fields/pages') ?>'
        : `<?= base_url('admin/form-fields/pages') ?>/${id}/rename`;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': ffCsrf },
        body: JSON.stringify({ title, description: desc })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            ffClosePageModal();
            if (isNew && data.page) {
                // Inject new page item into sidebar
                const pg = data.page;
                const list = document.getElementById('pages-sortable');
                const div = document.createElement('div');
                div.className = 'page-item';
                div.dataset.pageId = pg.id;
                div.onclick = function() { ffSelectPage(pg.id, this); };
                div.innerHTML = `
                    <div class="page-drag-handle"><svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor"><circle cx="3" cy="2" r="1.3"/><circle cx="7" cy="2" r="1.3"/><circle cx="3" cy="7" r="1.3"/><circle cx="7" cy="7" r="1.3"/><circle cx="3" cy="12" r="1.3"/><circle cx="7" cy="12" r="1.3"/></svg></div>
                    <div class="page-item-info">
                        <div class="page-item-title">${escHtml(pg.title)}</div>
                        <div class="page-item-count">0 fields</div>
                    </div>
                    <div class="page-item-actions">
                        <button class="pi-btn" onclick="ffEditPageModal(${pg.id}, ${JSON.stringify(pg.title)}, ${JSON.stringify(pg.description||'')}); event.stopPropagation();" title="Rename"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                        <button class="pi-btn" onclick="ffTogglePage(${pg.id}, this); event.stopPropagation();"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        <button class="pi-btn danger" onclick="ffDeletePage(${pg.id}); event.stopPropagation();"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
                    </div>`;
                list.appendChild(div);
                ffToast(`Page "${pg.title}" created!`, 'success');
            } else {
                // Update title in sidebar
                const item = document.querySelector(`.page-item[data-page-id="${id}"]`);
                if (item) {
                    const titleEl = item.querySelector('.page-item-title');
                    titleEl.childNodes[0].textContent = title + ' ';
                }
                ffToast('Page renamed.', 'success');
            }
        } else {
            ffToast(data.message || 'Something went wrong.', 'error');
        }
    });
}

document.getElementById('page-modal').addEventListener('click', function(e) { if (e.target === this) ffClosePageModal(); });

// ── Drag and Drop FUNCTIONALITY ───────────────────────────────────────
(function () {
    let dragEl   = null;
    let ghostEl  = null;
    let offsetY  = 0;
    const list   = document.getElementById('pages-sortable');

    list.addEventListener('mousedown', function (e) {
        const handle = e.target.closest('.page-drag-handle');
        if (!handle) return;
        e.preventDefault();

        dragEl = handle.closest('.page-item');
        const rect = dragEl.getBoundingClientRect();
        offsetY = e.clientY - rect.top;

        // Create a ghost clone that follows the cursor
        ghostEl = dragEl.cloneNode(true);
        ghostEl.style.cssText = `
            position: fixed;
            width: ${rect.width}px;
            left: ${rect.left}px;
            top: ${rect.top}px;
            opacity: 0.85;
            pointer-events: none;
            z-index: 9999;
            background: #fff;
            border: 1px solid var(--primary, #0040a6);
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        `;
        document.body.appendChild(ghostEl);

        // Dim the original
        dragEl.style.opacity = '0.3';
    });

    document.addEventListener('mousemove', function (e) {
        if (!dragEl) return;
        e.preventDefault();

        // Move ghost
        ghostEl.style.top = (e.clientY - offsetY) + 'px';

        // Find insertion point
        const items = [...list.querySelectorAll('.page-item')].filter(i => i !== dragEl);
        let inserted = false;

        for (const item of items) {
            const rect = item.getBoundingClientRect();
            const mid  = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                list.insertBefore(dragEl, item);
                inserted = true;
                break;
            }
        }
        if (!inserted) list.appendChild(dragEl);
    });

    document.addEventListener('mouseup', function () {
        if (!dragEl) return;

        // Restore
        dragEl.style.opacity = '';
        ghostEl.remove();
        dragEl  = null;
        ghostEl = null;

        // Save new order
        const order = [...list.querySelectorAll('.page-item')].map((item, idx) => ({
            id:         parseInt(item.dataset.pageId),
            sort_order: (idx + 1) * 10
        }));

        fetch('<?= base_url('admin/form-fields/pages/reorder') ?>', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': ffCsrf },
            body:    JSON.stringify({ order })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') ffToast('Page order saved.', 'success');
            else ffToast('Failed to save order.', 'error');
        })
        .catch(() => ffToast('Network error saving order.', 'error'));
    });
})();
// ── FIELD TOGGLES / EDITS ────────────────────────────────────
function ffToggleVisible(checkbox) {
    const row = checkbox.closest('.ff-field-row');
    const lbl = checkbox.closest('.ff-toggle-wrap').querySelector('.ff-toggle-label');
    row.classList.toggle('disabled-row', !checkbox.checked);
    lbl.textContent = checkbox.checked ? 'On' : 'Off';
    ffMarkChange();
}

function ffMarkChange() {
    ffChanges++;
    document.getElementById('ff-change-count').textContent = ffChanges + (ffChanges === 1 ? ' change' : ' changes');
}

function ffOpenAddRow(id) {
    document.querySelectorAll('.ff-add-row').forEach(r => r.classList.remove('open'));
    document.getElementById(id).classList.add('open');
}

function ffCloseAddRow(id) { document.getElementById(id).classList.remove('open'); }

function ffAutoKey(label) { return label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, ''); }

function ffAddCustomField(sectionId, pageId) {
    const label       = document.getElementById('add-label-' + sectionId).value.trim();
    const keyInput    = document.getElementById('add-key-' + sectionId).value.trim();
    const type        = document.getElementById('add-type-' + sectionId).value;
    const placeholder = document.getElementById('add-placeholder-' + sectionId).value.trim();
    if (!label) { ffToast('Please enter a field label.', 'error'); return; }
    const key = keyInput || ffAutoKey(label);
    const list = document.getElementById(sectionId);
    const row = document.createElement('div');
    row.className = 'ff-field-row';
    row.dataset.key     = key;
    row.dataset.page    = pageId;
    row.dataset.section = sectionId;
    row.dataset.system  = '0';
    row.innerHTML = `
        <div class="ff-drag-handle"><svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><circle cx="4" cy="3" r="1.5"/><circle cx="8" cy="3" r="1.5"/><circle cx="4" cy="8" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="4" cy="13" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg></div>
        <div class="ff-field-name">
            <div class="ff-field-label">${escHtml(label)} <span class="badge-custom">CUSTOM</span></div>
            <div class="ff-field-key">${escHtml(key)}</div>
        </div>
        <div class="ff-field-type">${type.toUpperCase()}</div>
        <div class="ff-field-placeholder">${escHtml(placeholder || '—')}</div>
        <div class="ff-toggle-wrap">
            <label class="ff-toggle"><input type="checkbox" checked onchange="ffToggleVisible(this)"><div class="ff-toggle-track"></div></label>
            <span class="ff-toggle-label">On</span>
        </div>
        <div class="ff-row-actions">
            <button class="ff-icon-btn" onclick="ffEditField(this)" title="Edit"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            <button class="ff-icon-btn delete" onclick="ffDeleteField(this)" title="Delete"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
        </div>`;
    list.appendChild(row);
    ['add-label-','add-key-','add-placeholder-'].forEach(p => { const el = document.getElementById(p + sectionId); if (el) el.value = ''; });
    ffCloseAddRow(sectionId + '-add');
    ffMarkChange();
    ffToast(`"${label}" added.`, 'success');
}

function ffDeleteField(btn) {
    const row = btn.closest('.ff-field-row');
    if (!confirm(`Delete field "${row.dataset.key}"?`)) return;
    row.remove();
    ffMarkChange();
    ffToast('Field removed.', 'success');
}

function ffEditField(btn) {
    const row = btn.closest('.ff-field-row');
    editRowEl = row;
    document.getElementById('modal-field-key').value   = row.dataset.key;
    document.getElementById('modal-label').value       = row.querySelector('.ff-field-label').childNodes[0].textContent.trim();
    document.getElementById('modal-placeholder').value = row.querySelector('.ff-field-placeholder').textContent.replace('—','').trim();
    document.getElementById('modal-visible').checked   = row.querySelector('input[type=checkbox]').checked;
    document.getElementById('ff-edit-modal').classList.add('open');
}

function ffCloseModal() { document.getElementById('ff-edit-modal').classList.remove('open'); editRowEl = null; }

function ffApplyModal() {
    if (!editRowEl) return;
    const newLabel       = document.getElementById('modal-label').value.trim();
    const newPlaceholder = document.getElementById('modal-placeholder').value.trim();
    const newVisible     = document.getElementById('modal-visible').checked;
    if (!newLabel) { ffToast('Label cannot be empty.', 'error'); return; }
    const labelEl = editRowEl.querySelector('.ff-field-label');
    labelEl.childNodes[0].textContent = newLabel + ' ';
    editRowEl.querySelector('.ff-field-placeholder').textContent = newPlaceholder || '—';
    const toggleInput = editRowEl.querySelector('input[type=checkbox]');
    toggleInput.checked = newVisible;
    editRowEl.classList.toggle('disabled-row', !newVisible);
    editRowEl.querySelector('.ff-toggle-label').textContent = newVisible ? 'On' : 'Off';
    ffCloseModal();
    ffMarkChange();
    ffToast('Field updated.', 'success');
}

document.getElementById('ff-edit-modal').addEventListener('click', function(e) { if (e.target === this) ffCloseModal(); });

// ── SAVE ALL ─────────────────────────────────────────────────
function ffSaveAll() {
    const payload = [];
    document.querySelectorAll('.ff-field-row').forEach(row => {
        payload.push({
            key:         row.dataset.key,
            page_id:     parseInt(row.dataset.page),
            section:     row.dataset.section,
            system:      row.dataset.system === '1',
            visible:     row.querySelector('input[type=checkbox]').checked,
            label:       row.querySelector('.ff-field-label').childNodes[0].textContent.trim(),
            placeholder: row.querySelector('.ff-field-placeholder').textContent.replace('—','').trim(),
            type:        row.querySelector('.ff-field-type').textContent.toLowerCase(),
        });
    });
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    fetch('<?= base_url('admin/form-fields/save') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ fields: payload })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            ffChanges = 0;
            document.getElementById('ff-change-count').textContent = '0 changes';
            ffToast('All changes saved!', 'success');
        } else {
            ffToast('Save failed: ' + (data.message ?? 'Unknown error'), 'error');
        }
    })
    .catch(() => ffToast('Network error.', 'error'));
}

function ffDiscardChanges() {
    if (ffChanges === 0) return;
    if (confirm('Discard all unsaved changes?')) window.location.reload();
}

function ffResetAll() {
    if (!confirm('Reset ALL fields to defaults and remove custom fields?')) return;
    fetch('<?= base_url('admin/form-fields/reset') ?>', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': ffCsrf } })
    .then(r => r.json())
    .then(data => { if (data.status === 'success') { ffToast('Reset to defaults.', 'success'); setTimeout(() => location.reload(), 1000); } });
}

// ── TOAST ─────────────────────────────────────────────────────
function ffToast(msg, type = 'success') {
    const el = document.getElementById('ff-toast');
    el.textContent = msg;
    el.className = 'ff-toast ' + type + ' show';
    setTimeout(() => el.classList.remove('show'), 2800);
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── ADD SECTION ───────────────────────────────────────────────
function ffAddSection(pageId) {
    const name = prompt('Section name (e.g. "General Info"):');
    if (!name || !name.trim()) return;

    const sectionId = 'page' + pageId + '-' + name.trim().toLowerCase().replace(/[^a-z0-9]+/g, '_');
    const panel     = document.getElementById('fields-panel');

    const sectionHtml = `
    <div class="ff-section-card">
        <div class="ff-section-head">
            <h4>${escHtml(name.trim())} <span>Page ${pageId}</span></h4>
            <button class="ff-btn ff-btn-ghost" onclick="ffOpenAddRow('${sectionId}-add')" style="padding:6px 14px;font-size:12px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Field
            </button>
        </div>
        <div class="ff-col-headers">
            <span></span><span>Field</span><span>Type</span><span>Placeholder</span><span>Visible</span><span>Actions</span>
        </div>
        <div class="ff-fields-list" id="${sectionId}"></div>
        <div class="ff-add-row open" id="${sectionId}-add">
            <div class="ff-add-row-title">+ New Custom Field</div>
            <div class="ff-add-grid">
                <div class="ff-add-field"><label>Label</label><input type="text" id="add-label-${sectionId}" placeholder="e.g. Religion"></div>
                <div class="ff-add-field"><label>Key</label><input type="text" id="add-key-${sectionId}" placeholder="auto-generated"></div>
                <div class="ff-add-field"><label>Type</label>
                    <select id="add-type-${sectionId}">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="tel">Phone</option>
                        <option value="date">Date</option>
                        <option value="select">Select</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </div>
                <div class="ff-add-field"><label>Placeholder</label><input type="text" id="add-placeholder-${sectionId}" placeholder="Optional"></div>
                <div style="display:flex;gap:6px;">
                    <button class="ff-btn ff-btn-primary" onclick="ffAddCustomField('${sectionId}', ${pageId})" style="padding:9px 14px;">Add</button>
                    <button class="ff-btn ff-btn-ghost" onclick="ffCloseAddRow('${sectionId}-add')" style="padding:9px 14px;">✕</button>
                </div>
            </div>
        </div>
    </div>`;

    // Remove empty state if present
    const emptyEl = panel.querySelector('#ff-empty-state');
    if (emptyEl) emptyEl.remove();

    // Insert before save bar
    const saveBar = panel.querySelector('.ff-save-bar');
    saveBar.insertAdjacentHTML('beforebegin', sectionHtml);

    ffToast(`Section "${name.trim()}" created. Add your fields below.`, 'success');
}
</script>
</body>
</html>