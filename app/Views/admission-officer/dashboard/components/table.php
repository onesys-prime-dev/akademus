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
            <?php if (empty($rows)): ?>
                <tr class="recent-applicants-table__row">
                    <td class="recent-applicants-table__cell" colspan="8" style="text-align:center;color:#718096;padding:24px 0;">
                        No applicants found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                <?php $rowStatus = $row['status'] ?? ''; ?>
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
                            <span class="status-badge status-badge--<?= esc($rowStatus) ?>">
                                <?= esc(ucwords(str_replace('_', ' ', $rowStatus))) ?>
                            </span>
                        </td>

                        <!-- DATE ADDED -->
                        <td class="recent-applicants-table__cell">
                            <?= $row['date_added'] ? esc(date('M j, Y', strtotime($row['date_added']))) : '-' ?>
                        </td>

                        <!-- ACTION -->
                        <td class="recent-applicants-table__cell recent-applicants-table__cell--actions">
                            <a href="<?= base_url('admission-officer/detailed-applicant/' . (int) $row['applicant_id']) ?>"
                               title="View Applicant">
                                <img class="recent-applicants-table__cell--actions-img"
                                     src="<?= base_url('assets/images/icons/view.svg') ?>">
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    const applicantRow = document.querySelectorAll('.recent-applicants-table__row');

    applicantRow.forEach(row => {
        row.addEventListener('click', (e) => {
            const target = e.target;
            const actionCell = row.querySelector('.recent-applicants-table__cell--actions');
            if (actionCell.contains(target)) {
                // Clicked on the action cell, do nothing here (let the link handle it)
                return;
            }
            // Otherwise, navigate to the applicant's detailed view
            const viewLink = row.querySelector('.recent-applicants-table__cell--actions a');
            if (viewLink) {
                window.location.href = viewLink.href;
            }
        });
    });
</script>