<!-- ── ICON SIDEBAR (SHARED) ── -->
<div class="icon-sidebar">
    <div class="isb-logo">
        <?php if (!empty($theme['logo_url'])): ?>
            <img src="<?= esc($theme['logo_url']) ?>" />
        <?php else: ?>
            <?= esc($theme['logo_text'] ?? 'AK') ?>
        <?php endif; ?>
    </div>

    <a href="<?= base_url('admin/theme') ?>" class="isb-item <?= (strpos(current_url(), 'admin/theme') !== false) ? 'on' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 0 20"/><path d="M2 12h20"/></svg>
        <div class="isb-tip">Theme Editor</div>
    </a>

    <a href="<?= base_url('admin/form-fields') ?>" class="isb-item <?= (strpos(current_url(), 'admin/form-fields') !== false) ? 'on' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <div class="isb-tip">Form Fields</div>
    </a>

    <a href="<?= base_url('admin/presets') ?>" class="isb-item <?= (strpos(current_url(), 'admin/presets') !== false) ? 'on' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><line x1="3" y1="9" x2="21" y2="9"/><path d="M9 5v4M15 5v4"/></svg>
        <div class="isb-tip">Applicant Forms</div>
    </a>

    <a href="<?= base_url('admin/requirement-categories') ?>" class="isb-item <?= (strpos(current_url(), 'admin/requirement-categories') !== false) ? 'on' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>
        <div class="isb-tip">Requirement Categories</div>
    </a>

    <a href="<?= base_url('admin/programs/presets') ?>" class="isb-item <?= (strpos(current_url(), 'admin/programs/presets') !== false) ? 'on' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <div class="isb-tip">Program Assignments</div>
    </a>

    <div class="isb-spacer"></div>

    <a href="<?= base_url('admin/logout') ?>" class="isb-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <div class="isb-tip">Logout</div>
    </a>
</div>
