<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme     = $theme     ?? [];
$category  = $category  ?? [];
$documents = $documents ?? [];
$action    = $action    ?? 'create';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | <?= ($action === 'edit') ? 'Edit Category' : 'Create Category' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color']  ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']   ?? '#002d7a') ?>;
            --accent:       <?= esc($theme['accent_color']   ?? '#ffb200') ?>;
            --accent-text:  <?= esc($theme['accent_text']    ?? '#002d7a') ?>;
            --radius:       <?= esc($theme['border_radius']  ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']   ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; }
        .shell { display: flex; height: 100vh; overflow: hidden; }
        /* sidebar styles are in admin/_sidebar.php */
        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }
        .content-area { flex:1; overflow-y:auto; padding:28px; }
        .page-title { font-size:24px; font-weight:700; color:#0d1e38; margin-bottom:24px; }
        .form-card { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); padding:28px; max-width:680px; }
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:700; color:#0d1e38; margin-bottom:6px; }
        .form-input, .form-textarea { width:100%; padding:10px 12px; border:1px solid #dde3f0; border-radius:6px; font-size:13px; font-family:var(--font); outline:none; transition:all .18s; }
        .form-input:focus, .form-textarea:focus { border-color:var(--primary); background:#f0f4f8; }
        .form-textarea { min-height:80px; resize:vertical; }
        .form-help { font-size:11px; color:#5a6a85; margin-top:4px; }
        .form-actions { display:flex; gap:10px; margin-top:28px; }
        .btn { padding:11px 20px; border:none; border-radius:var(--radius); cursor:pointer; font-size:13px; font-weight:700; transition:all .18s; text-decoration:none; display:inline-block; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-ghost { background:transparent; border:1px solid #dde3f0; color:#5a6a85; }
        .btn-ghost:hover { background:#f9fafb; }
        .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13px; }
        svg { width:16px; height:16px; }

        /* Document rows */
        #doc-list { display:flex; flex-direction:column; gap:10px; margin-bottom:10px; }
        .doc-row { display:flex; align-items:flex-start; gap:8px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:10px; }
        .doc-row-fields { flex:1; display:grid; grid-template-columns:1fr 1fr; gap:8px; }
        .doc-row-fields input[type="text"] { margin:0; }
        .doc-full { grid-column: 1 / -1; }
        .doc-required-label { display:flex; align-items:center; gap:6px; font-size:12px; color:#374151; cursor:pointer; grid-column:1/-1; }
        .doc-required-label input[type="checkbox"] { width:15px; height:15px; accent-color:var(--primary); cursor:pointer; }
        .btn-remove-doc { background:#fee2e2; color:#dc2626; border:none; border-radius:6px; width:28px; height:28px; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
        .btn-remove-doc:hover { background:#fecaca; }
        .btn-add-doc { background:#e8f0ff; color:var(--primary); border:1px dashed var(--primary); border-radius:6px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; transition:all .18s; width:100%; }
        .btn-add-doc:hover { background:#d0daf5; }
    </style>
</head>
<body>
<div class="shell">
    <?php echo view('admin/_sidebar', ['theme' => $theme]); ?>
    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title"><?= ($action === 'edit') ? 'Edit Category' : 'New Category' ?></div>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>
        <div class="content-area">
            <h1 class="page-title"><?= ($action === 'edit') ? 'Edit: ' . esc($category['category_name'] ?? '') : 'Create Requirement Category' ?></h1>

            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <?php foreach ($errors as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="<?= ($action === 'edit') ? base_url('admin/requirement-categories/' . ($category['category_id'] ?? '') . '/update') : base_url('admin/requirement-categories/store') ?>"
                  class="form-card">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="category_name" class="form-label">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" class="form-input"
                           value="<?= esc($oldInput['category_name'] ?? $category['category_name'] ?? '') ?>"
                           placeholder="e.g., Personal Documents, Academic, Medical"
                           required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea"
                              placeholder="Brief description shown to applicants at the top of this step"><?= esc($oldInput['description'] ?? $category['description'] ?? '') ?></textarea>
                    <p class="form-help">This text appears as a subtitle on the requirements step for this category.</p>
                </div>

                <!-- DOCUMENTS -->
                <div class="form-group">
                    <label class="form-label">Documents</label>
                    <p class="form-help" style="margin-bottom:12px;">List all documents in this category. Each becomes an upload slot for the applicant.</p>

                    <div id="doc-list">
                        <?php foreach ($documents as $i => $doc): ?>
                        <div class="doc-row" data-index="<?= $i ?>">
                            <div class="doc-row-fields">
                                <input type="hidden"
                                       name="documents[<?= $i ?>][requirement_id]"
                                       value="<?= esc($doc['requirement_id']) ?>">
                                <input type="text"
                                       name="documents[<?= $i ?>][requirement_name]"
                                       class="form-input doc-full"
                                       placeholder="Document name *"
                                       value="<?= esc($doc['requirement_name']) ?>"
                                       required>
                                <input type="text"
                                       name="documents[<?= $i ?>][description]"
                                       class="form-input doc-full"
                                       placeholder="Description (e.g. Original or certified copy - PDF/Image)"
                                       value="<?= esc($doc['description'] ?? '') ?>">
                                <label class="doc-required-label">
                                    <input type="checkbox"
                                           name="documents[<?= $i ?>][is_required]"
                                           value="1"
                                           <?= !empty($doc['is_required']) ? 'checked' : '' ?>>
                                    Required
                                </label>
                            </div>
                            <button type="button" class="btn-remove-doc" onclick="removeDoc(this)">✕</button>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn-add-doc" id="btn-add-doc">+ Add Document</button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= ($action === 'edit') ? 'Update Category' : 'Create Category' ?>
                    </button>
                    <a href="<?= base_url('admin/requirement-categories') ?>" class="btn btn-ghost">Cancel</a>
                </div>
            </form>

            <script>
            let docIndex = <?= max(count($documents), 1) ?>;

            function addDocRow(data = {}) {
                const i    = docIndex++;
                const list = document.getElementById('doc-list');
                const div  = document.createElement('div');
                div.className = 'doc-row';
                div.dataset.index = i;
                div.innerHTML = `
                    <div class="doc-row-fields">
                        <input type="hidden"
                               name="documents[${i}][requirement_id]"
                               value="${data.requirement_id || ''}">
                        <input type="text"
                               name="documents[${i}][requirement_name]"
                               class="form-input doc-full"
                               placeholder="Document name *"
                               value="${data.requirement_name || ''}"
                               required>
                        <input type="text"
                               name="documents[${i}][description]"
                               class="form-input doc-full"
                               placeholder="Description (e.g. Original or certified copy - PDF/Image)"
                               value="${data.description || ''}">
                        <label class="doc-required-label">
                            <input type="checkbox"
                                   name="documents[${i}][is_required]"
                                   value="1"
                                   ${data.is_required !== false ? 'checked' : ''}>
                            Required
                        </label>
                    </div>
                    <button type="button" class="btn-remove-doc" onclick="removeDoc(this)">✕</button>
                `;
                list.appendChild(div);
            }

            function removeDoc(btn) {
                btn.closest('.doc-row').remove();
            }

            document.getElementById('btn-add-doc').addEventListener('click', () => addDocRow());
            </script>
        </div>
    </div>
</div>
</body>
</html>
