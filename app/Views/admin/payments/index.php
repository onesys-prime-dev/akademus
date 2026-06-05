<?php
if (! session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme    = $theme    ?? [];
$payments = $payments ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Payment Review</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
            --primary-dark:  <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
            --primary-soft:  <?= esc($theme['primary_soft']  ?? '#e8f0ff') ?>;
            --accent:        <?= esc($theme['accent_color']  ?? '#ffb200') ?>;
            --accent-text:   <?= esc($theme['accent_text']   ?? '#002d7a') ?>;
            --radius:        <?= esc($theme['border_radius'] ?? '8px') ?>;
            --font:          '<?= esc($theme['font_family']  ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; }
        .shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── MAIN ── */
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .topbar { height: 52px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; flex-shrink: 0; }
        .topbar-title { font-size: 20px; font-weight: 800; color: #0d1e38; letter-spacing: -.4px; }
        .topbar-admin { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #5a6a85; }
        .topbar-admin-dot { width: 30px; height: 30px; border-radius: 50%; background: var(--primary, #0040a6); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; }

        /* ── CONTENT ── */
        .content-wrap { flex: 1; overflow-y: auto; padding: 28px 32px; }

        /* ── CARDS ── */
        .card { background: #fff; border-radius: var(--radius, 8px); border: 1px solid #e2e8f0; margin-bottom: 28px; overflow: hidden; }
        .card-header { padding: 16px 22px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
        .card-header h4 { font-size: 15px; font-weight: 700; color: #0d1e38; margin: 0; }
        .card-body { padding: 0; }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #f8fafc; }
        th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #7a8aaa; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; color: #2d3a4f; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafbfc; }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .badge-pending    { background: #fff8e1; color: #b45309; }
        .badge-processing { background: #e0f2fe; color: #0369a1; }
        .badge-paid       { background: #dcfce7; color: #15803d; }
        .badge-rejected   { background: #fee2e2; color: #b91c1c; }

        /* ── BUTTONS ── */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: opacity .15s; }
        .btn:hover { opacity: .85; }
        .btn-approve { background: #22c55e; color: #fff; }
        .btn-reject  { background: #ef4444; color: #fff; }

        /* ── EMPTY ROW ── */
        .empty-row td { text-align: center; color: #aab4c4; font-size: 13px; padding: 28px; }

        /* ── FLASH ── */
        .flash { margin-bottom: 20px; padding: 12px 18px; border-radius: var(--radius, 8px); font-size: 13px; font-weight: 600; }
        .flash-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .flash-error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    </style>
</head>
<body>
<div class="shell">

    <?php echo view('admin/_sidebar', ['theme' => $theme]); ?>

    <div class="main-area">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-title">Payment Review</div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrap">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash flash-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <!-- Admission Processing Fee -->
            <div class="card">
                <div class="card-header">
                    <h4>Admission Processing Fee</h4>
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Reference #</th>
                                <th>Amount</th>
                                <th>Proof</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $admissionRows = array_filter($payments, fn($p) => $p['fee_code'] === 'ADMISSION_FEE'); ?>
                            <?php if (empty($admissionRows)): ?>
                                <tr class="empty-row"><td colspan="6">No admission fee payments yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($admissionRows as $p): ?>
                                <tr>
                                    <td><?= esc($p['firstname'] . ' ' . $p['lastname']) ?></td>
                                    <td><?= esc($p['reference_num']) ?></td>
                                    <td>₱<?= number_format($p['amount_paid'], 2) ?></td>
                                    <td>
                                        <?php if (! empty($p['proof_of_payment'])): ?>
                                            <a href="<?= base_url($p['proof_of_payment']) ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background:#e8f0ff;color:var(--primary);">View</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-<?= strtolower($p['payment_status']) ?>"><?= esc($p['payment_status']) ?></span></td>
                                    <td>
                                        <?php if ($p['payment_status'] === 'PENDING'): ?>
                                            <form method="post" action="<?= base_url('admin/payments/approve/' . $p['payment_id']) ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-approve">Approve</button>
                                            </form>
                                            <form method="post" action="<?= base_url('admin/payments/reject/' . $p['payment_id']) ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-reject">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reservation Fee -->
            <div class="card">
                <div class="card-header">
                    <h4>Reservation Fee</h4>
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Reference #</th>
                                <th>Amount</th>
                                <th>Proof</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $reservationRows = array_filter($payments, fn($p) => $p['fee_code'] === 'RESERVATION_FEE'); ?>
                            <?php if (empty($reservationRows)): ?>
                                <tr class="empty-row"><td colspan="6">No reservation fee payments yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reservationRows as $p): ?>
                                <tr>
                                    <td><?= esc($p['firstname'] . ' ' . $p['lastname']) ?></td>
                                    <td><?= esc($p['reference_num']) ?></td>
                                    <td>₱<?= number_format($p['amount_paid'], 2) ?></td>
                                    <td>
                                        <?php if (! empty($p['proof_of_payment'])): ?>
                                            <a href="<?= base_url($p['proof_of_payment']) ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background:#e8f0ff;color:var(--primary);">View</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-<?= strtolower($p['payment_status']) ?>"><?= esc($p['payment_status']) ?></span></td>
                                    <td>
                                        <?php if ($p['payment_status'] === 'PENDING'): ?>
                                            <form method="post" action="<?= base_url('admin/payments/approve/' . $p['payment_id']) ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-approve">Approve</button>
                                            </form>
                                            <form method="post" action="<?= base_url('admin/payments/reject/' . $p['payment_id']) ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-reject">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /content-wrap -->
    </div><!-- /main-area -->
</div><!-- /shell -->
</body>
</html>
