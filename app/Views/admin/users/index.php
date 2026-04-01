<?php
if (!session()->get('admin_logged_in')) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
$theme = $theme ?? [];
$users = $users ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      <?= esc($theme['primary_color'] ?? '#0040a6') ?>;
            --primary-dark: <?= esc($theme['primary_dark']  ?? '#002d7a') ?>;
            --accent:       <?= esc($theme['accent_color']  ?? '#ffb200') ?>;
            --accent-text:  <?= esc($theme['accent_text']   ?? '#002d7a') ?>;
            --radius:       <?= esc($theme['border_radius'] ?? '8px') ?>;
            --font:         '<?= esc($theme['font_family']  ?? 'Sora') ?>';
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: var(--font), 'Sora', sans-serif; overflow: hidden; }
        .shell { display: flex; height: 100vh; overflow: hidden; }

        /* sidebar styles are in admin/_sidebar.php */

        /* ── MAIN ── */
        .main-area { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:52px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 28px; flex-shrink:0; }
        .topbar-title { font-size:20px; font-weight:800; color:#0d1e38; }
        .topbar-admin { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:#5a6a85; }
        .topbar-admin-dot { width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        .content-area { flex:1; overflow-y:auto; padding:28px; }
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
        .page-title { font-size:24px; font-weight:800; color:#0d1e38; margin-bottom:4px; }
        .page-desc  { font-size:13px; color:#5a6a85; }

        /* ── ROLE TABS ── */
        .role-tabs { display:flex; gap:8px; margin-bottom:20px; }
        .role-tab { padding:7px 16px; border-radius:20px; font-size:12px; font-weight:700; border:1.5px solid #dde3f0; background:#fff; color:#5a6a85; cursor:pointer; transition:all .18s; }
        .role-tab.on { background:var(--primary); color:#fff; border-color:var(--primary); }

        /* ── TABLE ── */
        .table-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { padding:11px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#5a6a85; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
        td { padding:13px 16px; font-size:13px; color:#0d1e38; border-bottom:1px solid #f0f4f8; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafbff; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-admin { background:#e8f0ff; color:var(--primary); }
        .badge-officer { background:#f0fdf4; color:#15803d; }

        .avatar { width:32px; height:32px; border-radius:50%; background:var(--primary); display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; flex-shrink:0; }

        .user-cell { display:flex; align-items:center; gap:10px; }
        .user-name { font-weight:700; color:#0d1e38; }
        .user-email { font-size:11px; color:#5a6a85; }

        .action-btns { display:flex; gap:6px; }
        .btn { padding:6px 12px; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:4px; }
        .btn-edit { background:#e8f0ff; color:var(--primary); }
        .btn-edit:hover { background:#d0daf5; }
        .btn-pwd  { background:#fef9ec; color:#b45309; }
        .btn-pwd:hover  { background:#fef3c7; }
        .btn-del  { background:#fef2f2; color:#dc2626; }
        .btn-del:hover  { background:#fee2e2; }

        .btn-primary { background:var(--primary); color:#fff; padding:9px 18px; font-size:13px; font-weight:700; border-radius:var(--radius); border:none; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:6px; }
        .btn-primary:hover { background:var(--primary-dark); }

        .empty { text-align:center; padding:48px; color:#5a6a85; font-size:14px; }

        /* ── MODAL ── */
        .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(3px); z-index:9999; align-items:center; justify-content:center; }
        .overlay.open { display:flex; }
        .modal { background:#fff; border-radius:14px; width:100%; max-width:460px; margin:16px; box-shadow:0 24px 60px rgba(0,0,0,.2); overflow:hidden; animation:mpop .2s ease; }
        @keyframes mpop { from{transform:scale(.94);opacity:0} to{transform:scale(1);opacity:1} }
        .modal-head { background:var(--primary); padding:18px 22px; }
        .modal-head h3 { font-size:16px; font-weight:700; color:#fff; margin-bottom:2px; }
        .modal-head p  { font-size:12px; color:rgba(255,255,255,.7); }
        .modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; }

        .field-group { display:flex; flex-direction:column; gap:4px; }
        .field-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#5a6a85; }
        .field-input {
            width:100%; padding:9px 12px; border:1.5px solid #dde3f0; border-radius:var(--radius);
            font-family:var(--font),'Sora',sans-serif; font-size:13px; color:#0d1e38; outline:none;
            transition:border-color .18s;
        }
        .field-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(0,64,166,.08); }
        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        select.field-input { cursor:pointer; }

        .modal-footer { padding:0 22px 18px; display:flex; gap:8px; justify-content:flex-end; }
        .btn-save { background:var(--primary); color:#fff; padding:9px 22px; border:none; border-radius:var(--radius); font-family:var(--font),'Sora',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; }
        .btn-save:hover { background:var(--primary-dark); }
        .btn-save:disabled { opacity:.6; cursor:not-allowed; }
        .btn-cancel { background:#f0f4f8; color:#5a6a85; padding:9px 18px; border:none; border-radius:var(--radius); font-family:var(--font),'Sora',sans-serif; font-size:13px; font-weight:600; cursor:pointer; }
        .btn-cancel:hover { background:#e2e8f0; }

        .field-error { font-size:11px; color:#dc2626; margin-top:2px; display:none; }

        /* ── TOAST ── */
        .toast { position:fixed; bottom:24px; right:24px; padding:12px 18px; border-radius:var(--radius); font-size:13px; font-weight:600; color:#fff; z-index:99999; box-shadow:0 8px 24px rgba(0,0,0,.15); transform:translateY(8px); opacity:0; transition:all .25s; pointer-events:none; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast.success { background:#15803d; }
        .toast.error   { background:#dc2626; }
    </style>
</head>
<body>
<div class="shell">

    <!-- SIDEBAR -->
    <?php echo view('admin/_sidebar', ['theme' => $theme]); ?>

    <!-- MAIN -->
    <div class="main-area">
        <div class="topbar">
            <span class="topbar-title">Manage Users</span>
            <div class="topbar-admin">
                <div class="topbar-admin-dot"><?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?></div>
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <div>
                    <div class="page-title">Users</div>
                    <div class="page-desc">Manage admin and admission officer accounts.</div>
                </div>
                <button class="btn-primary" onclick="openAdd()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add User
                </button>
            </div>

            <!-- ROLE TABS -->
            <div class="role-tabs">
                <button class="role-tab on" data-filter="all" onclick="filterRole('all', this)">All Users</button>
                <button class="role-tab" data-filter="admin" onclick="filterRole('admin', this)">Admins</button>
                <button class="role-tab" data-filter="admission_officer" onclick="filterRole('admission_officer', this)">Admission Officers</button>
            </div>

            <!-- TABLE -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        <?php if (empty($users)): ?>
                            <tr><td colspan="3" class="empty">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                            <tr data-role="<?= esc($u['role']) ?>">
                                <td>
                                    <div class="user-cell">
                                        <div class="avatar"><?= strtoupper(substr($u['firstname'], 0, 1) . substr($u['lastname'], 0, 1)) ?></div>
                                        <div>
                                            <div class="user-name"><?= esc($u['firstname'] . ' ' . $u['lastname']) ?></div>
                                            <div class="user-email"><?= esc($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-officer">Admission Officer</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-edit" onclick="openEdit(<?= $u['user_id'] ?>, '<?= esc($u['firstname']) ?>', '<?= esc($u['lastname']) ?>', '<?= esc($u['email']) ?>', '<?= esc($u['role']) ?>')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn btn-pwd" onclick="openPwd(<?= $u['user_id'] ?>, '<?= esc($u['firstname'] . ' ' . $u['lastname']) ?>')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                            Password
                                        </button>
                                        <?php if ((int)$u['user_id'] !== (int)session()->get('admin_id')): ?>
                                        <button class="btn btn-del" onclick="confirmDelete(<?= $u['user_id'] ?>, '<?= esc($u['firstname'] . ' ' . $u['lastname']) ?>')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            Delete
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ADD/EDIT MODAL -->
<div class="overlay" id="user-modal">
    <div class="modal">
        <div class="modal-head">
            <h3 id="modal-title">Add User</h3>
            <p id="modal-sub">Create a new admin or admission officer account.</p>
        </div>
        <div class="modal-body">
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label">First Name</label>
                    <input class="field-input" type="text" id="f-firstname" placeholder="Juan">
                </div>
                <div class="field-group">
                    <label class="field-label">Last Name</label>
                    <input class="field-input" type="text" id="f-lastname" placeholder="Dela Cruz">
                </div>
            </div>
            <div class="field-group">
                <label class="field-label">Email Address</label>
                <input class="field-input" type="email" id="f-email" placeholder="user@akademus.edu.ph">
            </div>
            <div class="field-group" id="f-password-group">
                <label class="field-label">Password</label>
                <input class="field-input" type="password" id="f-password" placeholder="Min. 8 characters">
                <span class="field-error" id="f-password-err">Password must be at least 8 characters.</span>
            </div>
            <div class="field-group">
                <label class="field-label">Role</label>
                <select class="field-input" id="f-role">
                    <option value="admin">Admin</option>
                    <option value="admission_officer">Admission Officer</option>
                </select>
            </div>
            <div class="field-error" id="modal-error" style="display:block;font-size:12px;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('user-modal')">Cancel</button>
            <button class="btn-save" id="modal-save-btn" onclick="saveUser()">Save User</button>
        </div>
    </div>
</div>

<!-- RESET PASSWORD MODAL -->
<div class="overlay" id="pwd-modal">
    <div class="modal">
        <div class="modal-head">
            <h3>Reset Password</h3>
            <p id="pwd-modal-sub">Set a new password for this user.</p>
        </div>
        <div class="modal-body">
            <div class="field-group">
                <label class="field-label">New Password</label>
                <input class="field-input" type="password" id="p-password" placeholder="Min. 8 characters">
            </div>
            <div class="field-group">
                <label class="field-label">Confirm Password</label>
                <input class="field-input" type="password" id="p-confirm" placeholder="Repeat password">
            </div>
            <div class="field-error" id="pwd-error" style="display:block;font-size:12px;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('pwd-modal')">Cancel</button>
            <button class="btn-save" id="pwd-save-btn" onclick="savePassword()">Reset Password</button>
        </div>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="overlay" id="del-modal">
    <div class="modal">
        <div class="modal-head" style="background:#dc2626;">
            <h3>Delete User</h3>
            <p>This action cannot be undone.</p>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:#0d1e38;">Are you sure you want to delete <strong id="del-name"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('del-modal')">Cancel</button>
            <button class="btn-save" id="del-confirm-btn" style="background:#dc2626;" onclick="doDelete()">Delete</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const BASE = '<?= base_url() ?>';

    let currentUserId  = null;
    let isEditMode     = false;

    /* ── FILTER ── */
    function filterRole(role, btn) {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('on'));
        btn.classList.add('on');
        document.querySelectorAll('#users-tbody tr[data-role]').forEach(row => {
            row.style.display = (role === 'all' || row.dataset.role === role) ? '' : 'none';
        });
    }

    /* ── MODALS ── */
    function openAdd() {
        isEditMode = false;
        currentUserId = null;
        document.getElementById('modal-title').textContent = 'Add User';
        document.getElementById('modal-sub').textContent   = 'Create a new admin or admission officer account.';
        document.getElementById('f-firstname').value = '';
        document.getElementById('f-lastname').value  = '';
        document.getElementById('f-email').value     = '';
        document.getElementById('f-password').value  = '';
        document.getElementById('f-role').value      = 'admin';
        document.getElementById('f-password-group').style.display = '';
        document.getElementById('modal-error').textContent = '';
        document.getElementById('modal-save-btn').textContent = 'Save User';
        openModal('user-modal');
    }

    function openEdit(id, first, last, email, role) {
        isEditMode = true;
        currentUserId = id;
        document.getElementById('modal-title').textContent = 'Edit User';
        document.getElementById('modal-sub').textContent   = 'Update account details.';
        document.getElementById('f-firstname').value = first;
        document.getElementById('f-lastname').value  = last;
        document.getElementById('f-email').value     = email;
        document.getElementById('f-role').value      = role;
        document.getElementById('f-password-group').style.display = 'none';
        document.getElementById('modal-error').textContent = '';
        document.getElementById('modal-save-btn').textContent = 'Save Changes';
        openModal('user-modal');
    }

    function openPwd(id, name) {
        currentUserId = id;
        document.getElementById('pwd-modal-sub').textContent = 'Resetting password for ' + name;
        document.getElementById('p-password').value  = '';
        document.getElementById('p-confirm').value   = '';
        document.getElementById('pwd-error').textContent = '';
        openModal('pwd-modal');
    }

    function confirmDelete(id, name) {
        currentUserId = id;
        document.getElementById('del-name').textContent = name;
        openModal('del-modal');
    }

    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });

    /* ── SAVE USER ── */
    function saveUser() {
        const btn = document.getElementById('modal-save-btn');
        const errEl = document.getElementById('modal-error');
        errEl.textContent = '';

        const body = new FormData();
        body.append('firstname', document.getElementById('f-firstname').value.trim());
        body.append('lastname',  document.getElementById('f-lastname').value.trim());
        body.append('email',     document.getElementById('f-email').value.trim());
        body.append('role',      document.getElementById('f-role').value);

        if (!isEditMode) {
            const pw = document.getElementById('f-password').value;
            if (pw.length < 8) {
                errEl.textContent = 'Password must be at least 8 characters.';
                return;
            }
            body.append('password', pw);
        }

        const url = isEditMode
            ? BASE + 'admin/users/' + currentUserId + '/update'
            : BASE + 'admin/users/store';

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        fetch(url, { method:'POST', body, headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeModal('user-modal');
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                errEl.textContent = data.message;
                btn.disabled    = false;
                btn.textContent = isEditMode ? 'Save Changes' : 'Save User';
            }
        })
        .catch(() => {
            errEl.textContent = 'Something went wrong.';
            btn.disabled    = false;
            btn.textContent = isEditMode ? 'Save Changes' : 'Save User';
        });
    }

    /* ── SAVE PASSWORD ── */
    function savePassword() {
        const btn   = document.getElementById('pwd-save-btn');
        const errEl = document.getElementById('pwd-error');
        const pw    = document.getElementById('p-password').value;
        const conf  = document.getElementById('p-confirm').value;
        errEl.textContent = '';

        if (pw.length < 8) { errEl.textContent = 'Password must be at least 8 characters.'; return; }
        if (pw !== conf)   { errEl.textContent = 'Passwords do not match.'; return; }

        const body = new FormData();
        body.append('password', pw);

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        fetch(BASE + 'admin/users/' + currentUserId + '/reset-password', {
            method:'POST', body, headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeModal('pwd-modal');
                showToast(data.message, 'success');
            } else {
                errEl.textContent = data.message;
            }
            btn.disabled    = false;
            btn.textContent = 'Reset Password';
        })
        .catch(() => {
            errEl.textContent = 'Something went wrong.';
            btn.disabled    = false;
            btn.textContent = 'Reset Password';
        });
    }

    /* ── DELETE ── */
    function doDelete() {
        const btn = document.getElementById('del-confirm-btn');
        btn.disabled    = true;
        btn.textContent = 'Deleting…';

        fetch(BASE + 'admin/users/' + currentUserId + '/delete', {
            method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeModal('del-modal');
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showToast(data.message, 'error');
                closeModal('del-modal');
            }
            btn.disabled    = false;
            btn.textContent = 'Delete';
        });
    }

    /* ── TOAST ── */
    function showToast(msg, type) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className   = 'toast ' + type;
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => t.classList.remove('show'), 3000);
    }
</script>
</body>
</html>
