<div class="admin-page">

    <h2 style="margin-bottom:20px;">Payment Review</h2>

    <!-- =============================== -->
    <!-- ADMISSION PROCESSING FEE -->
    <!-- =============================== -->
    <div class="card" style="margin-bottom:30px;">
        <div class="card-header">
            <h4>Admission Processing Fee</h4>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Reference #</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($payments as $p): ?>
                <?php if ($p['fee_code'] === 'ADMISSION_FEE'): ?>

                    <tr>
                        <td>
                            <?= esc($p['firstname'].' '.$p['lastname']) ?>
                        </td>

                        <td>
                            <?= esc($p['reference_num']) ?>
                        </td>

                        <td>
                            ₱<?= number_format($p['amount_paid'], 2) ?>
                        </td>

                        <td>
                            <?php if (!empty($p['proof_of_payment'])): ?>
                                <a href="<?= base_url('uploads/payments/'.$p['proof_of_payment']) ?>" target="_blank">
                                    View
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge badge-<?= strtolower($p['payment_status']) ?>">
                                <?= esc($p['payment_status']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($p['payment_status'] === 'PENDING'): ?>

                                <form method="post" action="<?= base_url('admin/payments/approve/'.$p['payment_id']) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="post" action="<?= base_url('admin/payments/reject/'.$p['payment_id']) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-danger btn-sm">Reject</button>
                                </form>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endif; ?>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>


    <!-- =============================== -->
    <!-- RESERVATION FEE -->
    <!-- =============================== -->
    <div class="card">
        <div class="card-header">
            <h4>Reservation Fee</h4>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Reference #</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($payments as $p): ?>
                <?php if ($p['fee_code'] === 'RESERVATION_FEE'): ?>

                    <tr>
                        <td>
                            <?= esc($p['firstname'].' '.$p['lastname']) ?>
                        </td>

                        <td>
                            <?= esc($p['reference_num']) ?>
                        </td>

                        <td>
                            ₱<?= number_format($p['amount_paid'], 2) ?>
                        </td>

                        <td>
                            <?php if (!empty($p['proof_of_payment'])): ?>
                                <a href="<?= base_url('uploads/payments/'.$p['proof_of_payment']) ?>" target="_blank">
                                    View
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge badge-<?= strtolower($p['payment_status']) ?>">
                                <?= esc($p['payment_status']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($p['payment_status'] === 'PENDING'): ?>

                                <form method="post" action="<?= base_url('admin/payments/approve/'.$p['payment_id']) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="post" action="<?= base_url('admin/payments/reject/'.$p['payment_id']) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-danger btn-sm">Reject</button>
                                </form>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endif; ?>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>