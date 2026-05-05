<div class="application-content__panel">
    <div class="profile-view">

        <?php if (empty($personal) && empty($educational) && empty($family)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">📄</div>
                <h3>No Applicant Form Data</h3>
                <p>No applicant form data submitted yet.</p>
            </div>

        <?php else: ?>

            <?php if (!empty($personal)): ?>
                <section class="profile-section">
                    <div class="profile-section__header">
                        <h3>Personal Information</h3>
                        <span>Primary Details</span>
                    </div>

                    <div class="profile-grid">
                        <?php foreach ($personal as $key => $value): ?>
                            <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                            <?php if ($value === null || $value === '') continue; ?>

                            <div class="info-card">
                                <label><?= esc(ucwords(str_replace('_', ' ', $key))) ?></label>
                                <div><?= esc($value) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>


            <?php if (!empty($educational)): ?>
                <section class="profile-section">
                    <div class="profile-section__header">
                        <h3>Educational Background</h3>
                        <span><?= count($educational) ?> record(s)</span>
                    </div>

                    <div class="stack-list">
                        <?php foreach ($educational as $i => $edu): ?>
                            <div class="stack-card">
                                <div class="stack-card__title">
                                    Record <?= $i + 1 ?>
                                </div>

                                <div class="profile-grid">
                                    <?php foreach ($edu as $key => $value): ?>
                                        <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                                        <?php if ($value === null || $value === '') continue; ?>

                                        <div class="info-card">
                                            <label><?= esc(ucwords(str_replace('_', ' ', $key))) ?></label>
                                            <div><?= esc($value) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>


            <?php if (!empty($family)): ?>
                <section class="profile-section">
                    <div class="profile-section__header">
                        <h3>Family Background</h3>
                        <span><?= count($family) ?> member(s)</span>
                    </div>

                    <div class="stack-list">
                        <?php foreach ($family as $i => $member): ?>
                            <div class="stack-card">
                                <div class="stack-card__title">
                                    Family Member <?= $i + 1 ?>
                                </div>

                                <div class="profile-grid">
                                    <?php foreach ($member as $key => $value): ?>
                                        <?php if (in_array($key, ['id', 'application_id', 'created_at', 'updated_at'])) continue; ?>
                                        <?php if ($value === null || $value === '') continue; ?>

                                        <div class="info-card">
                                            <label><?= esc(ucwords(str_replace('_', ' ', $key))) ?></label>
                                            <div><?= esc($value) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>


<style>
.profile-view{
    padding:28px;
    background:#fff;
}

.profile-section{
    margin-bottom:32px;
}

.profile-section__header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
    padding-bottom:12px;
    border-bottom:1px solid #ececec;
}

.profile-section__header h3{
    margin:0;
    font-size:16px;
    font-weight:700;
    color:#111827;
}

.profile-section__header span{
    font-size:12px;
    font-weight:600;
    color:#667085;
    background:#f3f4f6;
    padding:6px 10px;
    border-radius:999px;
}

.profile-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:14px;
}

.info-card{
    background:#fafafa;
    border:1px solid #ececec;
    border-radius:12px;
    padding:14px;
    transition:.2s ease;
}

.info-card:hover{
    background:#fff;
    border-color:#d9d9d9;
    box-shadow:0 4px 14px rgba(0,0,0,.04);
}

.info-card label{
    display:block;
    margin-bottom:6px;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:#98a2b3;
}

.info-card div{
    font-size:14px;
    font-weight:500;
    color:#111827;
    line-height:1.45;
    word-break:break-word;
}

.stack-list{
    display:flex;
    flex-direction:column;
    gap:16px;
}

.stack-card{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:18px;
}

.stack-card__title{
    font-size:13px;
    font-weight:700;
    color: var(--primary);
    margin-bottom:14px;
}

.empty-state{
    text-align:center;
    padding:80px 20px;
    color:#667085;
}

.empty-state__icon{
    font-size:42px;
    margin-bottom:14px;
}

.empty-state h3{
    margin:0 0 8px;
    font-size:18px;
    color:#111827;
}

.empty-state p{
    margin:0;
    font-size:14px;
}
</style>