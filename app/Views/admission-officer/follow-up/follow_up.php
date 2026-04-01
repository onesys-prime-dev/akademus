<!-- AKADEMUS | Follow-Up Messages (Admission Officer) -->

<style>
.followup-officer-page {
    display: flex;
    gap: 0;
    height: calc(100vh - 140px);
    min-height: 500px;
    background: #fff;
    border: 1.5px solid #e0ede6;
    border-radius: var(--border-radius);
    overflow: hidden;
}

/* ── Left: Conversation List ─────────────────────────── */
.fo-sidebar {
    width: 300px;
    flex-shrink: 0;
    border-right: 1.5px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
}

.fo-sidebar-header {
    padding: 16px;
    border-bottom: 1.5px solid #e2e8f0;
    background: #fff;
}

.fo-sidebar-header h3 {
    font-size: 13px;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.fo-sidebar-header span {
    font-size: 11px;
    color: #718096;
}

.fo-conversations {
    flex: 1;
    overflow-y: auto;
}

.fo-conversations::-webkit-scrollbar { width: 3px; }
.fo-conversations::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.fo-conv-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-bottom: 1px solid #edf2f7;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}

.fo-conv-item:hover   { background: #edf2f7; }
.fo-conv-item.active  { background: #e8f4ed; border-left: 3px solid var(--primary); }

.fo-conv-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}

.fo-conv-info { flex: 1; min-width: 0; }

.fo-conv-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a202c;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fo-conv-preview {
    font-size: 11.5px;
    color: #718096;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}

.fo-conv-badge {
    width: 18px; height: 18px;
    background: var(--primary);
    border-radius: 50%;
    font-size: 10px; font-weight: 800;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.fo-empty-convs {
    padding: 32px 16px;
    text-align: center;
    color: #a0aec0;
    font-size: 13px;
}

/* ── Right: Chat Panel ───────────────────────────────── */
.fo-chat-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.fo-chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1.5px solid #e2e8f0;
    background: #fff;
    flex-shrink: 0;
}

.fo-chat-header-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}

.fo-chat-header-info p    { font-size: 13.5px; font-weight: 700; color: #1a202c; margin: 0; }
.fo-chat-header-info span { font-size: 11px; color: #718096; }

.fo-view-applicant-btn {
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    background: var(--primary-soft, #e8f0ff);
    color: var(--primary, #0040a6);
    border: 1.5px solid var(--primary, #0040a6);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background 0.15s, color 0.15s;
    flex-shrink: 0;
}
.fo-view-applicant-btn:hover {
    background: var(--primary, #0040a6);
    color: #fff;
}

.fo-chat-placeholder {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #a0aec0;
    font-size: 13.5px;
    background: #f8fafc;
}

.fo-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #f8fafc;
}

.fo-chat-messages::-webkit-scrollbar { width: 4px; }
.fo-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.fo-msg {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    max-width: 75%;
    animation: fo-msg-in 0.2s ease;
}

@keyframes fo-msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.fo-msg.applicant { align-self: flex-start; }
.fo-msg.me        { align-self: flex-end; flex-direction: row-reverse; }

.fo-msg-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}

.fo-msg.me .fo-msg-avatar {
    background: #4a5568;
}

.fo-msg-bubble {
    padding: 10px 14px;
    border-radius: calc(var(--border-radius) * 0.9);
    font-size: 13.5px;
    line-height: 1.6;
}

.fo-msg.applicant .fo-msg-bubble {
    background: #fff;
    color: #1a202c;
    border: 1.5px solid #e2e8f0;
    border-bottom-left-radius: 3px;
}

.fo-msg.me .fo-msg-bubble {
    background: var(--primary);
    color: #fff;
    border-bottom-right-radius: 3px;
}

.fo-msg-time {
    font-size: 10px;
    color: #a0aec0;
    margin-top: 3px;
    padding: 0 4px;
}

.fo-input-area {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-top: 1.5px solid #e2e8f0;
    background: #fff;
    flex-shrink: 0;
}

#fo-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--border-radius) * 0.8);
    padding: 10px 14px;
    font-size: 13.5px;
    color: #1a202c;
    outline: none;
    resize: none;
    font-family: var(--font-family, inherit);
    transition: border-color 0.2s;
    max-height: 100px;
    min-height: 40px;
    line-height: 1.5;
}
#fo-input:focus { border-color: var(--primary); }
#fo-input::placeholder { color: #a0aec0; }
#fo-input:disabled { background: #f8fafc; cursor: not-allowed; }

#fo-send-btn {
    width: 40px; height: 40px;
    border-radius: calc(var(--border-radius) * 0.8);
    background: var(--primary);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background 0.15s, transform 0.1s;
}
#fo-send-btn:hover  { background: var(--primary-dark); }
#fo-send-btn:active { transform: scale(0.95); }
#fo-send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
#fo-send-btn svg { width: 16px; height: 16px; }
</style>

<div class="followup-officer-page">

    <!-- Left: Conversation list -->
    <div class="fo-sidebar">
        <div class="fo-sidebar-header">
            <h3>Conversations</h3>
            <span id="fo-conv-count">
                <?= count($conversations) ?> applicant<?= count($conversations) !== 1 ? 's' : '' ?>
            </span>
        </div>

        <div class="fo-conversations" id="foConversations">
            <?php if (empty($conversations)): ?>
            <div class="fo-empty-convs">No messages yet.</div>
            <?php else: ?>
            <?php foreach ($conversations as $conv): ?>
            <?php
                $initials = strtoupper(substr($conv['firstname'] ?? 'A', 0, 1) . substr($conv['lastname'] ?? '', 0, 1));
                $isActive = isset($activeApplicant) && (int)$activeApplicant['applicant_id'] === (int)$conv['applicant_id'];
            ?>
            <a href="<?= base_url('/admission-officer/follow-up/' . $conv['applicant_id']) ?>"
               class="fo-conv-item <?= $isActive ? 'active' : '' ?>">
                <div class="fo-conv-avatar"><?= esc($initials) ?></div>
                <div class="fo-conv-info">
                    <div class="fo-conv-name"><?= esc($conv['firstname'] . ' ' . $conv['lastname']) ?></div>
                    <div class="fo-conv-preview"><?= esc(mb_strimwidth($conv['last_message'] ?? '', 0, 40, '...')) ?></div>
                </div>
                <?php if ($conv['unread_count'] > 0): ?>
                <div class="fo-conv-badge"><?= (int)$conv['unread_count'] ?></div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Chat panel -->
    <div class="fo-chat-panel">

        <?php if (!isset($activeApplicant)): ?>
        <!-- Placeholder when no conversation selected -->
        <div class="fo-chat-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0" stroke-width="1.5">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z"/>
            </svg>
            <p>Select a conversation to view messages</p>
        </div>

        <?php else: ?>
        <?php
            $aInitials = strtoupper(substr($activeApplicant['firstname'] ?? 'A', 0, 1) . substr($activeApplicant['lastname'] ?? '', 0, 1));
        ?>
        <!-- Active conversation header -->
        <div class="fo-chat-header">
            <div class="fo-chat-header-avatar"><?= esc($aInitials) ?></div>
            <div class="fo-chat-header-info">
                <p><?= esc($activeApplicant['firstname'] . ' ' . $activeApplicant['lastname']) ?></p>
                <span>Applicant #<?= esc($activeApplicant['applicant_number'] ?? '') ?></span>
            </div>
            <a href="<?= base_url('admission-officer/detailed-applicant/' . (int)$activeApplicant['applicant_id']) ?>"
               class="fo-view-applicant-btn" target="_blank">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                View Applicant
            </a>
        </div>

        <!-- Messages -->
        <div class="fo-chat-messages" id="foMessages">
            <?php foreach ($messages as $msg): ?>
            <?php $isOfficer = $msg['sender_role'] === 'officer'; ?>
            <div class="fo-msg <?= $isOfficer ? 'me' : 'applicant' ?>">
                <div class="fo-msg-avatar">
                    <?= $isOfficer ? 'You' : esc($aInitials) ?>
                </div>
                <div>
                    <div class="fo-msg-bubble"><?= nl2br(esc($msg['message'])) ?></div>
                    <div class="fo-msg-time"><?= date('M j, g:i A', strtotime($msg['created_at'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Input -->
        <div class="fo-input-area">
            <textarea id="fo-input" placeholder="Reply to <?= esc($activeApplicant['firstname']) ?>..." rows="1"
                onkeydown="foHandleKey(event)" oninput="foAutoResize(this)"></textarea>
            <button id="fo-send-btn" onclick="foSend()">
                <svg viewBox="0 0 16 16" fill="none"><path d="M14 8L2 2l2.5 6L2 14l12-6z" fill="white"/></svg>
            </button>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
<?php if (isset($activeApplicant)): ?>
window.foConfig = {
    applicantId:   <?= (int) $activeApplicant['applicant_id'] ?>,
    sendUrl:       '<?= base_url('/admission-officer/follow-up/send') ?>',
    fetchUrl:      '<?= base_url('/admission-officer/follow-up/messages') ?>',
    applicantInit: '<?= esc($aInitials) ?>',
};

// Track last known message_id — poll only fetches new rows, not all messages.
let foLastId = <?= !empty($messages) ? (int) end($messages)['message_id'] : 0 ?>;

function foEsc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function foFormatTime(dateStr) {
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
}

function foAppend(msg) {
    const wrap      = document.getElementById('foMessages');
    const isOfficer = msg.sender_role === 'officer';
    const cls       = isOfficer ? 'me' : 'applicant';
    const avatar    = isOfficer ? 'You' : window.foConfig.applicantInit;

    const div = document.createElement('div');
    div.className = `fo-msg ${cls}`;
    div.innerHTML = `
        <div class="fo-msg-avatar">${avatar}</div>
        <div>
            <div class="fo-msg-bubble">${foEsc(msg.message).replace(/\n/g, '<br>')}</div>
            <div class="fo-msg-time">${foFormatTime(msg.created_at)}</div>
        </div>`;
    wrap.appendChild(div);
    wrap.scrollTop = wrap.scrollHeight;
}

async function foSend() {
    const input = document.getElementById('fo-input');
    const btn   = document.getElementById('fo-send-btn');
    const msg   = input.value.trim();
    if (!msg) return;

    btn.disabled   = true;
    input.disabled = true;
    input.value    = '';
    input.style.height = 'auto';

    const form = new FormData();
    form.append('applicant_id', window.foConfig.applicantId);
    form.append('message', msg);

    try {
        const res  = await fetch(window.foConfig.sendUrl, { method: 'POST', body: form });
        const data = await res.json();

        if (data.success) {
            foAppend({ sender_role: 'officer', message: msg, created_at: new Date().toISOString().replace('T', ' ').slice(0, 19) });
            foLastId = data.message_id; // Advance pointer so poll won't re-fetch our own message
        }
    } catch (e) {
        input.value = msg;
    } finally {
        btn.disabled   = false;
        input.disabled = false;
        input.focus();
    }
}

async function foPoll() {
    try {
        const res  = await fetch(`${window.foConfig.fetchUrl}?applicant_id=${window.foConfig.applicantId}&since_id=${foLastId}`);
        const data = await res.json();

        if (data.success && data.messages.length > 0) {
            data.messages.forEach(foAppend);
            foLastId = data.messages[data.messages.length - 1].message_id;
        }
    } catch (e) { /* silent */ }
}

function foHandleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); foSend(); }
}

function foAutoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

document.getElementById('foMessages').scrollTop = 9999;
setInterval(foPoll, 8000);
<?php endif; ?>
</script>
