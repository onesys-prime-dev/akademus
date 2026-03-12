<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Theme Customizer</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800
&family=Plus+Jakarta+Sans:wght@400;500;600;700;800
&family=DM+Sans:wght@400;500;600;700
&family=Outfit:wght@400;500;600;700;800
&family=Nunito:wght@400;600;700;800
&family=Inter:wght@400;500;600;700;800
&family=Poppins:wght@400;500;600;700;800
&family=Raleway:wght@400;500;600;700;800
&family=Montserrat:wght@400;500;600;700;800
&family=Geist:wght@400;500;600;700;800
&family=Space+Grotesk:wght@400;500;600;700
&family=Lexend:wght@400;500;600;700
&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: 'Sora', sans-serif; overflow: hidden; }
        .shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── ICON SIDEBAR ── */
        .icon-sidebar {
            width: 64px; background: #0d1e38; display: flex;
            flex-direction: column; align-items: center;
            padding: 14px 0; gap: 4px; flex-shrink: 0; z-index: 10;
        }
        .isb-logo {
            width: 36px; height: 36px; background: #ffb200; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; color: #002d7a; margin-bottom: 14px;
            overflow: hidden;
        }
        .isb-logo img { width: 100%; height: 100%; object-fit: cover; }
        .isb-item {
            width: 40px; height: 40px; border-radius: 10px; display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            font-size: 18px; transition: all 0.18s; border: 1px solid transparent;
            position: relative; text-decoration: none;
        }
        .isb-item:hover { background: rgba(255,255,255,0.08); }
        .isb-item.on { background: rgba(0,64,166,0.5); border-color: rgba(0,64,166,0.6); }
        .isb-tip {
            position: absolute; left: 52px; top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #fff; font-size: 10px; font-weight: 600;
            padding: 4px 9px; border-radius: 6px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity 0.18s; z-index: 100;
        }
        .isb-item:hover .isb-tip { opacity: 1; }
        .isb-spacer { flex: 1; }

        /* ── EDITOR PANEL ── */
        .editor {
            width: 340px; background: #fff; border-right: 1px solid #e2e8f0;
            display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0;
        }
        .editor-head { padding: 18px 20px 0; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
        .editor-title { font-size: 16px; font-weight: 800; color: #0d1e38; letter-spacing: -0.3px; margin-bottom: 2px; }
        .editor-sub   { font-size: 11px; color: #5a6a85; margin-bottom: 14px; }
        .tabs { display: flex; margin: 0 -20px; }
        .tab {
            flex: 1; padding: 9px 4px; border: none; background: transparent;
            font-family: 'Sora', sans-serif; font-size: 10px; font-weight: 600;
            color: #5a6a85; cursor: pointer; border-bottom: 2px solid transparent;
            transition: all 0.18s; display: flex; flex-direction: column; align-items: center; gap: 2px;
        }
        .tab.on { color: #0040a6; border-bottom-color: #0040a6; }
        .tab:hover:not(.on) { color: #0d1e38; }
        .tab-icon { font-size: 14px; }
        .editor-body { flex: 1; overflow-y: auto; padding: 18px 20px; }
        .tab-pane { display: none; }
        .tab-pane.on { display: block; }

        /* ── FORM ELEMENTS ── */
        .form-group { margin-bottom: 14px; }
        .form-label {
            display: block; font-size: 10px; font-weight: 700; color: #5a6a85;
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 5px;
        }
        .form-input {
            width: 100%; padding: 9px 12px; border: 1.5px solid #dde3f0;
            border-radius: 8px; font-family: 'Sora', sans-serif;
            font-size: 12px; color: #0d1e38; outline: none;
            transition: all 0.18s; background: #fff;
        }
        .form-input:focus { border-color: #0040a6; box-shadow: 0 0 0 3px rgba(0,64,166,0.08); }

        .sec-head { margin-bottom: 14px; }
        .sec-head-row { display: flex; align-items: center; gap: 8px; margin-bottom: 3px; }
        .sec-icon { font-size: 15px; }
        .sec-title { font-size: 13px; font-weight: 700; color: #0d1e38; letter-spacing: -0.2px; }
        .sec-desc { font-size: 11px; color: #5a6a85; padding-left: 23px; }
        .divider { height: 1px; background: #f0f4f8; margin: 18px 0; }

        /* ── PRESETS ── */
        .preset-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
        .preset-card {
            border: 1.5px solid #dde3f0; border-radius: 10px; padding: 10px 11px;
            cursor: pointer; transition: all 0.18s; background: #fff;
        }
        .preset-card:hover { border-color: #94a3b8; transform: translateY(-1px); }
        .preset-card.on { border-color: #0040a6; background: #e8eef8; }
        .preset-dots { display: flex; gap: 4px; margin-bottom: 6px; }
        .preset-dot { width: 14px; height: 14px; border-radius: 4px; }
        .preset-name { font-size: 11px; font-weight: 700; color: #0d1e38; }
        .preset-desc { font-size: 9px; color: #5a6a85; }

        /* ── COLOR SWATCHES ── */
        .color-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .swatch-label { font-size: 10px; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 5px; }
        .swatch-row {
            display: flex; align-items: center; gap: 8px; padding: 7px 10px;
            border: 1.5px solid #dde3f0; border-radius: 8px; cursor: pointer;
            background: #fff; transition: border-color 0.18s;
        }
        .swatch-dot { width: 20px; height: 20px; border-radius: 5px; border: 1px solid rgba(0,0,0,0.1); flex-shrink: 0; }
        .swatch-hex { font-size: 12px; font-weight: 600; color: #0d1e38; font-family: monospace; }
        .swatch-edit { margin-left: auto; font-size: 10px; color: #94a3b8; }

        /* ── FONT OPTIONS ── */
        .font-option {
            display: flex; align-items: center; gap: 10px; padding: 9px 12px;
            border: 1.5px solid #dde3f0; border-radius: 8px; cursor: pointer;
            transition: all 0.18s; background: #fff; margin-bottom: 6px;
            max-height: 320px;
    overflow-y: auto;
    padding-right: 4px;
        }
        .font-option:hover { border-color: #94a3b8; }
        .font-option.on { border-color: #0040a6; background: #e8eef8; }
        .font-radio { width: 14px; height: 14px; border-radius: 50%; border: 2px solid #dde3f0; flex-shrink: 0; transition: all 0.18s; }
        .font-option.on .font-radio { background: #0040a6; border-color: #0040a6; }
        .font-name { font-size: 12px; font-weight: 600; color: #0d1e38; }
        .font-sample { font-size: 11px; color: #5a6a85; }

        /* ── RADIUS ── */
        .radius-row { display: flex; gap: 6px; }
        .radius-btn {
            flex: 1; padding: 8px 4px; border: 1.5px solid #dde3f0; background: #fff;
            border-radius: 6px; cursor: pointer; font-family: 'Sora', sans-serif;
            font-size: 10px; font-weight: 600; color: #5a6a85; text-align: center; transition: all 0.18s;
        }
        .radius-btn:hover { border-color: #94a3b8; }
        .radius-btn.on { border-color: #0040a6; background: #e8eef8; color: #0040a6; }
        .radius-preview { width: 20px; height: 20px; background: #0040a6; margin: 0 auto 4px; }

        /* ── LOGO UPLOAD ── */
        .upload-zone {
            border: 2px dashed #dde3f0; border-radius: 10px; padding: 16px;
            text-align: center; cursor: pointer; transition: all 0.18s; background: #f8fafc;
        }
        .upload-zone:hover { border-color: #0040a6; background: #e8eef8; }

        /* ── COLOR PREVIEW BOX ── */
        .color-preview-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; padding: 11px 13px; }
        .color-preview-label { font-size: 10px; font-weight: 700; color: #5a6a85; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.6px; }
        .color-preview-swatches { display: flex; gap: 6px; flex-wrap: wrap; }
        .cp-swatch { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .cp-dot { width: 32px; height: 32px; border-radius: 7px; border: 1px solid rgba(0,0,0,0.08); }
        .cp-label { font-size: 8px; color: #94a3b8; font-weight: 600; }

        /* ── PREVIEW PANEL ── */
        .preview-panel { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .preview-topbar {
            height: 52px; background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; flex-shrink: 0; gap: 16px;
        }
        .preview-topbar-left { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .preview-topbar-title { font-size: 13px; font-weight: 700; color: #0d1e38; display: flex; align-items: center; gap: 8px; }
        .live-badge {
    padding: 3px 8px;
    background: #f0fdf4;
    color: #15803d;
    font-size: 9px; font-weight: 700;
    border-radius: 4px; /* square-ish instead of pill */
    letter-spacing: 0.5px;
    animation: pulse 2s infinite;
}
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.6; } }

        /* ── PAGE SELECTOR DROPDOWN ── */
        .page-selector-wrap { display: flex; align-items: center; gap: 8px; }
        .page-selector-label { font-size: 10px; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .page-selector {
            appearance: none; -webkit-appearance: none;
            padding: 6px 28px 6px 10px; border: 1.5px solid #dde3f0;
            border-radius: 8px; font-family: 'Sora', sans-serif;
            font-size: 11px; font-weight: 600; color: #0d1e38;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%235a6a85' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat right 10px center;
            cursor: pointer; outline: none; transition: border-color 0.18s;
            min-width: 200px;
        }
        .page-selector:focus { border-color: #0040a6; box-shadow: 0 0 0 3px rgba(0,64,166,0.08); }

        /* page transition */
        #portal-preview { width: 100%; height: 100%; display: flex; transition: opacity 0.2s ease; }
        #portal-preview.fading { opacity: 0; }

        .preview-body { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; background: #f0f4f8; overflow: auto; }
        .preview-frame {
            width: 100%; max-width: 760px; aspect-ratio: 16/10;
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(.4,0,.2,1);
        }
        .preview-footer {
            padding: 12px 24px; background: #fff; border-top: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
        }
        .pf-info { font-size: 11px; color: #5a6a85; }
        .pf-info strong { color: #0d1e38; }
        .pf-sep { margin: 0 6px; color: #dde3f0; }
        .btn-reset {
            padding: 8px 16px; border: 1.5px solid #dde3f0; background: transparent;
            border-radius: 8px; font-family: 'Sora', sans-serif; font-size: 11px;
            font-weight: 600; color: #5a6a85; cursor: pointer; transition: all 0.18s;
        }
        .btn-reset:hover { border-color: #0040a6; color: #0040a6; }
        .btn-save {
            padding: 8px 20px; border: none; border-radius: 8px;
            font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: all 0.3s; background: #0040a6; color: #fff;
        }
        .btn-save:hover { opacity: 0.9; }
        .btn-save:active { transform: scale(0.97); }

        /* ── TOAST ── */
        .toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: #0a7a52; color: #fff; padding: 10px 18px;
            border-radius: 10px; font-size: 12px; font-weight: 600;
            display: none; align-items: center; gap: 8px;
            box-shadow: 0 8px 24px rgba(10,122,82,0.3);
        }
        .toast.show { display: flex; animation: toastIn 0.3s cubic-bezier(.4,0,.2,1); }
        @keyframes toastIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

        .tag-new {
    font-size: 8px; font-weight: 700;
    background: #eff6ff; color: #1d4ed8;
    padding: 2px 6px; border-radius: 4px;
    vertical-align: middle; margin-left: 4px;
    letter-spacing: 0.4px; text-transform: uppercase;
}
    </style>
</head>
<body>

<?php
$csrfToken = csrf_token();
$csrfHash  = csrf_hash();
?>

<div class="toast" id="toast">Theme saved & applied to portal</div>

<div class="shell">

    <!-- ── ICON SIDEBAR ── -->
    <div class="icon-sidebar">
        <div class="isb-logo">
            <?php if (!empty($theme['logo_url'])): ?>
                <img src="<?= !empty($theme['logo_url']) ? esc($theme['logo_url']) : base_url('assets/images/logo_akademus.png') ?>" />
            <?php else: ?>
                <?= esc($theme['logo_text'] ?? 'AK') ?>
            <?php endif; ?>
        </div>
        <div class="isb-item on">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 0 20"/><path d="M2 12h20"/></svg>
    <div class="isb-tip">Theme Editor</div>
</div>
<a href="<?= base_url('admin/form-fields') ?>" class="isb-item <?= (current_url() === base_url('admin/form-fields')) ? 'on' : '' ?>">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    <div class="isb-tip">Form Fields</div>
</a>
<div class="isb-item">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>
    <div class="isb-tip">Requirements</div>
</div>
<div class="isb-item">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    <div class="isb-tip">Analytics</div>
</div>
<div class="isb-spacer"></div>
<a href="<?= base_url('admin/logout') ?>" class="isb-item">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    <div class="isb-tip">Logout</div>
</a>
            </div>

    <!-- ── EDITOR PANEL ── -->
    <div class="editor">
        <div class="editor-head">
            <div class="editor-title">Theme Customizer</div>
            <div class="editor-sub">Customize how the applicant portal looks</div>
            <div class="tabs">
                <button class="tab on" data-tab="branding">Branding</button>
<button class="tab" data-tab="colors">Colors</button>
<button class="tab" data-tab="typography">Typography</button>
<button class="tab" data-tab="layout">Layout</button>
            </div>
        </div>

        <div class="editor-body">

            <!-- BRANDING TAB -->
            <div class="tab-pane on" id="tab-branding">
                <div class="sec-head">
<div class="sec-head-row"><span class="sec-title">Logo & Identity</span></div>
                    <div class="sec-desc">Set your institution's name and logo mark</div>
                </div>
                <div class="form-group">
                    <label class="form-label">App / Portal Name</label>
                    <input class="form-input" id="app_name" type="text" value="<?= esc($theme['app_name'] ?? 'Akademus Portal') ?>" placeholder="Akademus Portal">
                </div>
                <div class="form-group">
                    <label class="form-label">Logo Mark Text</label>
                    <input class="form-input" id="logo_text" type="text" maxlength="3" value="<?= esc($theme['logo_text'] ?? 'AK') ?>" placeholder="AK">
                    <div style="font-size:10px;color:#94a3b8;margin-top:4px;">2–3 characters shown when no logo image is set</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Upload Logo Image <span class="tag-new">NEW</span></label>
                    <div class="upload-zone" id="upload-zone" onclick="document.getElementById('logo_file').click()">
                        <?php if (!empty($theme['logo_url'])): ?>
                            <img src="<?= esc($theme['logo_url']) ?>" style="width:48px;height:48px;object-fit:contain;border-radius:8px;margin-bottom:4px;" />
                            <div style="font-size:10px;color:#5a6a85;">Click to change</div>
                        <?php else: ?>
                            <div id="upload-placeholder">
                                <div style="font-size:24px;margin-bottom:6px;">📁</div>
                                <div style="font-size:12px;font-weight:600;color:#0d1e38;margin-bottom:2px;">Click to upload logo</div>
                                <div style="font-size:10px;color:#94a3b8;">PNG, JPG, SVG — recommended 64×64px</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="logo_file" accept="image/*" style="display:none">
                    <input type="hidden" id="logo_url" value="<?= esc($theme['logo_url'] ?? '') ?>">
                </div>
                <div class="divider"></div>
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon">🎭</span><span class="sec-title">Preset Themes</span></div>
                    <div class="sec-desc">Start from a preset and fine-tune from there</div>
                </div>
                <div class="preset-grid" id="preset-grid"></div>
            </div>

            <!-- COLORS TAB -->
            <div class="tab-pane" id="tab-colors">
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon">🎨</span><span class="sec-title">Brand Colors</span></div>
                    <div class="sec-desc">Fine-tune individual colors to match your institution</div>
                </div>
                <div class="color-grid">
                    <?php
                    $colorFields = [
                        'primary_color' => ['Primary',       '#0040a6'],
                        'primary_dark'  => ['Primary Dark',  '#002d7a'],
                        'primary_light' => ['Primary Light', '#1a55c0'],
                        'sidebar_color' => ['Sidebar',       '#002d7a'],
                        'accent_color'  => ['Accent',        '#ffb200'],
                        'accent_text'   => ['Accent Text',   '#002d7a'],
                    ];
                    foreach ($colorFields as $key => [$label, $default]):
                        $val = $theme[$key] ?? $default;
                    ?>
                    <div>
                        <div class="swatch-label"><?= $label ?></div>
                        <div class="swatch-row" onclick="document.getElementById('inp_<?= $key ?>').click()">
                            <div class="swatch-dot" id="dot_<?= $key ?>" style="background:<?= $val ?>"></div>
                            <span class="swatch-hex" id="hex_<?= $key ?>"><?= $val ?></span>
                            <span class="swatch-edit">edit</span>
                        </div>
                        <input type="color" id="inp_<?= $key ?>" value="<?= $val ?>" style="opacity:0;position:absolute;width:0;height:0">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="divider"></div>
                <div class="color-preview-box">
                    <div class="color-preview-label">Live Color Preview</div>
                    <div class="color-preview-swatches" id="color-preview-swatches"></div>
                </div>
            </div>

            <!-- TYPOGRAPHY TAB -->
            <div class="tab-pane" id="tab-typography">
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon">✏️</span><span class="sec-title">Font Family</span></div>
                    <div class="sec-desc">Choose the typeface used across the portal</div>
                </div>
                <div id="font-options"></div>
                <div class="divider"></div>
                <div class="color-preview-box">
                    <div class="color-preview-label">Font Preview</div>
                    <div id="font-preview" style="margin-top:8px;">
                        <div id="fp-heading" style="font-size:20px;font-weight:800;color:#0d1e38;margin-bottom:4px;letter-spacing:-0.5px;">Welcome back, Juan.</div>
                        <div style="font-size:12px;color:#5a6a85;line-height:1.6;margin-bottom:10px;">Your application is in progress. Upload the remaining documents to proceed.</div>
                        <div style="display:flex;gap:6px;">
                            <div id="fp-btn1" style="padding:6px 14px;color:#fff;border-radius:7px;font-size:11px;font-weight:700;">Continue</div>
                            <div id="fp-btn2" style="padding:6px 14px;border-radius:7px;font-size:11px;font-weight:700;">Pay Fee</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LAYOUT TAB -->
            <div class="tab-pane" id="tab-layout">
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon">⚡</span><span class="sec-title">Border Radius</span></div>
                    <div class="sec-desc">Controls the roundness of cards, buttons, and inputs</div>
                </div>
                <div class="radius-row" id="radius-row"></div>
                <div class="divider"></div>
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon">🔔</span><span class="sec-title">Portal Info</span></div>
                </div>
                <div class="form-group">
                    <label class="form-label">School Year</label>
                    <select class="form-input" id="school_year">
                        <?php foreach (['SY 2024-2025','SY 2025-2026','SY 2026-2027'] as $sy): ?>
                            <option <?= ($theme['school_year'] ?? 'SY 2025-2026') === $sy ? 'selected' : '' ?>><?= $sy ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Admission Status Banner</label>
                    <select class="form-input" id="admission_status">
                        <?php foreach (['Open for Applications','Applications Closing Soon','Closed — Next batch: June'] as $s): ?>
                            <option <?= ($theme['admission_status'] ?? 'Open for Applications') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- ── PREVIEW PANEL ── -->
    <div class="preview-panel">
        <div class="preview-topbar">
            <div class="preview-topbar-left">
                <div class="preview-topbar-title">Live Preview <span class="live-badge">LIVE</span></div>
            </div>
            <div class="page-selector-wrap">
                <span class="page-selector-label">Previewing:</span>
                <select class="page-selector" id="page-selector" onchange="switchPage(this.value)">
                    <option value="dashboard">🏠 Applicant Dashboard</option>
                    <option value="signin">🔐 Sign In Page</option>
                    <option value="signup">📝 Sign Up Page</option>
                    <option value="appform">📋 Application Form</option>
                    <option value="documents">📁 Document Upload</option>
                    <option value="track">📊 Track Progress</option>
                    <option value="success">✅ Registration Success</option>
                </select>
            </div>
            <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">Applicant Portal View</span>
        </div>
        <div class="preview-body">
            <div class="preview-frame">
                <div id="portal-preview"></div>
            </div>
        </div>
        <div class="preview-footer">
            <div class="pf-info">
                <strong id="pf-preset-name"><?= esc(ucwords(str_replace('-',' ', $theme['preset'] ?? 'SPUM Blue'))) ?></strong>
                <span class="pf-sep">·</span>
                <span id="pf-app-name"><?= esc($theme['app_name'] ?? 'Akademus Portal') ?></span>
                <span class="pf-sep">·</span>
                Font: <span id="pf-font"><?= esc($theme['font_family'] ?? 'Sora') ?></span>
                <span class="pf-sep">·</span>
                Page: <span id="pf-page">Applicant Dashboard</span>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn-reset" onclick="resetToDefault()">Reset to Default</button>
                <button class="btn-save" id="btn-save" onclick="saveTheme()">Save & Apply</button>
            </div>
        </div>
    </div>

</div>

<script>
const CSRF_NAME  = '<?= $csrfToken ?>';
const CSRF_HASH  = '<?= $csrfHash ?>';
const SAVE_URL   = '<?= base_url('admin/theme/save') ?>';

let currentPage = 'dashboard';

const state = {
    primary_color:    '<?= $theme['primary_color']    ?? '#0040a6' ?>',
    primary_dark:     '<?= $theme['primary_dark']     ?? '#002d7a' ?>',
    primary_light:    '<?= $theme['primary_light']    ?? '#1a55c0' ?>',
    accent_color:     '<?= $theme['accent_color']     ?? '#ffb200' ?>',
    accent_text:      '<?= $theme['accent_text']      ?? '#002d7a' ?>',
    sidebar_color:    '<?= $theme['sidebar_color']    ?? '#002d7a' ?>',
    font_family:      '<?= $theme['font_family']      ?? 'Sora' ?>',
    border_radius:    '<?= $theme['border_radius']    ?? '8px' ?>',
    app_name:         '<?= esc($theme['app_name']     ?? 'Akademus Portal') ?>',
    logo_text:        '<?= esc($theme['logo_text']    ?? 'AK') ?>',
    logo_url:         '<?= esc($theme['logo_url']     ?? '') ?>',
    school_year:      '<?= esc($theme['school_year']  ?? 'SY 2025-2026') ?>',
    admission_status: '<?= esc($theme['admission_status'] ?? 'Open for Applications') ?>',
    preset:           '<?= esc($theme['preset']       ?? 'spum-blue') ?>',
};

const PRESETS = [
    { id:'spum-blue', name:'SPUM Blue',     desc:'Default institutional blue', primary_color:'#0040a6', primary_dark:'#002d7a', primary_light:'#1a55c0', accent_color:'#ffb200', accent_text:'#002d7a', sidebar_color:'#002d7a' },
    { id:'forest',   name:'Forest Green',  desc:'Fresh and professional',     primary_color:'#0a6640', primary_dark:'#064d30', primary_light:'#0e7a4c', accent_color:'#f5c518', accent_text:'#064d30', sidebar_color:'#064d30' },
    { id:'crimson',  name:'Crimson',       desc:'Bold and distinguished',     primary_color:'#9b1c1c', primary_dark:'#7a1515', primary_light:'#b52323', accent_color:'#fbbf24', accent_text:'#7a1515', sidebar_color:'#7a1515' },
    { id:'violet',   name:'Royal Violet',  desc:'Modern and refined',         primary_color:'#5b21b6', primary_dark:'#4c1d95', primary_light:'#6d28d9', accent_color:'#f59e0b', accent_text:'#4c1d95', sidebar_color:'#3b0764' },
    { id:'slate',    name:'Slate Dark',    desc:'Sleek and corporate',        primary_color:'#1e293b', primary_dark:'#0f172a', primary_light:'#334155', accent_color:'#38bdf8', accent_text:'#0f172a', sidebar_color:'#0f172a' },
    { id:'rose',     name:'Rose Gold',     desc:'Warm and approachable',      primary_color:'#9f1239', primary_dark:'#881337', primary_light:'#be123c', accent_color:'#fda4af', accent_text:'#881337', sidebar_color:'#4c0519' },
];

const FONTS = [
    'Sora',
    'Plus Jakarta Sans',
    'DM Sans',
    'Outfit',
    'Nunito',
    'Inter',
    'Poppins',
    'Raleway',
    'Montserrat',
    'Space Grotesk',
    'Lexend',
];
const RADII = [{ label:'Sharp', value:'4px' },{ label:'Default', value:'8px' },{ label:'Rounded', value:'14px' },{ label:'Pill', value:'22px' }];

const PAGE_NAMES = {
    dashboard: 'Applicant Dashboard',
    signin:    'Sign In Page',
    signup:    'Sign Up Page',
    appform:   'Application Form',
    documents: 'Document Upload',
    track:     'Track Progress',
    success:   'Registration Success',
};

const lighten = (hex, a=0.88) => {
    const r = Math.round(parseInt(hex.slice(1,3),16) + (255-parseInt(hex.slice(1,3),16))*a);
    const g = Math.round(parseInt(hex.slice(3,5),16) + (255-parseInt(hex.slice(3,5),16))*a);
    const b = Math.round(parseInt(hex.slice(5,7),16) + (255-parseInt(hex.slice(5,7),16))*a);
    return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
};

// ── PAGE SWITCHER ─────────────────────────────────────────
function switchPage(page) {
    const el = document.getElementById('portal-preview');
    el.classList.add('fading');
    setTimeout(() => {
        currentPage = page;
        document.getElementById('pf-page').textContent = PAGE_NAMES[page];
        updatePreview();
        el.classList.remove('fading');
    }, 180);
}

// ── INIT ──────────────────────────────────────────────────
function init() {
    buildPresets(); buildFonts(); buildRadii(); buildColorPreview();
    updatePreview(); updateFontPreview();

    document.querySelectorAll('.tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(b => b.classList.remove('on'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('on'));
            btn.classList.add('on');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('on');
        });
    });

    ['primary_color','primary_dark','primary_light','accent_color','accent_text','sidebar_color'].forEach(key => {
        document.getElementById('inp_' + key).addEventListener('input', e => {
            state[key] = e.target.value;
            state.preset = null;
            document.getElementById('dot_' + key).style.background = e.target.value;
            document.getElementById('hex_' + key).textContent = e.target.value;
            buildColorPreview(); updatePreview(); updateFontPreview();
        });
    });

    document.getElementById('app_name').addEventListener('input', e => {
        state.app_name = e.target.value;
        document.getElementById('pf-app-name').textContent = e.target.value;
        updatePreview();
    });
    document.getElementById('logo_text').addEventListener('input', e => {
        state.logo_text = e.target.value.slice(0,3);
        updatePreview();
    });
    document.getElementById('school_year').addEventListener('change', e => {
        state.school_year = e.target.value; updatePreview();
    });
    document.getElementById('admission_status').addEventListener('change', e => {
        state.admission_status = e.target.value; updatePreview();
    });

    document.getElementById('logo_file').addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            state.logo_url = ev.target.result;
            document.getElementById('logo_url').value = ev.target.result;
            updatePreview();
            document.getElementById('upload-zone').innerHTML =
                `<img src="${ev.target.result}" style="width:48px;height:48px;object-fit:contain;border-radius:8px;margin-bottom:4px;"><div style="font-size:10px;color:#5a6a85;">Click to change</div>`;
        };
        reader.readAsDataURL(file);
    });
}

// ── SHARED LAYOUT HELPERS ─────────────────────────────────
function getSidebarHtml(p, pd, acc, at, f, rad) {
    const logoHtml = state.logo_url
        ? `<img src="${state.logo_url}" style="width:100%;height:100%;object-fit:cover;">`
        : (state.logo_text || 'AK');
    return `
    <div style="width:140px;background:${pd};display:flex;flex-direction:column;flex-shrink:0;">
        <div style="padding:14px 12px 10px;border-bottom:1px solid rgba(255,255,255,0.07);">
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;">
                <div style="width:26px;height:26px;border-radius:${parseInt(rad)/2}px;background:${acc};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:${at};flex-shrink:0;overflow:hidden;">${logoHtml}</div>
                <div>
                    <div style="font-size:9px;font-weight:800;color:#fff;letter-spacing:-0.2px;">${state.app_name || 'AKADEMUS'}</div>
                    <div style="font-size:7px;color:rgba(255,255,255,0.3);letter-spacing:1px;text-transform:uppercase;">Portal</div>
                </div>
            </div>
            <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:${parseInt(rad)/2}px;padding:6px 8px;">
                <div style="font-size:7px;font-weight:700;color:rgba(255,255,255,0.35);text-transform:uppercase;margin-bottom:2px;">Applicant ID</div>
                <div style="font-size:11px;font-weight:800;color:${acc};">#APP-2026-00001</div>
            </div>
        </div>
        <div style="flex:1;padding:8px 6px;display:flex;flex-direction:column;gap:2px;">
            ${[
                {l:'Dashboard',   page:'dashboard'},
                {l:'Applic. Form',page:'appform'},
                {l:'Documents',   page:'documents'},
                {l:'Track Status',page:'track'}
            ].map(n=>`
                <div onclick="switchPage('${n.page}');document.getElementById('page-selector').value='${n.page}';"
                     style="display:flex;align-items:center;gap:6px;padding:6px 8px;border-radius:${parseInt(rad)/1.5}px;background:${currentPage===n.page?p+'80':'transparent'};border:1px solid ${currentPage===n.page?p+'99':'transparent'};cursor:pointer;">
                    <div style="width:4px;height:4px;border-radius:50%;background:${currentPage===n.page?acc:'rgba(255,255,255,0.25)'};"></div>
                    <span style="font-size:9px;font-weight:${currentPage===n.page?600:400};color:${currentPage===n.page?'#fff':'rgba(255,255,255,0.5)'};">${n.l}</span>
                </div>`).join('')}
        </div>
        <div style="padding:8px 10px;border-top:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;gap:6px;">
            <div style="width:22px;height:22px;border-radius:50%;background:${acc};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:800;color:${at};">JD</div>
            <div>
                <div style="font-size:9px;font-weight:700;color:#fff;">Juan Dela Cruz</div>
                <div style="font-size:7px;color:rgba(255,255,255,0.35);text-transform:uppercase;">Applicant</div>
            </div>
        </div>
    </div>`;
}

function getTopbarHtml(title, p, acc, at, rad) {
    return `
    <div style="height:38px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;padding:0 14px;flex-shrink:0;">
        <span style="font-size:10px;font-weight:700;color:#0d1e38;">${title}</span>
        <div style="display:flex;gap:6px;align-items:center;">
            <div style="padding:3px 8px;border-radius:99px;background:#dcfce7;font-size:8px;font-weight:700;color:#166534;">Approved</div>
            <div style="padding:3px 9px;border-radius:${parseInt(rad)/1.5}px;background:${acc};font-size:8px;font-weight:700;color:${at};">Pay Fee</div>
        </div>
    </div>`;
}

// ── PAGE RENDERERS ────────────────────────────────────────
function renderDashboard(p, pd, acc, at, f, rad, lt) {
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;height:100%;width:100%;">
        ${getSidebarHtml(p,pd,acc,at,f,rad)}
        <div style="flex:1;display:flex;flex-direction:column;background:#f8fafc;min-width:0;">
            ${getTopbarHtml('Dashboard',p,acc,at,rad)}
            <div style="flex:1;padding:10px;overflow:hidden;">
                <div style="background:${p};border-radius:${rad};padding:12px 14px;margin-bottom:8px;position:relative;overflow:hidden;">
                    <div style="position:absolute;top:-30px;right:-30px;width:90px;height:90px;border-radius:50%;background:${acc};opacity:0.12;"></div>
                    <div style="font-size:8px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;margin-bottom:4px;">${state.school_year} · AKADEMUS</div>
                    <div style="font-size:13px;font-weight:800;color:#fff;margin-bottom:4px;">Hello, <span style="color:${acc};">Juan.</span></div>
                    <div style="display:flex;gap:6px;">
                        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:${parseInt(rad)/2}px;padding:4px 8px;">
                            <div style="font-size:8px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;">ID</div>
                            <div style="font-size:9px;font-weight:800;color:${acc};">#APP-2026-00001</div>
                        </div>
                        <div style="padding:5px 10px;background:${acc};border-radius:${parseInt(rad)/1.5}px;font-size:8px;font-weight:800;color:${at};align-self:center;">Upload Docs</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                    ${[{t:'Applicant Form',s:'2 of 4 completed',w:'50%'},{t:'Documents',s:'3 of 10 uploaded',w:'30%'}].map((c,i)=>`
                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:${rad};padding:10px 10px 8px;">
                            <div style="font-size:9px;font-weight:700;color:#0d1e38;margin-bottom:3px;">${c.t}</div>
                            <div style="height:4px;background:#edf1f9;border-radius:99px;margin-bottom:5px;overflow:hidden;">
                                <div style="height:100%;width:${c.w};background:${p};border-radius:99px;"></div>
                            </div>
                            <div style="font-size:8px;color:#5a6a85;">${c.s}</div>
                            <div style="margin-top:6px;padding:3px 8px;background:${lt};border-radius:${parseInt(rad)/1.5}px;font-size:8px;font-weight:700;color:${p};display:inline-block;">${i===0?'Continue':'Upload'}</div>
                        </div>`).join('')}
                </div>
            </div>
        </div>
    </div>`;
}

function renderSignIn(p, pd, acc, at, f, rad, lt) {
    const logoHtml = state.logo_url
        ? `<img src="${state.logo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`
        : `<img src="assets/images/logo_akademus.png" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`;
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;flex-direction:column;height:100%;width:100%;background:linear-gradient(-20deg,${p},${pd},${p},${pd});background-size:400% 400%;">
        <!-- NAVBAR -->
        <div style="width:100%;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;">
                <div style="width:24px;height:24px;border-radius:${rad};overflow:hidden;">${logoHtml}</div>
            </div>
            <div style="display:flex;gap:6px;">
                ${['Admissions','Academics','About Us','Contact Us'].map(n=>`<span style="font-size:7px;color:#fff;padding:4px 7px;">${n}</span>`).join('')}
            </div>
            <span style="font-size:7px;color:#fff;white-space:nowrap;">Proceed to Mentora →</span>
        </div>
        <!-- MAIN CARD AREA -->
        <div style="flex:1;display:flex;justify-content:center;align-items:center;padding:2px 12px 6px;">
            <div style="width:92%;height:88%;background:linear-gradient(180deg,rgba(255,255,255,0.61),rgba(138,140,202,0.61));border-radius:${rad};display:flex;overflow:hidden;">
                <!-- LEFT -->
                <div style="width:50%;padding:14px 22px;border-right:1px solid #D9D9D9;display:flex;flex-direction:column;justify-content:center;gap:12px;">
                    <!-- Logo + name -->
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:26px;height:26px;border-radius:${rad};overflow:hidden;flex-shrink:0;">${logoHtml}</div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:${p};line-height:1.2;word-break:break-word;max-width:150px;">${state.app_name || 'AKADEMUS'}</div>
<div style="font-size:7px;color:${p};">Admission System</div>
                        </div>
                    </div>
                    <!-- Tagline -->
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div>
                            <div style="font-size:18px;font-weight:900;color:#fff;line-height:1.1;">Your Future</div>
                            <div style="font-size:18px;font-weight:900;color:#fff;line-height:1.1;">Starts <span style="color:${acc};">Here</span></div>
                        </div>
                        <div style="font-size:8px;color:rgba(255,255,255,0.85);font-weight:600;line-height:1.4;">Complete your admission entirely online — from document submission to enrollment.</div>
                        <!-- Steps -->
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            ${[['1','Create your applicant account'],['2','Submit required documents'],['3','Track your application status']].map(([n,l])=>`
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:14px;height:14px;border:1px solid ${acc};border-radius:${parseInt(rad)/2}px;display:flex;align-items:center;justify-content:center;font-size:7px;color:#fff;flex-shrink:0;">${n}</div>
                                <span style="font-size:7px;color:rgba(255,255,255,0.85);font-weight:600;">${l}</span>
                            </div>`).join('')}
                        </div>
                    </div>
                </div>
                <!-- RIGHT -->
                <div style="width:50%;padding:14px 22px;display:flex;flex-direction:column;gap:8px;justify-content:center;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:${p};line-height:1;">Sign In</div>
                        <div style="font-size:7px;color:rgba(61,61,61,0.6);font-weight:700;">Welcome back! Sign in to continue.</div>
                    </div>
                    <!-- Toggle -->
                    <div style="display:flex;height:22px;background:rgba(255,255,255,0.5);border-radius:${rad};padding:2px;">
                        <div style="flex:1;display:flex;align-items:center;justify-content:center;background:${p};border-radius:${rad};font-size:8px;font-weight:600;color:#fff;">Sign In</div>
                        <div style="flex:1;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:#656070;">Sign Up</div>
                    </div>
                    <!-- Fields -->
                    ${[['EMAIL ADDRESS','yourname@email.com'],['PASSWORD','••••••••']].map(([l,ph])=>`
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        <div style="font-size:7px;font-weight:600;color:#F2F4F5;margin-left:3px;">${l}</div>
                        <div style="height:20px;background:rgba(255,255,255,0.6);border:1px solid #D9D9D9;border-radius:${rad};padding:0 8px;display:flex;align-items:center;font-size:7px;color:#797777;">${ph}</div>
                    </div>`).join('')}
                    <div style="text-align:right;font-size:7px;color:#F2F4F5;font-weight:600;">Forgot Password?</div>
                    <div style="height:22px;background:${p};border-radius:${rad};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:#fff;">Sign In</div>
                    <div style="text-align:center;font-size:7px;color:#F2F4F5;">Don't have an account? <span style="text-decoration:underline;">Click here</span></div>
                </div>
            </div>
        </div>
    </div>`;
}

function renderSignUp(p, pd, acc, at, f, rad, lt) {
    const logoHtml = state.logo_url
        ? `<img src="${state.logo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`
        : `<img src="assets/images/logo_akademus.png" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`;
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;flex-direction:column;height:100%;width:100%;background:linear-gradient(-20deg,${p},${pd},${p},${pd});background-size:400% 400%;">
        <!-- NAVBAR -->
        <div style="width:100%;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;">
                <div style="width:24px;height:24px;border-radius:${rad};overflow:hidden;">${logoHtml}</div>
            </div>
            <div style="display:flex;gap:6px;">
                ${['Admissions','Academics','About Us','Contact Us'].map(n=>`<span style="font-size:7px;color:#fff;padding:4px 7px;">${n}</span>`).join('')}
            </div>
            <span style="font-size:7px;color:#fff;white-space:nowrap;">Proceed to Mentora →</span>
        </div>
        <!-- MAIN CARD AREA -->
        <div style="flex:1;display:flex;justify-content:center;align-items:center;padding:2px 12px 6px;">
            <div style="width:88%;height:94%;background:linear-gradient(180deg,rgba(255,255,255,0.61),rgba(138,140,202,0.61));border-radius:${rad};display:flex;overflow:hidden;">
                <!-- LEFT -->
                <div style="width:50%;padding:14px 22px;border-right:1px solid #D9D9D9;display:flex;flex-direction:column;justify-content:center;gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:26px;height:26px;border-radius:${rad};overflow:hidden;flex-shrink:0;">${logoHtml}</div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:${p};line-height:1.2;word-break:break-word;max-width:150px;">${state.app_name || 'AKADEMUS'}</div>
<div style="font-size:7px;color:${p};">Admission System</div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div>
                            <div style="font-size:12px;font-weight:900;color:#fff;line-height:1.1;">Your Future</div>
                            <div style="font-size:12px;font-weight:900;color:#fff;line-height:1.1;">Starts <span style="color:${acc};">Here</span></div>
                        </div>
                        <div style="font-size:8px;color:rgba(255,255,255,0.85);font-weight:600;line-height:1.4;">Complete your admission entirely online — from document submission to enrollment.</div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            ${[['1','Create your applicant account'],['2','Submit required documents'],['3','Track your application status']].map(([n,l])=>`
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:14px;height:14px;border:1px solid ${acc};border-radius:${parseInt(rad)/2}px;display:flex;align-items:center;justify-content:center;font-size:7px;color:#fff;flex-shrink:0;">${n}</div>
                                <span style="font-size:7px;color:rgba(255,255,255,0.85);font-weight:600;">${l}</span>
                            </div>`).join('')}
                        </div>
                    </div>
                </div>
                <!-- RIGHT -->
                <div style="width:50%;padding:12px 22px;display:flex;flex-direction:column;gap:6px;justify-content:center;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:${p};line-height:1;">Create Account</div>
                        <div style="font-size:7px;color:rgba(61,61,61,0.6);font-weight:700;">Fill in your details to get started.</div>
                    </div>
                    <!-- Toggle -->
                    <div style="display:flex;height:22px;background:rgba(255,255,255,0.5);border-radius:${rad};padding:2px;">
                        <div style="flex:1;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:#656070;">Sign In</div>
                        <div style="flex:1;display:flex;align-items:center;justify-content:center;background:${p};border-radius:${rad};font-size:8px;font-weight:600;color:#fff;">Sign Up</div>
                    </div>
                    <!-- Two fields row -->
                    <div style="display:flex;gap:6px;">
                        ${[['FIRST NAME','John'],['LAST NAME','Doe']].map(([l,ph])=>`
                        <div style="flex:1;display:flex;flex-direction:column;gap:2px;">
                            <div style="font-size:7px;font-weight:600;color:#F2F4F5;margin-left:3px;">${l}</div>
                            <div style="height:18px;background:rgba(255,255,255,0.6);border:1px solid #D9D9D9;border-radius:${rad};padding:0 6px;display:flex;align-items:center;font-size:7px;color:#797777;">${ph}</div>
                        </div>`).join('')}
                    </div>
                    <!-- Single fields -->
                    ${[['EMAIL ADDRESS','yourname@email.com'],['MOBILE NUMBER','09XXXXXXXXX']].map(([l,ph])=>`
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        <div style="font-size:7px;font-weight:600;color:#F2F4F5;margin-left:3px;">${l}</div>
                        <div style="height:18px;background:rgba(255,255,255,0.6);border:1px solid #D9D9D9;border-radius:${rad};padding:0 8px;display:flex;align-items:center;font-size:7px;color:#797777;">${ph}</div>
                    </div>`).join('')}
                    <!-- Program select -->
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        <div style="font-size:7px;font-weight:600;color:#F2F4F5;margin-left:3px;">1ST CHOICE OF PROGRAM</div>
                        <div style="height:18px;background:rgba(255,255,255,0.6);border:1px solid #D9D9D9;border-radius:${rad};padding:0 8px;display:flex;align-items:center;font-size:7px;color:#9b9999;">Select a Program ▾</div>
                    </div>
                    <!-- Note box -->
                    <div style="border:1px solid rgba(0,64,166,0.4);background:rgba(0,64,166,0.2);border-radius:${rad};padding:4px 8px;">
                        <div style="font-size:7px;font-weight:600;color:${p};">Note:</div>
                        <div style="font-size:8px;color:#031D46;font-weight:700;opacity:0.8;line-height:1.3;">Your final program will be confirmed in the Applicant Information Form.</div>
                    </div>
                    <div style="height:20px;background:${p};border-radius:${rad};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:#fff;">CREATE ACCOUNT</div>
                </div>
            </div>
        </div>
    </div>`;
}

function renderAppForm(p, pd, acc, at, f, rad, lt) {
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;height:100%;width:100%;">
        ${getSidebarHtml(p,pd,acc,at,f,rad)}
        <div style="flex:1;display:flex;flex-direction:column;background:#f8fafc;min-width:0;">
            ${getTopbarHtml('Application Form',p,acc,at,rad)}
            <div style="flex:1;padding:10px;overflow:hidden;display:flex;flex-direction:column;gap:8px;">
                <!-- Step indicator -->
                <div style="display:flex;align-items:center;gap:4px;background:#fff;border:1px solid #e2e8f0;border-radius:${rad};padding:8px 12px;">
                    ${[['1','Personal','done'],['2','Academic','active'],['3','Contact',''],['4','Review','']].map(([n,l,s])=>`
                        <div style="display:flex;align-items:center;gap:4px;flex:1;">
                            <div style="width:18px;height:18px;border-radius:50%;background:${s==='done'?acc:s==='active'?p:'#e2e8f0'};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:700;color:${s==='done'?at:s==='active'?'#fff':'#94a3b8'};flex-shrink:0;">${s==='done'?'✓':n}</div>
                            <span style="font-size:8px;font-weight:${s==='active'?700:500};color:${s==='active'?p:'#94a3b8'};">${l}</span>
                            ${n!='4'?`<div style="flex:1;height:1px;background:${s==='done'?acc:'#e2e8f0'};margin:0 4px;"></div>`:''}
                        </div>`).join('')}
                </div>
                <!-- Form fields -->
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:${rad};padding:10px 12px;flex:1;">
                    <div style="font-size:10px;font-weight:700;color:#0d1e38;margin-bottom:8px;">Academic Information</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        ${[['Previous School','St. Paul University Manila'],['Year Level','Grade 12 / Senior High'],['Program Applying For','BS Computer Science'],['GWA / Grade Average','95.5']].map(([l,v])=>`
                            <div>
                                <div style="font-size:8px;font-weight:700;color:#5a6a85;margin-bottom:3px;">${l}</div>
                                <div style="height:26px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:${parseInt(rad)/1.5}px;padding:0 10px;display:flex;align-items:center;font-size:9px;color:#0d1e38;font-weight:600;">${v}</div>
                            </div>`).join('')}
                    </div>
                    <div style="margin-top:8px;">
                        <div style="font-size:8px;font-weight:700;color:#5a6a85;margin-bottom:3px;">Honors & Awards</div>
                        <div style="height:40px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:${parseInt(rad)/1.5}px;padding:8px 10px;font-size:9px;color:#94a3b8;">With Honors, Academic Excellence Award 2024...</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                        <div style="padding:5px 12px;border:1px solid #e2e8f0;border-radius:${parseInt(rad)/1.5}px;font-size:9px;font-weight:600;color:#5a6a85;">← Back</div>
                        <div style="padding:5px 14px;background:${p};border-radius:${parseInt(rad)/1.5}px;font-size:9px;font-weight:700;color:#fff;">Save & Continue →</div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

function renderDocuments(p, pd, acc, at, f, rad, lt) {
    const docs = [
        {n:'Birth Certificate',s:'uploaded',i:'✓'},
        {n:'Form 138 / Report Card',s:'uploaded',i:'✓'},
        {n:'Good Moral Certificate',s:'pending',i:'⏳'},
        {n:'2x2 ID Photo',s:'missing',i:'📷'},
        {n:'Medical Certificate',s:'missing',i:'📋'},
    ];
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;height:100%;width:100%;">
        ${getSidebarHtml(p,pd,acc,at,f,rad)}
        <div style="flex:1;display:flex;flex-direction:column;background:#f8fafc;min-width:0;">
            ${getTopbarHtml('Document Upload',p,acc,at,rad)}
            <div style="flex:1;padding:10px;overflow:hidden;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:10px;font-weight:700;color:#0d1e38;">Required Documents <span style="color:#5a6a85;font-weight:500;">(2 of 5 uploaded)</span></div>
                    <div style="height:5px;width:120px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:40%;background:${p};border-radius:99px;"></div>
                    </div>
                </div>
                ${docs.map(d=>`
                    <div style="background:#fff;border:1px solid ${d.s==='uploaded'?acc+'66':d.s==='pending'?p+'44':'#e2e8f0'};border-radius:${rad};padding:8px 10px;display:flex;align-items:center;gap:10px;">
                        <div style="width:28px;height:28px;border-radius:${parseInt(rad)/1.5}px;background:${d.s==='uploaded'?acc+'22':d.s==='pending'?lt:'#f0f4f8'};display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">${d.i}</div>
                        <div style="flex:1;">
                            <div style="font-size:9px;font-weight:700;color:#0d1e38;">${d.n}</div>
                            <div style="font-size:8px;color:${d.s==='uploaded'?'#0a7a52':d.s==='pending'?p:'#94a3b8'};">${d.s==='uploaded'?'Uploaded successfully':d.s==='pending'?'Under review':'Not yet uploaded'}</div>
                        </div>
                        <div style="padding:3px 10px;border-radius:${parseInt(rad)/1.5}px;font-size:8px;font-weight:700;background:${d.s==='uploaded'?'#dcfce7':d.s==='pending'?lt:p};color:${d.s==='uploaded'?'#166534':d.s==='pending'?p:'#fff'};">${d.s==='uploaded'?'View':d.s==='pending'?'Pending':'Upload'}</div>
                    </div>`).join('')}
            </div>
        </div>
    </div>`;
}

function renderTrack(p, pd, acc, at, f, rad, lt) {
    const steps = [
        {l:'Application Submitted',d:'Feb 10, 2026',done:true},
        {l:'Documents Verified',d:'Feb 14, 2026',done:true},
        {l:'Entrance Exam Scheduled',d:'Feb 20, 2026',done:true},
        {l:'Interview',d:'Mar 5, 2026',active:true},
        {l:'Final Decision',d:'Pending',done:false},
    ];
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;height:100%;width:100%;">
        ${getSidebarHtml(p,pd,acc,at,f,rad)}
        <div style="flex:1;display:flex;flex-direction:column;background:#f8fafc;min-width:0;">
            ${getTopbarHtml('Track Progress',p,acc,at,rad)}
            <div style="flex:1;padding:10px;overflow:hidden;display:flex;flex-direction:column;gap:8px;">
                <!-- Status card -->
                <div style="background:${p};border-radius:${rad};padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:8px;color:rgba(255,255,255,0.5);text-transform:uppercase;font-weight:700;margin-bottom:2px;">Current Status</div>
                        <div style="font-size:13px;font-weight:800;color:#fff;">Interview Scheduled</div>
                        <div style="font-size:9px;color:${acc};font-weight:600;margin-top:2px;">Mar 5, 2026 · 10:00 AM · Room 204</div>
                    </div>
                    <div style="padding:6px 12px;background:${acc};border-radius:${parseInt(rad)/1.5}px;font-size:9px;font-weight:700;color:${at};">View Details</div>
                </div>
                <!-- Timeline -->
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:${rad};padding:10px 14px;flex:1;">
                    <div style="font-size:10px;font-weight:700;color:#0d1e38;margin-bottom:10px;">Application Timeline</div>
                    ${steps.map((s,i)=>`
                        <div style="display:flex;gap:10px;margin-bottom:${i<steps.length-1?'8':'0'}px;">
                            <div style="display:flex;flex-direction:column;align-items:center;">
                                <div style="width:16px;height:16px;border-radius:50%;background:${s.done?acc:s.active?p:'#e2e8f0'};display:flex;align-items:center;justify-content:center;font-size:8px;color:${s.done?at:s.active?'#fff':'#94a3b8'};flex-shrink:0;font-weight:700;">${s.done?'✓':s.active?'●':''}</div>
                                ${i<steps.length-1?`<div style="width:1px;flex:1;background:${s.done?acc+'55':'#e2e8f0'};margin:2px 0;min-height:14px;"></div>`:''}
                            </div>
                            <div style="padding-bottom:${i<steps.length-1?'6':'0'}px;">
                                <div style="font-size:9px;font-weight:${s.active?700:600};color:${s.active?p:s.done?'#0d1e38':'#94a3b8'};">${s.l}</div>
                                <div style="font-size:8px;color:#94a3b8;">${s.d}</div>
                            </div>
                        </div>`).join('')}
                </div>
            </div>
        </div>
    </div>`;
}

function renderSuccess(p, pd, acc, at, f, rad, lt) {
    const logoHtml = state.logo_url
        ? `<img src="${state.logo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`
        : `<img src="assets/images/logo_akademus.png" style="width:100%;height:100%;object-fit:cover;border-radius:${rad};">`;
    return `
    <div style="font-family:'${f}',sans-serif;display:flex;flex-direction:column;height:100%;width:100%;background:linear-gradient(-20deg,${p},${pd},${p},${pd});background-size:400% 400%;">
        <!-- NAVBAR -->
        <div style="width:100%;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="width:24px;height:24px;border-radius:${rad};overflow:hidden;">${logoHtml}</div>
            <div style="display:flex;gap:6px;">
                ${['Admissions','Academics','About Us','Contact Us'].map(n=>`<span style="font-size:7px;color:#fff;padding:4px 7px;">${n}</span>`).join('')}
            </div>
            <span style="font-size:7px;color:#fff;white-space:nowrap;">Proceed to Mentora →</span>
        </div>
        <!-- MAIN CARD AREA -->
        <div style="flex:1;display:flex;justify-content:center;align-items:center;padding:2px 12px 6px;">
            <div style="width:88%;height:88%;background:linear-gradient(180deg,rgba(255,255,255,0.61),rgba(138,140,202,0.61));border-radius:${rad};display:flex;overflow:hidden;">
                <!-- LEFT (same as sign-in) -->
                <div style="width:50%;padding:14px 22px;border-right:1px solid #D9D9D9;display:flex;flex-direction:column;justify-content:center;gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:26px;height:26px;border-radius:${rad};overflow:hidden;flex-shrink:0;">${logoHtml}</div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:${p};line-height:1.2;word-break:break-word;max-width:150px;">${state.app_name || 'AKADEMUS'}</div>
<div style="font-size:7px;color:${p};">Admission System</div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div>
                            <div style="font-size:12px;font-weight:900;color:#fff;line-height:1.1;">Your Future</div>
                            <div style="font-size:12px;font-weight:900;color:#fff;line-height:1.1;">Starts <span style="color:${acc};">Here</span></div>
                        </div>
                        <div style="font-size:8px;color:rgba(255,255,255,0.85);font-weight:600;line-height:1.4;">Complete your admission entirely online — from document submission to enrollment.</div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            ${[['1','Create your applicant account'],['2','Submit required documents'],['3','Track your application status']].map(([n,l])=>`
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:14px;height:14px;border:1px solid ${acc};border-radius:${parseInt(rad)/2}px;display:flex;align-items:center;justify-content:center;font-size:7px;color:#fff;flex-shrink:0;">${n}</div>
                                <span style="font-size:7px;color:rgba(255,255,255,0.85);font-weight:600;">${l}</span>
                            </div>`).join('')}
                        </div>
                    </div>
                </div>
                <!-- RIGHT — success view, no toggle -->
                <div style="width:50%;padding:14px 22px;display:flex;flex-direction:column;gap:10px;justify-content:center;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:${p};line-height:1;">Registration Successful! 🎉</div>
                        <div style="font-size:7px;color:rgba(61,61,61,0.6);font-weight:700;margin-top:2px;">Your account has been created.</div>
                    </div>
                    <!-- Applicant ID box -->
                    <div style="width:100%;padding:10px 10px 18px;background:rgba(0,64,166,0.2);border-radius:${rad};border:1px solid rgba(0,64,166,0.4);display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <div style="font-size:8px;font-weight:600;color:${p};">Applicant ID</div>
                        <div style="font-size:16px;font-weight:700;color:#031D46;">#APP-2026-00001</div>
                    </div>
                    <!-- Info text -->
                    <div style="font-size:7px;color:#3D3D3D;font-weight:700;line-height:1.5;">A welcome email with your ID has been sent to your registered email address. Keep this for your records.</div>
                    <!-- Go back btn -->
                    <div style="height:22px;background:${p};border-radius:${rad};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:#fff;">GO BACK TO LOGIN</div>
                </div>
            </div>
        </div>
    </div>`;
}

// ── MASTER UPDATE ─────────────────────────────────────────
function updatePreview() {
    const p   = state.primary_color;
    const pd  = state.sidebar_color;
    const acc = state.accent_color;
    const at  = state.accent_text;
    const f   = state.font_family;
    const rad = state.border_radius;
    const lt  = lighten(p);

    const renders = {
        dashboard: renderDashboard,
        signin:    renderSignIn,
        signup:    renderSignUp,
        appform:   renderAppForm,
        documents: renderDocuments,
        track:     renderTrack,
        success:   renderSuccess,
    };

    const fn = renders[currentPage] || renderDashboard;
    document.getElementById('portal-preview').innerHTML = fn(p, pd, acc, at, f, rad, lt);
}

// ── PRESETS / FONTS / RADII ───────────────────────────────
function buildPresets() {
    document.getElementById('preset-grid').innerHTML = PRESETS.map(p => `
        <div class="preset-card ${state.preset === p.id ? 'on' : ''}" data-preset="${p.id}" onclick="applyPreset('${p.id}')">
            <div class="preset-dots">
                <div class="preset-dot" style="background:${p.primary_dark}"></div>
                <div class="preset-dot" style="background:${p.primary_color}"></div>
                <div class="preset-dot" style="background:${p.accent_color}"></div>
            </div>
            <div class="preset-name">${p.name}</div>
            <div class="preset-desc">${p.desc}</div>
        </div>`).join('');
}

function applyPreset(id) {
    const p = PRESETS.find(x => x.id === id);
    if (!p) return;
    Object.assign(state, { ...p });
    state.preset = id;
    ['primary_color','primary_dark','primary_light','accent_color','accent_text','sidebar_color'].forEach(key => {
        if (!state[key]) return;
        const inp = document.getElementById('inp_' + key);
        if (inp) inp.value = state[key];
        const dot = document.getElementById('dot_' + key);
        if (dot) dot.style.background = state[key];
        const hex = document.getElementById('hex_' + key);
        if (hex) hex.textContent = state[key];
    });
    document.querySelectorAll('.preset-card').forEach(c => c.classList.toggle('on', c.dataset.preset === id));
    document.getElementById('pf-preset-name').textContent = p.name;
    buildColorPreview(); updatePreview(); updateFontPreview();
}

function buildFonts() {
    document.getElementById('font-options').innerHTML = FONTS.map(f => `
        <div class="font-option ${state.font_family === f ? 'on' : ''}" data-font="${f}" onclick="selectFont('${f}')">
            <div class="font-radio"></div>
            <div>
                <div class="font-name" style="font-family:'${f}',sans-serif">${f}</div>
                <div class="font-sample" style="font-family:'${f}',sans-serif">Aa Bb Cc 123</div>
            </div>
        </div>`).join('');
}

function selectFont(f) {
    state.font_family = f;
    document.querySelectorAll('.font-option').forEach(el => el.classList.toggle('on', el.dataset.font === f));
    document.getElementById('pf-font').textContent = f;
    updateFontPreview(); updatePreview();
}

function updateFontPreview() {
    const f = state.font_family;
    const fp = document.getElementById('font-preview');
    if (fp) fp.style.fontFamily = `'${f}', sans-serif`;
    const btn1 = document.getElementById('fp-btn1');
    const btn2 = document.getElementById('fp-btn2');
    if (btn1) { btn1.style.background = state.primary_color; btn1.style.fontFamily = `'${f}', sans-serif`; }
    if (btn2) { btn2.style.background = state.accent_color; btn2.style.color = state.accent_text; btn2.style.fontFamily = `'${f}', sans-serif`; }
}

function buildRadii() {
    document.getElementById('radius-row').innerHTML = RADII.map(r => `
        <button class="radius-btn ${state.border_radius === r.value ? 'on' : ''}" data-radius="${r.value}" onclick="selectRadius('${r.value}')">
            <div class="radius-preview" style="border-radius:${r.value}"></div>
            ${r.label}
        </button>`).join('');
}

function selectRadius(v) {
    state.border_radius = v;
    document.querySelectorAll('.radius-btn').forEach(b => b.classList.toggle('on', b.dataset.radius === v));
    updatePreview();
}

function buildColorPreview() {
    const container = document.getElementById('color-preview-swatches');
    [
        { label:'Sidebar', color: state.sidebar_color },
        { label:'Primary', color: state.primary_color },
        { label:'Light',   color: state.primary_light },
        { label:'Accent',  color: state.accent_color },
    ].forEach(s => {
        let el = container.querySelector(`[data-label="${s.label}"]`);
        if (!el) {
            el = document.createElement('div');
            el.className = 'cp-swatch';
            el.dataset.label = s.label;
            el.innerHTML = `<div class="cp-dot"></div><span class="cp-label">${s.label}</span>`;
            container.appendChild(el);
        }
        el.querySelector('.cp-dot').style.background = s.color;
    });
}

// ── SAVE ──────────────────────────────────────────────────
async function saveTheme() {
    const btn = document.getElementById('btn-save');
    btn.textContent = 'Saving…';
    btn.disabled = true;

    const formData = new FormData();
    formData.append(CSRF_NAME, CSRF_HASH);

    const fields = ['primary_color','primary_dark','primary_light','accent_color','accent_text',
                    'sidebar_color','font_family','border_radius','app_name','logo_text',
                    'school_year','admission_status'];
    fields.forEach(k => formData.append(k, state[k] || ''));
    if (state.preset) formData.append('preset', state.preset);

    formData.append('logo_url', state.logo_url || '');

    try {
        const res = await fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch(e) {
            console.error('Non-JSON response:', text);
            alert('Save failed — server returned an unexpected response. Check the CI4 log.');
            btn.textContent = 'Save & Apply'; btn.disabled = false;
            return;
        }
        if (data.status === 'success') {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        } else {
            alert('Save failed: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Save failed — network error.');
    }

    btn.textContent = 'Save & Apply';
    btn.disabled = false;
}

// ── RESET ─────────────────────────────────────────────────
function resetToDefault() {
    applyPreset('spum-blue');
    state.font_family = 'Sora'; state.border_radius = '8px';
    state.app_name = 'Akademus Portal'; state.logo_text = 'AK'; state.logo_url = '';
    state.school_year = 'SY 2025-2026'; state.admission_status = 'Open for Applications';
    document.getElementById('app_name').value  = state.app_name;
    document.getElementById('logo_text').value = state.logo_text;
    document.getElementById('logo_url').value  = '';
    buildFonts(); buildRadii(); updatePreview(); updateFontPreview();
}

init();
</script>
</body>
</html>