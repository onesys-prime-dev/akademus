<?php
if (! session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme   = $theme   ?? [];
$fees    = $fees    ?? [];
$methods = $methods ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Payment Settings</title>
    <?= theme_favicon_tags() ?>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
            --primary-soft: <?= esc($theme['primary_soft']  ?? '#e8f0ff') ?>;
            --accent:       <?= esc($theme['accent_color']  ?? '#ffb200') ?>;
            --radius:       <?= esc($theme['border_radius'] ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']  ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; }
        .shell { display: flex; height: 100vh; overflow: hidden; }
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        /* Topbar */
        .topbar { height: 52px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; flex-shrink: 0; }
        .topbar-title { font-size: 20px; font-weight: 800; color: #0d1e38; letter-spacing: -.4px; }
        .topbar-admin { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #5a6a85; }
        .topbar-admin-dot { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; }

        /* Scroll area */
        .content-wrap { flex: 1; overflow-y: auto; padding: 28px 32px; }

        /* Flash */
        .flash { margin-bottom: 20px; padding: 12px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 600; max-width: 820px; display: flex; align-items: center; gap: 10px; }
        .flash-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .flash-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Page header */
        .page-head { max-width: 820px; margin-bottom: 22px; }
        .page-head h1 { font-size: 22px; font-weight: 800; color: #0d1e38; letter-spacing: -.3px; }
        .page-head p  { font-size: 13px; color: #7a8aaa; margin-top: 4px; }

        /* Card */
        .card { background: #fff; border-radius: var(--radius); border: 1px solid #e2e8f0; max-width: 820px; overflow: hidden; margin-bottom: 28px; }
        .card-header { padding: 16px 22px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .card-header-left h4 { font-size: 14px; font-weight: 700; color: #0d1e38; margin: 0; }
        .card-header-left p  { font-size: 12px; color: #7a8aaa; margin: 3px 0 0; }

        /* Tables shared */
        .settings-table { width: 100%; border-collapse: collapse; }
        .settings-table thead th {
            padding: 11px 20px;
            font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .settings-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .settings-table tbody tr:last-child { border-bottom: none; }
        .settings-table tbody td { padding: 14px 20px; vertical-align: middle; }

        /* Row label */
        .row-name { font-size: 13px; font-weight: 700; color: #1e293b; }
        .row-sub  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        /* Status badge */
        .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .status-badge--active   { background: #dcfce7; color: #15803d; }
        .status-badge--inactive { background: #f1f5f9; color: #64748b; }

        /* Amount input */
        .fee-input-wrap { display: flex; align-items: center; gap: 6px; max-width: 160px; }
        .fee-input-prefix { font-size: 13px; font-weight: 700; color: #64748b; }
        .fee-input {
            width: 100%;
            border: 1px solid #cbd5e0; 
            border-radius: 7px;
            padding: 8px 10px;
            font-size: 14px; 
            font-weight: 700; 
            color: #1e293b;
            font-family: ui-monospace, monospace;
            transition: border-color .15s, box-shadow .15s;

            appearance: textfield;
            -moz-appearance: textfield;
        }
        .fee-input::-webkit-outer-spin-button,
        .fee-input::-webkit-inner-spin-button { -webkit-appearance: none; }
        .fee-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,64,166,.1); }

        /* Text input (method fields) */
        .text-input {
            width: 100%;
            border: 1px solid #cbd5e0; border-radius: 7px;
            padding: 7px 10px;
            font-size: 13px; font-weight: 600; color: #1e293b;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }
        .text-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,64,166,.1); }
        .text-input[readonly] { background: #f8fafc; color: #94a3b8; cursor: default; }

        /* Method row columns */
        .method-name-col { min-width: 130px; max-width: 170px; }
        .method-acc-name-col { min-width: 150px; max-width: 200px; }
        .method-acc-num-col  { min-width: 150px; max-width: 200px; }

        /* Toggle switch */
        .toggle-wrap { display: inline-flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; }
        .toggle-track {
            width: 40px; height: 22px; border-radius: 999px; background: #cbd5e0;
            position: relative; transition: background .2s; flex-shrink: 0;
        }
        .toggle-track.on { background: #22c55e; }
        .toggle-thumb {
            position: absolute; top: 3px; left: 3px;
            width: 16px; height: 16px; border-radius: 50%; background: #fff;
            transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.15);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(18px); }
        .toggle-label { font-size: 10px; font-weight: 700; color: #94a3b8; }
        .toggle-track.on + .toggle-label { color: #15803d; }

        /* Save / action buttons */
        .btn-save {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 7px;
            font-size: 12px; font-weight: 700;
            background: var(--primary); color: #fff;
            border: none; cursor: pointer;
            transition: opacity .15s, transform .1s, background .15s;
            white-space: nowrap;
        }
        .btn-save:hover:not(:disabled) { opacity: .88; }
        .btn-save:active { transform: scale(.97); }
        .btn-save:disabled { opacity: .45; cursor: not-allowed; }
        .btn-save.saved { background: #22c55e; }

        .btn-add {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 16px; border-radius: 7px;
            font-size: 12px; font-weight: 700;
            background: var(--primary-soft, #e8f0ff); color: var(--primary);
            border: 1.5px solid var(--primary-soft, #e8f0ff);
            cursor: pointer; transition: background .15s, border-color .15s;
        }
        .btn-add:hover { background: #dce8ff; border-color: #dce8ff; }

        /* Saved confirmation text */
        .saved-msg { font-size: 11px; font-weight: 700; color: #15803d; margin-top: 3px; display: none; white-space: nowrap; }

        /* Error text */
        .row-err { font-size: 11px; color: #ef4444; margin-top: 3px; display: none; }

        /* Add-method form row */
        .add-row { display: none; border-top: 2px dashed #e2e8f0; }
        .add-row.open { display: table-row; }
        .add-row td { padding: 16px 20px; background: #f8fafc; }
        .add-form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
        .add-form-field label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 5px; }
        .add-form-field .text-input { background: #fff; }
        .btn-cancel {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 14px; border-radius: 7px;
            font-size: 12px; font-weight: 700;
            background: transparent; color: #64748b;
            border: 1.5px solid #e2e8f0;
            cursor: pointer; transition: border-color .15s, color .15s;
        }
        .btn-cancel:hover { border-color: #94a3b8; color: #374151; }

        .add-err { font-size: 12px; color: #ef4444; padding: 6px 0 0; display: none; }
    </style>
</head>
<body>
<div class="shell">

    <?= view('admin/_sidebar', ['theme' => $theme]) ?>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">Payment Settings</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-wrap">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="flash flash-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
            <?php endif; ?>

            <div class="page-head">
                <h1>Payment Settings</h1>
                <p>Manage fee amounts and configure available payment methods for applicants.</p>
            </div>

            <!-- ── Card 1: Fee Amounts ── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-header-left">
                        <h4>Fee Amounts</h4>
                        <p>Set required amounts for each payment type. Changes apply immediately.</p>
                    </div>
                </div>
                <table class="settings-table">
                    <thead>
                        <tr>
                            <th style="width:38%">Fee Type</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th style="text-align:right;padding-right:24px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $feeDescs = [
                        'ADMISSION_FEE'   => 'Paid at the start of the application process.',
                        'RESERVATION_FEE' => 'Paid upon accepting an admission offer.',
                    ];
                    foreach ($fees as $fee):
                        $isActive = (bool)($fee['is_active'] ?? true);
                        $feeId    = (int)$fee['payment_fee_type_id'];
                        $desc     = $feeDescs[$fee['code'] ?? ''] ?? '';
                    ?>
                    <tr>
                        <td>
                            <div class="row-name"><?= esc($fee['name']) ?></div>
                            <?php if ($desc): ?><div class="row-sub"><?= esc($desc) ?></div><?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?= $isActive ? 'status-badge--active' : 'status-badge--inactive' ?>">
                                <svg width="7" height="7" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                <?= $isActive ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <form id="fee-form-<?= $feeId ?>"
                                  onsubmit="saveFee(event,<?= $feeId ?>)">
                                <div class="fee-input-wrap">
                                    <span class="fee-input-prefix">₱</span>
                                    <input type="number" step="0.01" min="0" name="amount"
                                           class="fee-input"
                                           value="<?= esc(number_format((float)$fee['amount'], 2, '.', '')) ?>">
                                </div>
                            </form>
                        </td>
                        <td style="text-align:right;padding-right:20px;">
                            <button type="submit" form="fee-form-<?= $feeId ?>"
                                    class="btn-save" id="fee-btn-<?= $feeId ?>">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Save
                            </button>
                            <div class="saved-msg" id="fee-saved-<?= $feeId ?>">Saved!</div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ── Card 2: Payment Methods ── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-header-left">
                        <h4>Payment Methods</h4>
                        <p>Toggle, edit account details, or add new payment options for applicants.</p>
                    </div>
                    <button class="btn-add" onclick="toggleAddRow()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Method
                    </button>
                </div>
                <table class="settings-table" id="methods-table">
                    <thead>
                        <tr>
                            <th style="width:20%">Method Name</th>
                            <th style="width:22%">Account Name</th>
                            <th style="width:22%">Account Number</th>
                            <th style="width:10%;">Active</th>
                            <th style="text-align:right;padding-right:24px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="methods-body">
                    <?php foreach ($methods as $m):
                        $mid  = (int)$m['payment_method_id'];
                        $amid = (int)($m['payment_method_account_id'] ?? 0);
                        $on   = (bool)$m['is_active'];
                    ?>
                    <tr id="method-row-<?= $mid ?>">
                        <td class="method-name-col">
                            <input class="text-input" type="text"
                                   id="m-name-<?= $mid ?>"
                                   value="<?= esc($m['name']) ?>"
                                   placeholder="Method name">
                        </td>
                        <td class="method-acc-name-col">
                            <input class="text-input" type="text"
                                   id="m-aname-<?= $mid ?>"
                                   value="<?= esc($m['account_name'] ?? '') ?>"
                                   placeholder="e.g. School Cashier">
                        </td>
                        <td class="method-acc-num-col">
                            <input class="text-input" type="text"
                                   id="m-anum-<?= $mid ?>"
                                   value="<?= esc($m['account_number'] ?? '') ?>"
                                   placeholder="e.g. 09XX-XXX-XXXX">
                        </td>
                        <td>
                            <div class="toggle-wrap" onclick="toggleMethod(<?= $mid ?>)" id="toggle-wrap-<?= $mid ?>">
                                <div class="toggle-track <?= $on ? 'on' : '' ?>" id="toggle-track-<?= $mid ?>">
                                    <div class="toggle-thumb"></div>
                                </div>
                                <span class="toggle-label" id="toggle-label-<?= $mid ?>"><?= $on ? 'ON' : 'OFF' ?></span>
                            </div>
                        </td>
                        <td style="text-align:right;padding-right:20px;">
                            <button class="btn-save"
                                    id="m-btn-<?= $mid ?>"
                                    onclick="saveMethod(<?= $mid ?>,<?= $amid ?>)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Save
                            </button>
                            <div class="saved-msg" id="m-saved-<?= $mid ?>">Saved!</div>
                            <div class="row-err"   id="m-err-<?= $mid ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Add-method form row -->
                    <tr class="add-row" id="add-row">
                        <td colspan="5">
                            <div class="add-form-grid">
                                <div class="add-form-field">
                                    <label>Method Name <span style="color:#ef4444">*</span></label>
                                    <input class="text-input" type="text" id="add-name" placeholder="e.g. UnionBank">
                                </div>
                                <div class="add-form-field">
                                    <label>Account Name</label>
                                    <input class="text-input" type="text" id="add-aname" placeholder="e.g. School Cashier">
                                </div>
                                <div class="add-form-field">
                                    <label>Account Number</label>
                                    <input class="text-input" type="text" id="add-anum" placeholder="e.g. 109-XXXXXX-X">
                                </div>
                                <div class="add-form-field" style="display:flex;gap:8px;align-items:flex-end;">
                                    <button class="btn-save" style="flex:1;" id="add-save-btn" onclick="addMethod()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Add
                                    </button>
                                    <button class="btn-cancel" onclick="toggleAddRow()">Cancel</button>
                                </div>
                            </div>
                            <div class="add-err" id="add-err"></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div><!-- /.content-wrap -->
    </div><!-- /.main-area -->
</div><!-- /.shell -->

<script>
const BASE      = '<?= base_url() ?>';
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// ── Fee save ──────────────────────────────────────────────────────
function saveFee(e, feeId) {
    e.preventDefault();
    const form = document.getElementById('fee-form-' + feeId);
    const btn  = document.getElementById('fee-btn-'  + feeId);
    const msg  = document.getElementById('fee-saved-' + feeId);
    _ajaxSave(BASE + 'admin/payment-fees/update/' + feeId, new FormData(form), btn, msg);
}

// ── Method save ───────────────────────────────────────────────────
function saveMethod(mid, amid) {
    const btn  = document.getElementById('m-btn-'   + mid);
    const msg  = document.getElementById('m-saved-' + mid);
    const err  = document.getElementById('m-err-'   + mid);

    const name  = document.getElementById('m-name-'  + mid)?.value.trim();
    const aname = document.getElementById('m-aname-' + mid)?.value.trim();
    const anum  = document.getElementById('m-anum-'  + mid)?.value.trim();

    if (!name) { showErr(err, 'Method name is required.'); return; }
    hideErr(err);

    const fd = new FormData();
    fd.append('name',           name);
    fd.append('account_name',   aname);
    fd.append('account_number', anum);
    fd.append('account_id',     amid);

    _ajaxSave(BASE + 'admin/payment-methods/update/' + mid, fd, btn, msg, err);
}

// ── Toggle ────────────────────────────────────────────────────────
function toggleMethod(mid) {
    const track = document.getElementById('toggle-track-' + mid);
    const label = document.getElementById('toggle-label-' + mid);
    const wrap  = document.getElementById('toggle-wrap-'  + mid);
    wrap.style.pointerEvents = 'none';

    const tfd = new FormData();
    tfd.append(CSRF_NAME, CSRF_HASH);
    fetch(BASE + 'admin/payment-methods/toggle/' + mid, { method: 'POST', body: tfd })
        .then(r => r.json())
        .then(data => {
            wrap.style.pointerEvents = '';
            if (data.success) {
                const on = data.is_active === 1;
                track.classList.toggle('on', on);
                label.textContent = on ? 'ON' : 'OFF';
                label.style.color = on ? '#15803d' : '';
            }
        })
        .catch(() => { wrap.style.pointerEvents = ''; });
}

// ── Add method ────────────────────────────────────────────────────
function toggleAddRow() {
    const row = document.getElementById('add-row');
    const open = row.classList.toggle('open');
    if (open) {
        document.getElementById('add-name').focus();
    } else {
        // Clear fields
        ['add-name','add-aname','add-anum'].forEach(id => document.getElementById(id).value = '');
        hideErr(document.getElementById('add-err'));
    }
}

function addMethod() {
    const name  = document.getElementById('add-name').value.trim();
    const aname = document.getElementById('add-aname').value.trim();
    const anum  = document.getElementById('add-anum').value.trim();
    const err   = document.getElementById('add-err');
    const btn   = document.getElementById('add-save-btn');

    if (!name) { showErr(err, 'Method name is required.'); return; }
    hideErr(err);

    btn.disabled = true;
    btn.textContent = 'Adding…';

    const fd = new FormData();
    fd.append('name',           name);
    fd.append('account_name',   aname);
    fd.append('account_number', anum);

    fetch(BASE + 'admin/payment-methods/add', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add';
            if (data.success) {
                appendMethodRow(data.method);
                toggleAddRow();
            } else {
                showErr(err, data.message || 'Failed to add.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add';
            showErr(err, 'Network error. Please try again.');
        });
}

function appendMethodRow(m) {
    const mid  = m.payment_method_id;
    const amid = m.payment_method_account_id ?? 0;
    const on   = m.is_active == 1;

    const tr = document.createElement('tr');
    tr.id = 'method-row-' + mid;
    tr.innerHTML = `
        <td class="method-name-col">
            <input class="text-input" type="text" id="m-name-${mid}" value="${esc(m.name)}" placeholder="Method name">
        </td>
        <td class="method-acc-name-col">
            <input class="text-input" type="text" id="m-aname-${mid}" value="${esc(m.account_name ?? '')}" placeholder="e.g. School Cashier">
        </td>
        <td class="method-acc-num-col">
            <input class="text-input" type="text" id="m-anum-${mid}" value="${esc(m.account_number ?? '')}" placeholder="e.g. 09XX-XXX-XXXX">
        </td>
        <td>
            <div class="toggle-wrap" onclick="toggleMethod(${mid})" id="toggle-wrap-${mid}">
                <div class="toggle-track ${on ? 'on' : ''}" id="toggle-track-${mid}"><div class="toggle-thumb"></div></div>
                <span class="toggle-label" id="toggle-label-${mid}" style="${on ? 'color:#15803d' : ''}">${on ? 'ON' : 'OFF'}</span>
            </div>
        </td>
        <td style="text-align:right;padding-right:20px;">
            <button class="btn-save" id="m-btn-${mid}" onclick="saveMethod(${mid},${amid})">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Save
            </button>
            <div class="saved-msg" id="m-saved-${mid}">Saved!</div>
            <div class="row-err" id="m-err-${mid}"></div>
        </td>
    `;

    // Insert before the add-row
    const addRow = document.getElementById('add-row');
    addRow.parentNode.insertBefore(tr, addRow);
}

// ── Helpers ───────────────────────────────────────────────────────
function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showErr(el, msg) { if (el) { el.textContent = msg; el.style.display = 'block'; } }
function hideErr(el)       { if (el) { el.textContent = '';  el.style.display = 'none';  } }

function _ajaxSave(url, fd, btn, msgEl, errEl) {
    const origHtml = btn.innerHTML;
    btn.disabled = true;

    fd.append(CSRF_NAME, CSRF_HASH);
    fetch(url, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Saved';
                btn.classList.add('saved');
                if (msgEl) { msgEl.style.display = 'block'; }
                if (errEl) hideErr(errEl);
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    btn.classList.remove('saved');
                    if (msgEl) msgEl.style.display = 'none';
                }, 2200);
            } else {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                if (errEl) showErr(errEl, data.message || 'Failed to save.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (errEl) showErr(errEl, 'Network error. Please try again.');
        });
}
</script>
</body>
</html>
