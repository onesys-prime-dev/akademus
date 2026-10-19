<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Theme Customizer</title>
    <?= theme_favicon_tags() ?>
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

        /* sidebar styles are in admin/_sidebar.php */

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
        .editor-body { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 18px 20px; }
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
        .sec-icon { display: inline-flex; color: #5a6a85; flex-shrink: 0; }
        .sec-icon svg { width: 15px; height: 15px; display: block; }
        .sec-title { font-size: 13px; font-weight: 700; color: #0d1e38; letter-spacing: -0.2px; }
        .sec-desc { font-size: 11px; color: #5a6a85; padding-left: 23px; }
        .divider { height: 1px; background: #f0f4f8; margin: 18px 0; }

        /* ── PRESETS ── */
        .preset-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
        .preset-card {
            position: relative;
            border: 1.5px solid #dde3f0; border-radius: 10px; padding: 10px 11px;
            cursor: pointer; transition: all 0.18s; background: #fff;
        }
        .preset-card:hover { border-color: #94a3b8; transform: translateY(-1px); }
        .preset-card.on { border-color: #0040a6; background: #e8eef8; }
        .preset-dots { display: flex; gap: 4px; margin-bottom: 6px; }
        .preset-dot { width: 14px; height: 14px; border-radius: 4px; }
        .preset-name { font-size: 11px; font-weight: 700; color: #0d1e38; }
        .preset-desc { font-size: 9px; color: #5a6a85; }
        .preset-delete {
            position: absolute; top: 4px; right: 4px; width: 18px; height: 18px;
            border: none; border-radius: 50%; background: rgba(0,0,0,0.06);
            color: #5a6a85; font-size: 13px; line-height: 1; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .preset-delete:hover { background: #fee2e2; color: #dc2626; }

        /* ── COLOR SWATCHES ── */
        .color-grid { display: flex; flex-direction: column; gap: 10px; }
        .swatch-label { font-size: 10px; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 5px; }
        .swatch-row {
            display: flex; align-items: center; gap: 8px; padding: 7px 10px;
            border: 1.5px solid #dde3f0; border-radius: 8px;
            background: #fff; transition: border-color 0.18s;
        }
        .swatch-row:focus-within { border-color: #0040a6; box-shadow: 0 0 0 3px rgba(0,64,166,0.08); }
        .swatch-dot-wrap {
            position: relative; width: 20px; height: 20px; border-radius: 5px;
            flex-shrink: 0; cursor: pointer; overflow: hidden;
        }
        .swatch-dot { width: 100%; height: 100%; border-radius: 5px; border: 1px solid rgba(0,0,0,0.1); box-sizing: border-box; }
        .swatch-color-input {
            position: absolute; inset: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer; border: none; padding: 0;
        }
        .swatch-hex-input {
            flex: 1; min-width: 0; border: none; outline: none; background: transparent;
            font-size: 12px; font-weight: 600; color: #0d1e38; font-family: monospace;
            padding: 0; text-transform: lowercase;
        }
        .swatch-hex-input::placeholder { color: #c4cbda; }

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
        #preview-iframe { display: block; width: 100%; height: 100%; border: none; opacity: 1; transition: opacity 0.2s ease; }
        #preview-iframe.fading { opacity: 0; }

        .preview-body { flex: 1; display: flex; align-items: stretch; padding: 20px; background: #f0f4f8; overflow: hidden; }
        .preview-frame {
            flex: 1; border-radius: 14px; overflow: hidden;
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
function ti(string $name): string
{
    $paths = [
        'palette'  => '<path d="M12 22a1 1 0 0 1 0-20 10 9 0 0 1 10 9 5 5 0 0 1-5 5h-2.25a1.75 1.75 0 0 0-1.4 2.8l.3.4a1.75 1.75 0 0 1-1.4 2.8z"/><circle cx="6.5" cy="11.5" r="1.5"/><circle cx="9.5" cy="7.5" r="1.5"/><circle cx="14.5" cy="7.5" r="1.5"/><circle cx="17.5" cy="11.5" r="1.5"/>',
        'droplet'  => '<path d="M12 2.69 17.66 8.35a8 8 0 1 1-11.31 0z"/>',
        'sidebar'  => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/>',
        'image'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
        'button'   => '<rect x="4" y="8" width="16" height="8" rx="4"/>',
        'heading'  => '<polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/>',
        'activity' => '<line x1="4" y1="20" x2="4" y2="14"/><line x1="10" y1="20" x2="10" y2="10"/><line x1="16" y1="20" x2="16" y2="6"/>',
        'pencil'   => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'radius'   => '<rect x="3" y="3" width="18" height="18" rx="6"/>',
        'info'     => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
        'upload'   => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>',
        'save'     => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
    ];
    $path = $paths[$name] ?? '';
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
}

$csrfToken = csrf_token();
$csrfHash  = csrf_hash();

$logo = $theme['logo_url'] ?? '';

$logoSrc = '';

if (!empty($logo)) {
    $logoSrc = str_starts_with($logo, 'http') || str_starts_with($logo, 'data:image')
        ? $logo
        : base_url($logo);
}
?>

<div class="toast" id="toast">Theme saved & applied to portal</div>

<div class="shell">

    <?php echo view('admin/_sidebar', ['theme' => $theme ?? []]); ?>

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
                        <?php if (!empty($logoSrc)): ?>
                            <img
                                src="<?= esc($logoSrc, 'attr') ?>"
                                style="width:48px;height:48px;object-fit:contain;border-radius:8px;margin-bottom:4px;"
                            >
                            <div style="font-size:10px;color:#5a6a85;">Click to change</div>
                        <?php else: ?>
                            <div id="upload-placeholder">
                                <div class="sec-icon" style="justify-content:center;margin-bottom:6px;"><?= ti('upload') ?></div>
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
                    <div class="sec-head-row"><span class="sec-icon"><?= ti('palette') ?></span><span class="sec-title">Preset Themes</span></div>
                    <div class="sec-desc">Start from a preset and fine-tune from there</div>
                </div>
                <div class="preset-grid" id="preset-grid"></div>
                <div id="custom-preset-section" style="display:none;">
                    <div class="sec-head" style="margin-top:16px;">
                        <div class="sec-head-row"><span class="sec-icon"><?= ti('save') ?></span><span class="sec-title">Your Presets</span></div>
                        <div class="sec-desc">Saved from your own color combinations</div>
                    </div>
                    <div class="preset-grid" id="custom-preset-grid"></div>
                </div>
                <button type="button" class="btn-reset" style="width:100%;margin-top:10px;" onclick="saveCurrentAsPreset()">Save Current as Preset</button>
            </div>

            <!-- COLORS TAB -->
            <div class="tab-pane" id="tab-colors">
                <?php
                $colorGroups = [
                    [ 'icon' => 'droplet', 'title' => 'Brand Colors', 'desc' => 'Fine-tune individual colors to match your institution', 'fields' => [
                        'primary_color' => ['Primary',       '#0040a6'],
                        'primary_dark'  => ['Primary Dark',  '#002d7a'],
                        'primary_light' => ['Primary Light', '#1a55c0'],
                        'accent_color'  => ['Accent',        '#ffb200'],
                        'accent_text'   => ['Accent Text',   '#002d7a'],
                    ]],
                    [ 'icon' => 'sidebar', 'title' => 'Sidebar', 'desc' => 'The navigation sidebar, independent from the rest of the app', 'fields' => [
                        'sidebar_color'        => ['Sidebar Background',      '#002d7a'],
                        'sidebar_active_color' => ['Sidebar Active Highlight', '#ffb200'],
                    ]],
                    [ 'icon' => 'image', 'title' => 'Dashboard Banner', 'desc' => 'The welcome banner at the top of the applicant dashboard', 'fields' => [
                        'banner_bg_color'  => ['Banner Background',       '#0040a6'],
                        'banner_box_color' => ['Applicant ID Box',        '#1a55c0'],
                    ]],
                    [ 'icon' => 'button', 'title' => 'Buttons', 'desc' => 'Call-to-action buttons used across the portal', 'fields' => [
                        'button_primary_bg'   => ['Primary Button Background', '#0040a6'],
                        'button_primary_text' => ['Primary Button Text',       '#ffffff'],
                        'button_accent_bg'    => ['Accent Button Background',  '#ffb200'],
                        'button_accent_text'  => ['Accent Button Text',        '#002d7a'],
                    ]],
                    [ 'icon' => 'heading', 'title' => 'Header', 'desc' => 'The page-title text shown at the top of every page', 'fields' => [
                        'header_text_color' => ['Page Title Text', '#0040a6'],
                    ]],
                    [ 'icon' => 'activity', 'title' => 'Progress Indicators', 'desc' => 'The "in-progress" step markers across the portal', 'fields' => [
                        'progress_active_color' => ['In-Progress Color', '#ffb200'],
                    ]],
                ];
                foreach ($colorGroups as $i => $group): ?>
                <div class="sec-head"<?= $i > 0 ? ' style="margin-top:16px;"' : '' ?>>
                    <div class="sec-head-row"><span class="sec-icon"><?= ti($group['icon']) ?></span><span class="sec-title"><?= $group['title'] ?></span></div>
                    <div class="sec-desc"><?= $group['desc'] ?></div>
                </div>
                <div class="color-grid">
                    <?php foreach ($group['fields'] as $key => [$label, $default]):
                        $val = $theme[$key] ?? $default;
                    ?>
                    <div>
                        <div class="swatch-label"><?= $label ?></div>
                        <div class="swatch-row">
                            <label class="swatch-dot-wrap">
                                <div class="swatch-dot" id="dot_<?= $key ?>" style="background:<?= $val ?>"></div>
                                <input type="color" id="inp_<?= $key ?>" value="<?= $val ?>" class="swatch-color-input">
                            </label>
                            <input type="text" class="swatch-hex-input" id="hex_<?= $key ?>" value="<?= $val ?>" placeholder="#rrggbb" maxlength="7" spellcheck="false" autocomplete="off">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                <div class="divider"></div>
                <div class="color-preview-box">
                    <div class="color-preview-label">Live Color Preview</div>
                    <div class="color-preview-swatches" id="color-preview-swatches"></div>
                </div>
            </div>

            <!-- TYPOGRAPHY TAB -->
            <div class="tab-pane" id="tab-typography">
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon"><?= ti('pencil') ?></span><span class="sec-title">Font Family</span></div>
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
                    <div class="sec-head-row"><span class="sec-icon"><?= ti('radius') ?></span><span class="sec-title">Border Radius</span></div>
                    <div class="sec-desc">Controls the roundness of cards, buttons, and inputs</div>
                </div>
                <div class="radius-row" id="radius-row"></div>
                <div class="divider"></div>
                <div class="sec-head">
                    <div class="sec-head-row"><span class="sec-icon"><?= ti('info') ?></span><span class="sec-title">Portal Info</span></div>
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
                    <option value="dashboard">Applicant Dashboard</option>
                    <option value="appform">Applicant Form</option>
                    <option value="documents">Requirements</option>
                    <option value="track">Tracking Progress</option>
                    <option value="exam">Exam Schedule</option>
                    <option value="admission-fee">Admission Processing Fee</option>
                    <option value="reservation-fee">Reservation Fee</option>
                    <option value="faq">FAQ &amp; Chat Support</option>
                    <option value="followup">Follow-Up Messages</option>
                    <option value="tuition-calc">Tuition Fee Calculator</option>
                    <option value="signin">Sign In Page</option>
                    <option value="signup">Sign Up Page</option>
                    <option value="success">Registration Success</option>
                </select>
            </div>
            <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">Applicant Portal View</span>
        </div>
        <div class="preview-body">
            <div class="preview-frame">
                <iframe id="preview-iframe"
                        src="<?= base_url('admin/theme/preview?page=dashboard') ?>"
                        frameborder="0"
                        title="Portal Preview"></iframe>
            </div>
        </div>
        <div class="preview-footer">
            <div class="pf-info">
                <strong id="pf-preset-name"><?= esc(ucwords(str_replace('-',' ', $theme['preset'] ?? 'Classic Blue'))) ?></strong>
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
    sidebar_active_color:  '<?= $theme['sidebar_active_color']  ?? '#ffb200' ?>',
    banner_bg_color:       '<?= $theme['banner_bg_color']       ?? '#0040a6' ?>',
    banner_box_color:      '<?= $theme['banner_box_color']      ?? '#1a55c0' ?>',
    button_primary_bg:     '<?= $theme['button_primary_bg']     ?? '#0040a6' ?>',
    button_primary_text:   '<?= $theme['button_primary_text']   ?? '#ffffff' ?>',
    button_accent_bg:      '<?= $theme['button_accent_bg']      ?? '#ffb200' ?>',
    button_accent_text:    '<?= $theme['button_accent_text']    ?? '#002d7a' ?>',
    header_text_color:     '<?= $theme['header_text_color']     ?? '#0040a6' ?>',
    progress_active_color: '<?= $theme['progress_active_color'] ?? '#ffb200' ?>',
    font_family:      '<?= $theme['font_family']      ?? 'Sora' ?>',
    border_radius:    '<?= $theme['border_radius']    ?? '8px' ?>',
    app_name:         '<?= esc($theme['app_name']     ?? 'Akademus Portal') ?>',
    logo_text:        '<?= esc($theme['logo_text']    ?? 'AK') ?>',
    logo_url:         '<?= esc($theme['logo_url']     ?? '') ?>',
    school_year:      '<?= esc($theme['school_year']  ?? 'SY 2025-2026') ?>',
    admission_status: '<?= esc($theme['admission_status'] ?? 'Open for Applications') ?>',
    preset:           '<?= esc($theme['preset']       ?? 'classic-blue') ?>',
};

// Fields every preset (built-in or custom) carries.
const PRESET_COLOR_KEYS = [
    'primary_color','primary_dark','primary_light','accent_color','accent_text',
    'sidebar_color','sidebar_active_color','banner_bg_color','banner_box_color',
    'button_primary_bg','button_primary_text','button_accent_bg','button_accent_text',
    'header_text_color','progress_active_color',
];

// Fills in the 9 granular tokens from the 6 base brand colors, matching
// today's coupled look — every field stays independently overridable
// afterward, this just gives each built-in preset a sensible starting point.
function withDerivedTokens(p) {
    return Object.assign({
        sidebar_active_color: p.accent_color,
        banner_bg_color:      p.primary_color,
        banner_box_color:     p.primary_light,
        button_primary_bg:    p.primary_color,
        button_primary_text:  '#ffffff',
        button_accent_bg:     p.accent_color,
        button_accent_text:   p.accent_text,
        header_text_color:    p.primary_color,
        progress_active_color: p.accent_color,
    }, p);
}

const PRESETS = [
    { id:'classic-blue', name:'Classic Blue',  desc:'Default institutional blue', primary_color:'#0040a6', primary_dark:'#002d7a', primary_light:'#1a55c0', accent_color:'#ffb200', accent_text:'#002d7a', sidebar_color:'#002d7a' },
    { id:'forest',   name:'Forest Green',  desc:'Fresh and professional',     primary_color:'#0a6640', primary_dark:'#064d30', primary_light:'#0e7a4c', accent_color:'#f5c518', accent_text:'#064d30', sidebar_color:'#064d30' },
    { id:'crimson',  name:'Crimson',       desc:'Bold and distinguished',     primary_color:'#9b1c1c', primary_dark:'#7a1515', primary_light:'#b52323', accent_color:'#fbbf24', accent_text:'#7a1515', sidebar_color:'#7a1515' },
    { id:'violet',   name:'Royal Violet',  desc:'Modern and refined',         primary_color:'#5b21b6', primary_dark:'#4c1d95', primary_light:'#6d28d9', accent_color:'#f59e0b', accent_text:'#4c1d95', sidebar_color:'#3b0764' },
    { id:'slate',    name:'Slate Dark',    desc:'Sleek and corporate',        primary_color:'#1e293b', primary_dark:'#0f172a', primary_light:'#334155', accent_color:'#38bdf8', accent_text:'#0f172a', sidebar_color:'#0f172a' },
    { id:'rose',     name:'Rose Gold',     desc:'Warm and approachable',      primary_color:'#9f1239', primary_dark:'#881337', primary_light:'#be123c', accent_color:'#fda4af', accent_text:'#881337', sidebar_color:'#4c0519' },
    { id:'ocean',    name:'Ocean Teal',    desc:'Calm and trustworthy',       primary_color:'#0e7490', primary_dark:'#155e75', primary_light:'#0891b2', accent_color:'#fbbf24', accent_text:'#155e75', sidebar_color:'#164e63' },
    { id:'sunset',   name:'Sunset Orange', desc:'Energetic and welcoming',    primary_color:'#c2410c', primary_dark:'#9a3412', primary_light:'#ea580c', accent_color:'#fde047', accent_text:'#9a3412', sidebar_color:'#7c2d12' },
    { id:'emerald',  name:'Emerald',       desc:'Vibrant and growth-minded',  primary_color:'#047857', primary_dark:'#065f46', primary_light:'#059669', accent_color:'#facc15', accent_text:'#065f46', sidebar_color:'#064e3b' },
    { id:'graphite', name:'Graphite',      desc:'Minimal and neutral',        primary_color:'#374151', primary_dark:'#1f2937', primary_light:'#4b5563', accent_color:'#f97316', accent_text:'#1f2937', sidebar_color:'#111827' },
    { id:'burgundy', name:'Burgundy',      desc:'Rich and traditional',       primary_color:'#7f1d1d', primary_dark:'#651414', primary_light:'#991b1b', accent_color:'#eab308', accent_text:'#651414', sidebar_color:'#450a0a' },
    { id:'sky',      name:'Sky Blue',      desc:'Light and approachable',     primary_color:'#0369a1', primary_dark:'#075985', primary_light:'#0284c7', accent_color:'#fb923c', accent_text:'#075985', sidebar_color:'#0c4a6e' },
].map(withDerivedTokens);

let customPresets = <?= json_encode(array_map(function ($p) {
    $p = array_intersect_key($p, array_flip(array_merge(['id', 'name'], \App\Controllers\ThemeController::PRESET_COLOR_FIELDS)));
    $p['id'] = (int) $p['id'];
    return $p;
}, $customPresets ?? [])) ?>;

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
    dashboard:        'Applicant Dashboard',
    appform:           'Applicant Form',
    documents:         'Requirements',
    track:             'Tracking Progress',
    exam:              'Exam Schedule',
    'admission-fee':   'Admission Processing Fee',
    'reservation-fee': 'Reservation Fee',
    faq:               'FAQ & Chat Support',
    followup:          'Follow-Up Messages',
    'tuition-calc':    'Tuition Fee Calculator',
    signin:            'Sign In Page',
    signup:            'Sign Up Page',
    success:           'Registration Success',
};

const PREVIEW_BASE = '<?= base_url('admin/theme/preview') ?>';

function activateTab(tab) {
    document.querySelectorAll('.tab').forEach(b => b.classList.toggle('on', b.dataset.tab === tab));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.toggle('on', p.id === 'tab-' + tab));
}

// ── PAGE SWITCHER ─────────────────────────────────────────
function switchPage(page) {
    const iframe = document.getElementById('preview-iframe');
    iframe.classList.add('fading');
    currentPage = page;
    document.getElementById('pf-page').textContent = PAGE_NAMES[page] || page;
    document.getElementById('page-selector').value = page;
    iframe.src = PREVIEW_BASE + '?page=' + page;
}

// Iframe tells us (via postMessage) which page it's actually showing —
// keeps the dropdown/footer in sync when navigation happens by clicking
// inside the preview itself, not just via the dropdown. The fade-out
// starts the moment the iframe tells us it's about to navigate; the
// matching fade-in happens once in the single 'load' listener in init(),
// regardless of whether the dropdown or an in-preview click triggered it.
window.addEventListener('message', function (e) {
    if (!e.data) return;

    if (e.data.type === 'preview-navigating') {
        document.getElementById('preview-iframe').classList.add('fading');
        return;
    }

    if (e.data.type !== 'preview-page-changed') return;
    const page = e.data.page;
    if (!page || page === currentPage) return;
    currentPage = page;
    document.getElementById('pf-page').textContent = PAGE_NAMES[page] || page;
    const selector = document.getElementById('page-selector');
    if (selector) selector.value = page;
});

// ── INIT ──────────────────────────────────────────────────
function init() {
    buildPresets(); buildFonts(); buildRadii(); buildColorPreview();
    updateFontPreview();

    if (state.preset && state.preset.startsWith('custom-')) {
        const cp = customPresets.find(x => 'custom-' + x.id === state.preset);
        if (cp) document.getElementById('pf-preset-name').textContent = cp.name;
    }

    // Single persistent handler for every iframe load — fires whether the
    // navigation was triggered by the dropdown (switchPage) or by clicking
    // a link inside the preview itself, so the fade transition and the
    // "send current unsaved theme" sync always stay consistent either way.
    const iframe = document.getElementById('preview-iframe');
    iframe.addEventListener('load', function() {
        updatePreview();
        iframe.classList.remove('fading');
    });

    document.querySelectorAll('.tab').forEach(btn => {
        btn.addEventListener('click', () => activateTab(btn.dataset.tab));
    });

    // After a Save & Apply reload, land back on the tab/preview page the
    // admin was actually looking at instead of resetting to Branding/Dashboard.
    const restore = sessionStorage.getItem('akademus_theme_restore');
    if (restore) {
        sessionStorage.removeItem('akademus_theme_restore');
        try {
            const { tab, page } = JSON.parse(restore);
            if (tab && tab !== 'branding') activateTab(tab);
            if (page && page !== 'dashboard') switchPage(page);
        } catch (e) { /* ignore malformed state */ }
    }

    const HEX_RE = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i;

    PRESET_COLOR_KEYS.forEach(key => {
        const inp = document.getElementById('inp_' + key);
        const hex = document.getElementById('hex_' + key);
        if (!inp || !hex) return;

        // Native color-swatch picker
        inp.addEventListener('input', e => applyColorValue(key, e.target.value, true));

        // Typed/pasted hex — applies live once it's a valid #rgb or #rrggbb
        hex.addEventListener('input', e => {
            let val = e.target.value.trim();
            if (val && !val.startsWith('#')) val = '#' + val;
            if (HEX_RE.test(val)) applyColorValue(key, val, false);
        });
        // Normalize/restore on blur so an incomplete value doesn't linger
        hex.addEventListener('blur', () => { hex.value = state[key]; });
        hex.addEventListener('keydown', e => { if (e.key === 'Enter') hex.blur(); });
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

    document.getElementById('logo_file').addEventListener('change', async e => {

        const file = e.target.files[0];

        if (!file) return;

        const formData = new FormData();

        formData.append(CSRF_NAME, CSRF_HASH);
        formData.append('logo_file', file);

        try {

            const response = await fetch('<?= base_url('admin/theme/upload-logo') ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {

                state.logo_url = data.logo_url;

                document.getElementById('logo_url').value = data.logo_path;

                const uploadZone = document.getElementById('upload-zone');
                uploadZone.innerHTML = '';

                const img = document.createElement('img');
                img.src = data.logo_url;
                img.style.cssText = 'width:48px;height:48px;object-fit:contain;border-radius:8px;margin-bottom:4px;';
                uploadZone.appendChild(img);

                const label = document.createElement('div');
                label.style.cssText = 'font-size:10px;color:#5a6a85;';
                label.textContent = 'Click to change';
                uploadZone.appendChild(label);

                updatePreview();
            }

        } catch (err) {
            alert('Upload failed');
        }

    });
}

// ── MASTER UPDATE (postMessage to iframe) ─────────────────
function updatePreview() {
    const iframe = document.getElementById('preview-iframe');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({ type: 'theme-update', theme: state }, '*');
    }
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
    buildCustomPresets();
}

function buildCustomPresets() {
    const section = document.getElementById('custom-preset-section');
    const grid = document.getElementById('custom-preset-grid');
    if (!customPresets.length) {
        section.style.display = 'none';
        grid.innerHTML = '';
        return;
    }
    section.style.display = 'block';
    grid.innerHTML = customPresets.map(p => `
        <div class="preset-card ${state.preset === 'custom-' + p.id ? 'on' : ''}" data-preset="custom-${p.id}" onclick="applyCustomPreset(${p.id})">
            <button type="button" class="preset-delete" title="Delete preset" onclick="event.stopPropagation(); deleteCustomPreset(${p.id})">&times;</button>
            <div class="preset-dots">
                <div class="preset-dot" style="background:${p.primary_dark}"></div>
                <div class="preset-dot" style="background:${p.primary_color}"></div>
                <div class="preset-dot" style="background:${p.accent_color}"></div>
            </div>
            <div class="preset-name">${p.name}</div>
        </div>`).join('');
}

function applyPresetColors(p) {
    Object.assign(state, { ...p });
    PRESET_COLOR_KEYS.forEach(key => {
        if (!state[key]) return;
        const inp = document.getElementById('inp_' + key);
        if (inp) inp.value = state[key];
        const dot = document.getElementById('dot_' + key);
        if (dot) dot.style.background = state[key];
        const hex = document.getElementById('hex_' + key);
        if (hex) hex.value = state[key];
    });
    buildColorPreview(); updatePreview(); updateFontPreview();
}

function applyPreset(id) {
    const p = PRESETS.find(x => x.id === id);
    if (!p) return;
    applyPresetColors(p);
    state.preset = id;
    document.querySelectorAll('.preset-card').forEach(c => c.classList.toggle('on', c.dataset.preset === id));
    document.getElementById('pf-preset-name').textContent = p.name;
}

function applyCustomPreset(id) {
    const p = customPresets.find(x => x.id === id);
    if (!p) return;
    applyPresetColors(p);
    state.preset = 'custom-' + id;
    document.querySelectorAll('.preset-card').forEach(c => c.classList.toggle('on', c.dataset.preset === 'custom-' + id));
    document.getElementById('pf-preset-name').textContent = p.name;
}

async function saveCurrentAsPreset() {
    const name = prompt('Name this preset:');
    if (!name) return;

    const formData = new FormData();
    formData.append(CSRF_NAME, CSRF_HASH);
    formData.append('name', name);
    PRESET_COLOR_KEYS.forEach(k => formData.append(k, state[k] || ''));

    try {
        const res = await fetch('<?= base_url('admin/theme/presets/save') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        });
        const data = await res.json();
        if (data.status === 'success') {
            customPresets.push(data.preset);
            buildCustomPresets();
        } else {
            alert('Could not save preset: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('Could not save preset — network error.');
    }
}

async function deleteCustomPreset(id) {
    if (!confirm('Delete this preset?')) return;
    try {
        await fetch('<?= base_url('admin/theme/presets/delete') ?>/' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: (() => { const fd = new FormData(); fd.append(CSRF_NAME, CSRF_HASH); return fd; })(),
        });
        customPresets = customPresets.filter(p => p.id !== id);
        buildCustomPresets();
    } catch (e) {
        alert('Could not delete preset — network error.');
    }
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

// Single source of truth for setting a color field — used by the native
// swatch picker, the typeable/pasteable hex input, and preset application.
function applyColorValue(key, val, syncHexInput) {
    state[key] = val;
    state.preset = null;

    const dot = document.getElementById('dot_' + key);
    if (dot) dot.style.background = val;

    const inp = document.getElementById('inp_' + key);
    if (inp && inp.value !== val) inp.value = val;

    if (syncHexInput) {
        const hex = document.getElementById('hex_' + key);
        if (hex) hex.value = val;
    }

    buildColorPreview(); updatePreview(); updateFontPreview();
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

    const fields = [...PRESET_COLOR_KEYS,
                    'font_family','border_radius','app_name','logo_text',
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

            // Remember where the admin was (active tab + previewed page) so the
            // reload below lands back in the same spot instead of resetting to
            // Branding/Dashboard — the reload itself is what makes the saved
            // theme actually show up in the admin's own sidebar/branding, which
            // (unlike the live preview) was only ever rendered once on page load.
            const activeTab = document.querySelector('.tab.on');
            sessionStorage.setItem('akademus_theme_restore', JSON.stringify({
                tab: activeTab ? activeTab.dataset.tab : 'branding',
                page: currentPage,
            }));

            setTimeout(() => window.location.reload(), 700);
            return;
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
    applyPreset('classic-blue');
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