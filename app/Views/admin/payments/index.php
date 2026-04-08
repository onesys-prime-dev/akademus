<h2>Applicant Payments</h2>

<table class="table">
<thead>
<tr>
<th>Applicant</th>
<th>Fee Type</th>
<th>Amount</th>
<th>Proof</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php foreach($payments as $p): ?>
<tr>
<td><?= esc($p['firstname'].' '.$p['lastname']) ?></td>
<td><?= esc($p['fee_name']) ?></td>
<td>₱<?= number_format($p['amount_paid'],2) ?></td>

<td>
<a href="<?= base_url($p['proof_of_payment']) ?>" target="_blank">
View
</a>
</td>

<td><?= esc($p['payment_status']) ?></td>

<td>
<form method="post" action="<?= base_url('admin/payments/approve/'.$p['payment_id']) ?>" style="display:inline;">
<button>Approve</button>
</form>

<form method="post" action="<?= base_url('admin/payments/reject/'.$p['payment_id']) ?>" style="display:inline;">
<button>Reject</button>
</form>
</td>

</tr>
<?php endforeach; ?>
</tbody>
</table>