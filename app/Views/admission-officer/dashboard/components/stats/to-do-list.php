<div class="to-do-list card">
    <span class="to-do-list__header">Action Items</span>

    <div class="to-do-list__body">
        <?php foreach ($todoItems as $item): ?>
        <?php
            $done     = (int) $item['done'];
            $total    = (int) $item['total'];
            $left     = max(0, $total - $done);
            $progress = $total > 0 ? round(($done / $total) * 100) : 0;
        ?>
        <div class="to-do-list__row">
            <input type="checkbox" class="to-do-list_checkbox" <?= $left === 0 ? 'checked' : '' ?>>

            <div class="to-do-list__details">
                <span class="to-do-list__details--task">
                    <?= esc($item['task']) ?>
                </span>

                <div class="to-do-list__progress">
                    <div class="to-do-list__progress-top">
                        <span class="to-do-list__progress-count"><?= number_format($done) ?> / <?= number_format($total) ?></span>
                        <span class="to-do-list__progress-remaining"><?= number_format($left) ?> left</span>
                    </div>

                    <div class="to-do-list__progress-bar">
                        <div class="to-do-list__progress-fill" style="--progress: <?= $progress ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
