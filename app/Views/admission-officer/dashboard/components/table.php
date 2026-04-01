<div class="recent-applicants-table__card card">
    <div class="recent-applicants-table__loading" id="recent-applicants-table-loading" hidden>
        Loading...
    </div>

    <table class="recent-applicants-table__table">
        <thead class="recent-applicants-table__head">
            <tr class="recent-applicants-table__row--head">
                <th class="recent-applicants-table__cell">Applicant Name</th>
                <th class="recent-applicants-table__cell">Email</th>
                <th class="recent-applicants-table__cell">Applicant #</th>
                <th class="recent-applicants-table__cell">Applicant Type</th>
                <th class="recent-applicants-table__cell">Program</th>
                <th class="recent-applicants-table__cell">Status</th>
                <th class="recent-applicants-table__cell">Date Added</th>
                <th class="recent-applicants-table__cell recent-applicants-table__cell--actions-header">Action</th>
            </tr>
        </thead>

        <tbody class="recent-applicants-table__body" id="recent-applicants-table-body">
            <?php if (empty($recent_applicants)): ?>
                <tr class="recent-applicants-table__row">
                    <td class="recent-applicants-table__cell" colspan="8" style="text-align:center;">
                        No requirements found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recent_applicants as $row): ?>

                    <tr class="recent-applicants-table__row">

                        <!-- APPLICANT NAME -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['applicant_name'] ?? '-') ?>
                        </td>

                        <!-- EMAIL -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['email'] ?? '-') ?>
                        </td>

                        <!-- APPLICANT NUMBER -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['applicant_number'] ?? '-') ?>
                        </td>

                        <!-- APPLICANT TYPE -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['applicant_type'] ?? '-') ?>
                        </td>

                        <!-- PROGRAM -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['program'] ?? '-') ?>
                        </td>

                        <!-- STATUS -->
                        <td class="recent-applicants-table__cell">
                            <span class="status-badge status-badge--<?= esc($row['status']) ?? '' ?>">
                                <?= esc(ucwords(str_replace('_', ' ', $row['status']))) ?>
                            </span>
                        </td>

                        <!-- DATE ADDED -->
                        <td class="recent-applicants-table__cell">
                            <?= esc($row['date_added'] ?? '-') ?>
                        </td>

                        <!-- ACTION -->
                        <td class="recent-applicants-table__cell recent-applicants-table__cell--actions">
                            <img class="recent-applicants-table__cell--actions-img" src="<?= base_url('assets/images/icons/view.svg') ?>">
                            <img class="recent-applicants-table__cell--actions-img" src="<?= base_url('assets/images/icons/pen-edit.svg') ?>">
                            <img class="recent-applicants-table__cell--actions-img" src="<?= base_url('assets/images/icons/trash.svg') ?>">
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>