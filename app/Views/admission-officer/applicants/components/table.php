<div class="application-table__card card">
    <div class="application-table__loading" id="application-table-loading" hidden>
        Loading...
    </div>

    <table class="application-table__table">
        <thead class="application-table__head">
            <tr class="application-table__row--head">
                <th class="application-table__cell">Document Name</th>
                <th class="application-table__cell">Category</th>
                <th class="application-table__cell">Required</th>
                <th class="application-table__cell">Hard Copy</th>
                <th class="application-table__cell">Soft Copy</th>
                <th class="application-table__cell">Upload Date</th>
                <th class="application-table__cell">Status</th>
                <th class="application-table__cell application-table__cell--actions-header">Action</th>
            </tr>
        </thead>

        <tbody class="application-table__body" id="application-table-body">
            <?php if (empty($rows)): ?>
                <tr class="application-table__row">
                    <td class="application-table__cell" colspan="8" style="text-align:center;">
                        No requirements found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                        $status = $row['status'] ?? null;
                        $status = $status ?: 'pending'; // fallback for NULL

                        $isRequired = (int) ($row['required'] ?? 0) === 1;
                    ?>
                    <tr class="application-table__row">

                        <!-- DOCUMENT NAME -->
                        <td class="application-table__cell">
                            <?= esc($row['document_name'] ?? '-') ?>
                        </td>

                        <!-- CATEGORY -->
                        <td class="application-table__cell">
                            <?= esc($row['category'] ?? '-') ?>
                        </td>

                        <!-- REQUIRED -->
                        <td class="application-table__cell">
                            <?= $isRequired ? '<span class="badge badge--required">Yes</span>' : 'No' ?>
                        </td>

                        <!-- HARD COPY (placeholder for now) -->
                        <td class="application-table__cell">
                            -
                        </td>

                        <!-- SOFT COPY -->
                        <td class="application-table__cell">
                            <?php if (!empty($row['soft_copy'])): ?>
                                <a href="<?= base_url('uploads/' . $row['soft_copy']) ?>" target="_blank">
                                    View
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </td>

                        <!-- UPLOAD DATE -->
                        <td class="application-table__cell">
                            <?= !empty($row['upload_date']) 
                                ? esc(date('Y-m-d', strtotime($row['upload_date']))) 
                                : '-' ?>
                        </td>

                        <!-- STATUS -->
                        <td class="application-table__cell">
                            <span class="status-badge status-badge--<?= esc($status) ?? '' ?>">
                                <?= esc(ucwords(str_replace('_', ' ', $status))) ?>
                            </span>
                        </td>

                        <!-- ACTION -->
                        <td class="application-table__cell application-table__cell--actions">
                            <?php if (!empty($row['soft_copy'])): ?>
                                <img class="application-table__cell--actions-img" src="<?= base_url('assets/images/icons/view.svg') ?>">
                                <img class="application-table__cell--actions-img" src="<?= base_url('assets/images/icons/pen-edit.svg') ?>">
                                <img class="application-table__cell--actions-img" src="<?= base_url('assets/images/icons/trash.svg') ?>">
                            <?php else: ?>
                                
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?= $this->include('admission-officer/components/pagination') ?>
</div>