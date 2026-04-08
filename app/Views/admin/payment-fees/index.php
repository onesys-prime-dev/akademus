<h2>Applicant Payments</h2>

<table border="1" cellpadding="10" width="100%">
<thead>
<tr>
    <th>Applicant #</th>
    <th>Fee Type</th>
    <th>Amount</th>
    <th>Method</th>
    <th>Reference</th>
    <th>Proof</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

<?php foreach ($payments as $p): ?>
<tr>
    <td><?= esc($p['applicant_number']) ?></td>
    <td><?= esc($p['fee_name']) ?></td>
    <td>₱<?= number_format($p['amount_paid'],2) ?></td>
    <td><?= esc($p['method_name']) ?></td>
    <td><?= esc($p['reference_num']) ?></td>

    <td>
        <a href="<?= base_url($p['proof_of_payment']) ?>" target="_blank">
            View Proof
        </a>
    </td>

    <td><?= esc($p['payment_status']) ?></td>

    <td>
        <?php if ($p['payment_status'] == 'PENDING'): ?>

        <form method="post" action="<?= base_url('admin/payments/approve/'.$p['payment_id']) ?>" style="display:inline">
            <button type="submit">Approve</button>
        </form>

        <form method="post" action="<?= base_url('admin/payments/reject/'.$p['payment_id']) ?>" style="display:inline">
            <button type="submit">Reject</button>
        </form>

        <?php endif; ?>
    </td>

</tr>
<?php endforeach; ?>

</tbody>
</table>