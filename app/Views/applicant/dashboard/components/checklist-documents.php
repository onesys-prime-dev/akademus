<?php
// Variables expected:
// $doc_stats, $doc_progress, $docBarClass, $previewReqs, $uploadedDoc, $moreCount, $allReqs
?>

<div class="document-checklist-summary card">
    <div class="top-dashboard-container">

        <div class="top-dashboard-header">
            <h4>Documents</h4>
            <p><?= $doc_stats['uploaded_required'] ?> of <?= $doc_stats['total_required'] ?> required uploaded</p>
        </div>

        <div class="documents-progress-container">
            <div class="documents-progress-bar">
                <div class="documents-current-progress <?= $docBarClass ?>"
                     style="width:<?= $doc_progress ?>%"></div>
            </div>
            <div class="documents-progress-legend-container">
                <div class="document-progress-legend">
                    <div class="documents-progress-legend-circle legend-uploaded"></div>
                    <p>Uploaded <span><?= $doc_stats['uploaded_required'] ?></span></p>
                </div>
                <div class="document-progress-legend">
                    <div class="documents-progress-legend-circle legend-missing"></div>
                    <p>Missing <span><?= $doc_stats['missing_required'] ?></span></p>
                </div>
                <?php if ($doc_stats['total_optional'] > 0): ?>
                <div class="document-progress-legend">
                    <div class="documents-progress-legend-circle legend-optional"></div>
                    <p>Optional <span><?= $doc_stats['total_optional'] ?></span></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-checklist-row-container">
            <?php foreach ($previewReqs as $req):
                $uploaded = isset($uploadedDoc[$req['program_requirement_id']]);
            ?>
                <div class="dashboard-checklist-row <?= $uploaded ? 'checklist-row-done' : '' ?>">
                    <div class="dashboard-checklist-box <?= $uploaded ? 'checklist-box-done' : '' ?>">
                        <?= $uploaded ? '✓' : '' ?>
                    </div>
                    <p><?= esc($req['requirement_name']) ?></p>
                </div>
            <?php endforeach; ?>

            <?php if ($moreCount > 0): ?>
                <div class="dashboard-checklist-row">
                    <span>+<?= $moreCount ?> more document<?= $moreCount > 1 ? 's' : '' ?>…</span>
                </div>
            <?php endif; ?>

            <?php if (empty($allReqs)): ?>
                <div class="dashboard-checklist-row">
                    <span>No documents required for your program yet.</span>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="bottom-dashboard-container">
        <span class="checklist-completed-container"><?= $doc_progress ?>% Completed</span>
        <a href="<?= base_url('requirements') ?>" class="continue-dashboard-btn">
            <?= $doc_progress >= 100 ? 'Review' : 'Upload' ?>
        </a>
    </div>
</div>
