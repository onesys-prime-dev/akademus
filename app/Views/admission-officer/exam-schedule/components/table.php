<div class="exam-schedule-table__card card">
    <div class="exam-schedule-table__loading" id="exam-schedule-table-loading" hidden>
        Loading...
    </div>

    <table class="exam-schedule-table__table">
        <thead class="exam-schedule-table__head">
            <tr class="exam-schedule-table__row--head">
                <th class="exam-schedule-table__cell">Date</th>
                <th class="exam-schedule-table__cell">Time</th>
                <th class="exam-schedule-table__cell">Location</th>
                <th class="exam-schedule-table__cell">Slots</th>
                <th class="exam-schedule-table__cell">Status</th>
                <th class="exam-schedule-table__cell exam-schedule-table__cell--actions-header">Action</th>
            </tr>
        </thead>

        <tbody class="exam-schedule-table__body" id="exam-schedule-table-body">
            <?php if (empty($rows)): ?>
                <tr class="exam-schedule-table__row">
                    <td class="exam-schedule-table__cell" colspan="6" style="text-align:center;">
                        No exam schedules found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                        $status = $row['status'] ?? 'closed';
                        $statusLabel = ucwords(str_replace('_', ' ', $status));
                    ?>
                    <tr class="exam-schedule-table__row" data-schedule-id="<?= esc($row['exam_schedule_id']) ?>">
                        <td class="exam-schedule-table__cell"><?= esc($row['date'] ?? '-') ?></td>
                        <td class="exam-schedule-table__cell"><?= esc($row['time'] ?? '-') ?></td>
                        <td class="exam-schedule-table__cell"><?= esc($row['location'] ?? '-') ?></td>
                        <td class="exam-schedule-table__cell"><?= esc($row['slots'] ?? '-') ?></td>
                        <td class="exam-schedule-table__cell">
                            <span class="status-badge status-badge--<?= esc($status) ?>">
                                <?= esc($statusLabel) ?>
                            </span>
                        </td>
                        <td class="exam-schedule-table__cell exam-schedule-table__cell--actions">
                            <button type="button" class="exam-schedule-table__action-btn" data-action="view" data-id="<?= esc($row['exam_schedule_id']) ?>">
                                <img src="<?= base_url('assets/images/icons/view.svg') ?>" alt="View">
                            </button>

                            <button type="button" class="exam-schedule-table__action-btn" data-action="edit" data-id="<?= esc($row['exam_schedule_id']) ?>">
                                <img src="<?= base_url('assets/images/icons/pen-edit.svg') ?>" alt="Edit">
                            </button>

                            <button type="button" class="exam-schedule-table__action-btn" data-action="delete" data-id="<?= esc($row['exam_schedule_id']) ?>">
                                <img src="<?= base_url('assets/images/icons/trash.svg') ?>" alt="Delete">
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?= $this->include('admission-officer/components/pagination') ?>
</div>