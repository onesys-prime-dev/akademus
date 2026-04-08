<h2>Payment Fee Settings</h2>

<table class="table">
    <thead>
        <tr>
            <th>Fee Type</th>
            <th>Amount</th>
            <th>Active</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($fees as $fee): ?>
        <tr>
            <form method="post" action="<?= base_url('admin/payment-fees/update/'.$fee['payment_fee_type_id']) ?>">
                <td><?= esc($fee['name']) ?></td>
                <td>
                    <input type="number" step="0.01" name="amount"
                           value="<?= esc($fee['amount']) ?>">
                </td>
                <td><?= $fee['is_active'] ? 'Active' : 'Inactive' ?></td>
                <td>
                    <button type="submit">Save</button>
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>