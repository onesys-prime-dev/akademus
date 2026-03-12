<?php
/** @var array $page */
/** @var array $grouped */
?>

<div class="ff-page-header">
    <div class="ff-page-header-left">
        <h2>
            <?= esc($page['title']) ?>
            <span class="page-status-badge <?= $page['is_enabled'] ? 'enabled' : 'disabled' ?>">
                <?= $page['is_enabled'] ? 'Enabled' : 'Disabled' ?>
            </span>
        </h2>
        <p><?= esc($page['description'] ?? '') ?></p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <button class="ff-btn ff-btn-ghost" onclick="ffAddSection(<?= (int)$page['id'] ?>)" style="font-size:12px;padding:7px 16px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Section
        </button>
        <button class="ff-btn ff-btn-ghost" onclick="ffResetAll()" style="font-size:12px;padding:7px 16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
            </svg>
            Reset All
        </button>
        <button class="ff-btn ff-btn-primary" onclick="ffSaveAll()" style="font-size:12px;padding:7px 16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Save Changes
        </button>
    </div>
</div>

<?php if (!$page['is_enabled']): ?>
<div class="disabled-page-notice">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    This page is currently <strong>&nbsp;disabled&nbsp;</strong>. Applicants will skip it. You can still edit its fields here.
</div>
<?php endif; ?>

<?php foreach ($grouped as $sectionId => $sectionFields): ?>
<div class="ff-section-card">
    <div class="ff-section-head">
        <h4>
            <?= esc(ucwords(str_replace(['-','_'], ' ', preg_replace('/^step\d+-/', '', $sectionId)))) ?>
            <span>Page <?= (int)$page['id'] ?></span>
        </h4>
        <button class="ff-btn ff-btn-ghost" onclick="ffOpenAddRow('<?= esc($sectionId) ?>-add')" style="padding:6px 14px;font-size:12px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Field
        </button>
    </div>

    <div class="ff-col-headers">
        <span></span><span>Field</span><span>Type</span><span>Placeholder</span><span>Visible</span><span>Actions</span>
    </div>

    <div class="ff-fields-list" id="<?= esc($sectionId) ?>">
        <?php foreach ($sectionFields as $f): ?>
        <div class="ff-field-row <?= $f['is_visible'] ? '' : 'disabled-row' ?>"
             data-key="<?= esc($f['field_key']) ?>"
             data-page="<?= (int)$f['page_id'] ?>"
             data-section="<?= esc($sectionId) ?>"
             data-system="<?= $f['is_system'] ? '1' : '0' ?>">

            <div class="ff-drag-handle">
                <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor">
                    <circle cx="4" cy="3" r="1.5"/><circle cx="8" cy="3" r="1.5"/>
                    <circle cx="4" cy="8" r="1.5"/><circle cx="8" cy="8" r="1.5"/>
                    <circle cx="4" cy="13" r="1.5"/><circle cx="8" cy="13" r="1.5"/>
                </svg>
            </div>

            <div class="ff-field-name">
                <div class="ff-field-label">
                    <?= esc($f['label']) ?>
                    <span class="<?= $f['is_system'] ? 'badge-system' : 'badge-custom' ?>">
                        <?= $f['is_system'] ? 'SYSTEM' : 'CUSTOM' ?>
                    </span>
                </div>
                <div class="ff-field-key"><?= esc($f['field_key']) ?></div>
            </div>

            <div class="ff-field-type"><?= strtoupper(esc($f['field_type'])) ?></div>
            <div class="ff-field-placeholder"><?= esc($f['placeholder'] ?: '—') ?></div>

            <div class="ff-toggle-wrap">
                <label class="ff-toggle">
                    <input type="checkbox" <?= $f['is_visible'] ? 'checked' : '' ?> onchange="ffToggleVisible(this)">
                    <div class="ff-toggle-track"></div>
                </label>
                <span class="ff-toggle-label"><?= $f['is_visible'] ? 'On' : 'Off' ?></span>
            </div>

            <div class="ff-row-actions">
                <button class="ff-icon-btn" onclick="ffEditField(this)" title="Edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <?php if (!$f['is_system']): ?>
                <button class="ff-icon-btn delete" onclick="ffDeleteField(this)" title="Delete">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ff-add-row" id="<?= esc($sectionId) ?>-add">
        <div class="ff-add-row-title">+ New Custom Field</div>
        <div class="ff-add-grid">
            <div class="ff-add-field">
                <label>Label</label>
                <input type="text" id="add-label-<?= esc($sectionId) ?>" placeholder="e.g. Religion">
            </div>
            <div class="ff-add-field">
                <label>Key</label>
                <input type="text" id="add-key-<?= esc($sectionId) ?>" placeholder="auto-generated">
            </div>
            <div class="ff-add-field">
                <label>Type</label>
                <select id="add-type-<?= esc($sectionId) ?>">
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="email">Email</option>
                    <option value="tel">Phone</option>
                    <option value="date">Date</option>
                    <option value="select">Select</option>
                    <option value="textarea">Textarea</option>
                </select>
            </div>
            <div class="ff-add-field">
                <label>Placeholder</label>
                <input type="text" id="add-placeholder-<?= esc($sectionId) ?>" placeholder="Optional">
            </div>
            <div style="display:flex;gap:6px;">
                <button class="ff-btn ff-btn-primary"
                        onclick="ffAddCustomField('<?= esc($sectionId) ?>', <?= (int)$page['id'] ?>)"
                        style="padding:9px 14px;">Add</button>
                <button class="ff-btn ff-btn-ghost"
                        onclick="ffCloseAddRow('<?= esc($sectionId) ?>-add')"
                        style="padding:9px 14px;">✕</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($grouped)): ?>
<div class="ff-empty" id="ff-empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14,2 14,8 20,8"/>
    </svg>
    <p>No fields on this page yet.</p>
    <button class="ff-btn ff-btn-primary" style="margin-top:14px;" onclick="ffAddSection(<?= (int)$page['id'] ?>)">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add First Section
    </button>
</div>
<?php endif; ?>

<!-- SAVE BAR -->
<div class="ff-save-bar">
    <div class="ff-save-bar-msg">
        <strong id="ff-change-count">0 changes</strong> pending — don't forget to save!
    </div>
    <div class="ff-save-actions">
        <button class="ff-btn ff-btn-ghost" onclick="ffDiscardChanges()">Discard</button>
        <button class="ff-btn ff-btn-primary" onclick="ffSaveAll()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Save Changes
        </button>
    </div>
</div>