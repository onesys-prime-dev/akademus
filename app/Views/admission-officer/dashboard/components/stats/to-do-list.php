<div class="to-do-list card">
    <span class="to-do-list__header">To Do Lists</span>

    <div class="to-do-list__body">
        <?php foreach ($todoItems as $item): ?>
        <?php
            $done     = (int) $item['done'];
            $total    = (int) $item['total'];
            $left     = max(0, $total - $done);
            $progress = $total > 0 ? round(($done / $total) * 100) : 0;

            $progressColor =
                $progress === 100 ? '#10b981' :
                ($progress > 60 ? '#3b82f6' :
                ($progress > 30 ? '#f59e0b' : '#ef4444'));
        ?>
        <div class="to-do-list__row">
            <div class="to-do-list__status <?= $left === 0 ? 'is-done' : '' ?>"></div>

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
                        <div class="to-do-list__progress-fill"
                            style="--progress: <?= $progress ?>%; background: <?= $progressColor ?>"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
