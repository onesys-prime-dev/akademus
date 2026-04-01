<div class="application-content__panel">
    <div style="padding: 24px;">

        <?php if (empty($personal) && empty($educational) && empty($family)): ?>
            <p style="text-align:center; color:#888;">No applicant form data submitted yet.</p>

        <?php else: ?>

            <?php if (!empty($personal)): ?>
            <section style="margin-bottom: 28px;">
                <h4 style="margin-bottom: 12px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #555;">Personal Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;">
                    <?php foreach ($personal as $key => $value): ?>
                        <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                        <?php if ($value === null || $value === '') continue; ?>
                        <div style="background:#f9f9f9; border-radius:6px; padding:10px 14px;">
                            <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">
                                <?= esc(ucwords(str_replace('_', ' ', $key))) ?>
                            </div>
                            <div style="font-size:13px; color:#222;">
                                <?= esc($value) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($educational)): ?>
            <section style="margin-bottom: 28px;">
                <h4 style="margin-bottom: 12px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #555;">Educational Background</h4>
                <?php foreach ($educational as $i => $edu): ?>
                    <div style="background:#f9f9f9; border-radius:6px; padding:14px; margin-bottom:10px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            <?php foreach ($edu as $key => $value): ?>
                                <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                                <?php if ($value === null || $value === '') continue; ?>
                                <div>
                                    <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">
                                        <?= esc(ucwords(str_replace('_', ' ', $key))) ?>
                                    </div>
                                    <div style="font-size:13px; color:#222;">
                                        <?= esc($value) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>

            <?php if (!empty($family)): ?>
            <section style="margin-bottom: 28px;">
                <h4 style="margin-bottom: 12px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #555;">Family Background</h4>
                <?php foreach ($family as $i => $member): ?>
                    <div style="background:#f9f9f9; border-radius:6px; padding:14px; margin-bottom:10px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            <?php foreach ($member as $key => $value): ?>
                                <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                                <?php if ($value === null || $value === '') continue; ?>
                                <div>
                                    <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">
                                        <?= esc(ucwords(str_replace('_', ' ', $key))) ?>
                                    </div>
                                    <div style="font-size:13px; color:#222;">
                                        <?= esc($value) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>
