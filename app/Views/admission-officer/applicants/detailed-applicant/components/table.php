<div class="application-table__card card">

    <?php if (empty($rows)): ?>
        <div class="req-empty">No requirements found.</div>
    <?php else: ?>

        <?php
            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row['category'] ?? 'Uncategorized'][] = $row;
            }
        ?>

        <table class="application-table__table">
            <thead class="application-table__head">
                <tr class="application-table__row--head">
                    <th class="application-table__cell">Document</th>
                    <th class="application-table__cell req-col-required">Required</th>
                    <th class="application-table__cell">Soft Copy</th>
                    <th class="application-table__cell req-col-date">Uploaded</th>
                    <th class="application-table__cell req-col-status">Status</th>
                    <th class="application-table__cell req-col-action">Action</th>
                </tr>
            </thead>

            <tbody id="application-table-body">
                <?php foreach ($grouped as $categoryName => $categoryRows):
                    $uploadedCount = count(array_filter($categoryRows, fn($r) => !empty($r['soft_copy'])));
                    $total         = count($categoryRows);
                ?>

                    <!-- Category divider -->
                    <tr class="req-category-row">
                        <td colspan="6">
                            <div class="req-category-inner">
                                <span class="req-category-title"><?= esc($categoryName) ?></span>
                                <span class="req-category-pill"><?= $uploadedCount ?>/<?= $total ?> uploaded</span>
                            </div>
                        </td>
                    </tr>

                    <?php foreach ($categoryRows as $row):
                        $status     = $row['status'] ?? null;
                        $status     = $status ?: 'pending';
                        $isRequired = (int)($row['required'] ?? 0) === 1;
                        $hasFile    = !empty($row['soft_copy']);
                    ?>
                    <tr class="application-table__row req-doc-row">

                        <!-- Document name -->
                        <td class="application-table__cell">
                            <div class="req-doc-name">
                                <div class="req-doc-icon">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <span><?= esc($row['document_name'] ?? '-') ?></span>
                            </div>
                        </td>

                        <!-- Required -->
                        <td class="application-table__cell req-col-required">
                            <?php if ($isRequired): ?>
                                <span class="req-badge req-badge--required">Required</span>
                            <?php else: ?>
                                <span class="req-badge req-badge--optional">Optional</span>
                            <?php endif; ?>
                        </td>

                        <!-- Soft Copy -->
                        <td class="application-table__cell">
                            <?php if ($hasFile): ?>
                                <div class="req-file-info">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#31a646" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span class="req-filename" title="<?= esc($row['soft_copy']) ?>"><?= esc($row['soft_copy']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="req-not-uploaded">Not uploaded</span>
                            <?php endif; ?>
                        </td>

                        <!-- Upload date -->
                        <td class="application-table__cell req-col-date">
                            <?php if (!empty($row['upload_date'])): ?>
                                <span class="req-date"><?= esc(date('M j, Y', strtotime($row['upload_date']))) ?></span>
                            <?php else: ?>
                                <span class="req-not-uploaded">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td class="application-table__cell req-col-status">
                            <span class="status-badge status-badge--<?= esc($status) ?>">
                                <?= esc(ucwords(str_replace('_', ' ', $status))) ?>
                            </span>
                        </td>

                        <!-- Action -->
                        <td class="application-table__cell req-col-action">
                            <?php if ($hasFile): ?>
                                <button class="ao-view-doc-btn req-view-btn"
                                    data-doc-url="<?= esc(base_url('admission-officer/applicants/document/' . $applicantId . '/' . $row['program_requirement_id'])) ?>"
                                    data-doc-name="<?= esc($row['soft_copy']) ?>"
                                    title="View document">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            <?php else: ?>
                                <span class="req-no-action">—</span>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <?= $this->include('admission-officer/components/pagination') ?>
</div>
