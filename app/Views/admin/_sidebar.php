<!-- ── ADMIN SIDEBAR ── -->
<style>
/* All sidebar styles live here so pages don't repeat them */
.admin-nav {
    width: 220px;
    background: #0d1e38;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 10;
    scrollbar-width: none;
}
.admin-nav::-webkit-scrollbar { display: none; }

/* ── Brand ── */
.nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 14px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    text-decoration: none;
    flex-shrink: 0;
}
.nav-brand-logo {
    width: 36px; height: 36px;
    background: var(--accent, #ffb200);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800;
    color: var(--accent-text, #002d7a);
    overflow: hidden; flex-shrink: 0;
}
/* Use contain so logos aren't cropped — transparent bg shows through */
.nav-brand-logo img { width: 100%; height: 100%; object-fit: contain; }
.nav-brand-text { min-width: 0; }
.nav-brand-name {
    font-size: 13px; font-weight: 800; color: #fff;
    letter-spacing: -0.2px; line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.nav-brand-sub { font-size: 10px; color: rgba(255,255,255,0.35); font-weight: 500; }

/* ── Nav body ── */
.nav-body {
    flex: 1;
    padding: 10px 10px;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.nav-section-label {
    font-size: 9px; font-weight: 700; letter-spacing: 1.1px;
    text-transform: uppercase; color: rgba(255,255,255,0.2);
    padding: 12px 8px 5px;
    flex-shrink: 0;
}
.nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px; border-radius: 8px;
    text-decoration: none;
    color: rgba(255,255,255,0.55);
    font-size: 12.5px; font-weight: 600;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    border-left: 3px solid transparent;
}
.nav-item:hover {
    background: rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.9);
}
.nav-item.on {
    background: rgba(0,64,166,0.45);
    color: #fff;
    border-left-color: var(--accent, #ffb200);
}
.nav-item svg { flex-shrink: 0; transition: opacity 0.15s; opacity: 0.6; }
.nav-item.on svg, .nav-item:hover svg { opacity: 1; }

/* ── Footer ── */
.nav-footer {
    padding: 10px;
    border-top: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}
.nav-admin-card {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 8px 10px;
}
.nav-admin-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--primary, #0040a6);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.nav-admin-name {
    font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.8);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.nav-admin-role { font-size: 10px; color: rgba(255,255,255,0.3); }
</style>

<nav class="admin-nav">

    <!-- Brand -->
    <a href="<?= base_url('admin/theme') ?>" class="nav-brand">
        <div class="nav-brand-logo">
            <?php if (!empty($theme['logo_url'])): ?>
                <img src="<?= base_url(esc($theme['logo_url'])) ?>" alt="logo">
            <?php else: ?>
                <?= strtoupper(substr($theme['logo_text'] ?? $theme['app_name'] ?? 'AK', 0, 2)) ?>
            <?php endif; ?>
        </div>
        <div class="nav-brand-text">
            <div class="nav-brand-name"><?= esc($theme['app_name'] ?? 'AKADEMUS') ?></div>
            <div class="nav-brand-sub">Admin Panel</div>
        </div>
    </a>

    <!-- Navigation -->
    <div class="nav-body">

        <div class="nav-section-label">Customize</div>

        <a href="<?= base_url('admin/theme') ?>" class="nav-item <?= strpos(current_url(), 'admin/theme') !== false ? 'on' : '' ?>">
            <!-- Sliders: theme/customization -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
            </svg>
            Theme Editor
        </a>

        <div class="nav-section-label">Forms</div>

        <a href="<?= base_url('admin/form-fields') ?>" class="nav-item <?= strpos(current_url(), 'admin/form-fields') !== false ? 'on' : '' ?>">
            <!-- FileEdit: editing form fields on a document -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Form Fields
        </a>

        <a href="<?= base_url('admin/presets') ?>" class="nav-item <?= (strpos(current_url(), 'admin/presets') !== false && strpos(current_url(), 'programs') === false) ? 'on' : '' ?>">
            <!-- Clipboard list: form presets/templates -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Applicant Forms
        </a>

        <a href="<?= base_url('admin/requirement-categories') ?>" class="nav-item <?= strpos(current_url(), 'admin/requirement-categories') !== false ? 'on' : '' ?>">
            <!-- Folder: document categories -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                <line x1="9" y1="13" x2="15" y2="13"/>
                <line x1="12" y1="10" x2="12" y2="16"/>
            </svg>
            Requirement Docs
        </a>

        <a href="<?= base_url('admin/programs/presets') ?>" class="nav-item <?= strpos(current_url(), 'admin/programs/presets') !== false ? 'on' : '' ?>">
            <!-- Layers: stacking programs to presets -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                <polyline points="2 17 12 22 22 17"/>
                <polyline points="2 12 12 17 22 12"/>
            </svg>
            Program Assignments
        </a>

        <div class="nav-section-label">System</div>

        <a href="<?= base_url('admin/users') ?>" class="nav-item <?= strpos(current_url(), 'admin/users') !== false ? 'on' : '' ?>">
            <!-- Users: two people = manage user accounts -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Manage Users
        </a>

    </div>

    <!-- Footer -->
    <div class="nav-footer">
        <div class="nav-admin-card">
            <div class="nav-admin-avatar"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
            <div style="min-width:0;">
                <div class="nav-admin-name"><?= esc(session()->get('admin_name') ?? 'Admin') ?></div>
                <div class="nav-admin-role">Administrator</div>
            </div>
        </div>
        <a href="<?= base_url('admin/logout') ?>" class="nav-item" style="color:rgba(255,100,100,0.7);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
        </a>
    </div>

</nav>
