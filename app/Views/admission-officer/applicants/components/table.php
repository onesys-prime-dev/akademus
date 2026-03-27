<div class="applicants-table card">
    <div class="applicants-table__loading" id="table-loading" hidden>
        Loading...
    </div>

    <table class="applicants-table__table">
        <thead class="applicants-table__head">
            <tr class="applicants-table__row applicants-table__row--head">
                <th class="applicants-table__cell applicants-table__cell--checkbox"></th>
                <th class="applicants-table__cell">Applicant Name</th>
                <th class="applicants-table__cell">Email</th>
                <th class="applicants-table__cell">Applicant ID</th>
                <th class="applicants-table__cell">Applicant Type</th>
                <th class="applicants-table__cell">Program</th>
                <th class="applicants-table__cell">Status</th>
                <th class="applicants-table__cell">Date Added</th>
                <th class="applicants-table__cell applicants-table__cell--actions-header">Actions</th>
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
                                <button class="ao-view-doc-btn"
                                    data-doc-url="<?= esc(base_url('admission-officer/applicants/document/' . $applicantId . '/' . $row['program_requirement_id'])) ?>"
                                    data-doc-name="<?= esc($row['soft_copy']) ?>"
                                    style="background:none; border:none; cursor:pointer; color:#1a6fdb; font-size:13px; padding:0; text-decoration:underline;">
                                    View
                                </button>
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
                                <button class="ao-view-doc-btn"
                                    data-doc-url="<?= esc(base_url('admission-officer/applicants/document/' . $applicantId . '/' . $row['program_requirement_id'])) ?>"
                                    data-doc-name="<?= esc($row['soft_copy']) ?>"
                                    style="background:none; border:none; cursor:pointer; padding:0;">
                                    <img class="application-table__cell--actions-img" src="<?= base_url('assets/images/icons/view.svg') ?>">
                                </button>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?= $this->include('admission-officer/components/pagination') ?>
</div>