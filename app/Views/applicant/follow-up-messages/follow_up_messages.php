<!-- AKADEMUS | Follow-Up Messages (Applicant) -->

<style>
.followup-page {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
    min-height: 480px;
    background: #fff;
    border: 1.5px solid #e0ede6;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.followup-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: var(--primary);
    flex-shrink: 0;
}

.followup-avatar {
    width: 38px; height: 38px;
    background: rgba(255,255,255,0.15);
    border-radius: calc(var(--border-radius) * 0.7);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}

.followup-header-info p    { font-size: 14px; font-weight: 700; color: #fff; margin: 0; }
.followup-header-info span { font-size: 11px; color: rgba(255,255,255,0.65); }

.followup-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #f8fafc;
}

.followup-messages::-webkit-scrollbar { width: 4px; }
.followup-messages::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.fu-empty {
    margin: auto;
    text-align: center;
    color: #a0aec0;
    font-size: 13.5px;
}

.fu-empty p { margin: 8px 0 0; }

.fu-msg {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    max-width: 75%;
    animation: msg-in 0.2s ease;
}

@keyframes msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.fu-msg.officer { align-self: flex-start; }
.fu-msg.me      { align-self: flex-end; flex-direction: row-reverse; }

.fu-msg-avatar {
    width: 28px; height: 28px;
    border-radius: calc(var(--border-radius) * 0.6);
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; flex-shrink: 0; color: #fff;
    font-weight: 700;
}

.fu-msg.me .fu-msg-avatar {
    background: #e2e8f0;
    color: #4a5568;
}

.fu-msg-bubble {
    padding: 10px 14px;
    border-radius: calc(var(--border-radius) * 0.9);
    font-size: 13.5px;
    line-height: 1.6;
}

.fu-msg.officer .fu-msg-bubble {
    background: #fff;
    color: #1a202c;
    border: 1.5px solid #e2e8f0;
    border-bottom-left-radius: 3px;
}

.fu-msg.me .fu-msg-bubble {
    background: var(--primary);
    color: #fff;
    border-bottom-right-radius: 3px;
}

.fu-msg-time {
    font-size: 10px;
    color: #a0aec0;
    margin-top: 3px;
    padding: 0 4px;
}

.followup-input-area {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-top: 1.5px solid #e2e8f0;
    background: #fff;
    flex-shrink: 0;
}

#fu-input {
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
#fu-input:focus { border-color: var(--primary); }
#fu-input::placeholder { color: #a0aec0; }

#fu-send-btn {
    width: 40px; height: 40px;
    border-radius: calc(var(--border-radius) * 0.8);
    background: var(--primary);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background 0.15s, transform 0.1s;
}
#fu-send-btn:hover  { background: var(--primary-dark); }
#fu-send-btn:active { transform: scale(0.95); }
#fu-send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
#fu-send-btn svg { width: 16px; height: 16px; }
</style>

<div class="followup-page">

    <!-- Header -->
    <div class="followup-header">
        <div class="followup-avatar">🎓</div>
        <div class="followup-header-info">
            <p>Admissions Office</p>
            <span>Send a message · we'll reply as soon as possible</span>
        </div>
    </div>

    <!-- Messages -->
    <div class="followup-messages" id="fuMessages">
        <?php if (empty($messages)): ?>
        <div class="fu-empty" id="fuEmpty">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0" stroke-width="1.5">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z"/>
            </svg>
            <p>No messages yet. Send your first message below.</p>
        </div>
        <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="fu-msg <?= $msg['sender_role'] === 'applicant' ? 'me' : 'officer' ?>">
                <div class="fu-msg-avatar">
                    <?= $msg['sender_role'] === 'officer' ? '🎓' : esc(strtoupper(substr(session()->get('firstname') ?? 'A', 0, 1))) ?>
                </div>
                <div>
                    <div class="fu-msg-bubble"><?= nl2br(esc($msg['message'])) ?></div>
                    <div class="fu-msg-time"><?= date('M j, g:i A', strtotime($msg['created_at'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Input -->
    <div class="followup-input-area">
        <textarea id="fu-input" placeholder="Type your message..." rows="1"
            onkeydown="fuHandleKey(event)" oninput="fuAutoResize(this)"></textarea>
        <button id="fu-send-btn" onclick="fuSend()">
            <svg viewBox="0 0 16 16" fill="none"><path d="M14 8L2 2l2.5 6L2 14l12-6z" fill="white"/></svg>
        </button>
    </div>
</div>

<script>
window.fuConfig = {
    sendUrl:  '<?= base_url('/follow-up-messages/send') ?>',
    fetchUrl: '<?= base_url('/follow-up-messages/fetch') ?>',
    userInit: '<?= esc(strtoupper(substr(session()->get('firstname') ?? 'A', 0, 1))) ?>',
};

let fuLastCount = <?= count($messages) ?>;

function fuFormatTime(dateStr) {
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
}

function fuAppend(msg) {
    const empty = document.getElementById('fuEmpty');
    if (empty) empty.remove();

    const wrap   = document.getElementById('fuMessages');
    const cls    = msg.sender_role === 'applicant' ? 'me' : 'officer';
    const avatar = msg.sender_role === 'officer' ? '🎓' : window.fuConfig.userInit;

    const div = document.createElement('div');
    div.className = `fu-msg ${cls}`;
    div.innerHTML = `
        <div class="fu-msg-avatar">${avatar}</div>
        <div>
            <div class="fu-msg-bubble">${msg.message.replace(/\n/g, '<br>')}</div>
            <div class="fu-msg-time">${fuFormatTime(msg.created_at)}</div>
        </div>`;
    wrap.appendChild(div);
    wrap.scrollTop = wrap.scrollHeight;
}

async function fuSend() {
    const input = document.getElementById('fu-input');
    const btn   = document.getElementById('fu-send-btn');
    const msg   = input.value.trim();
    if (!msg) return;

    btn.disabled = true;
    input.value  = '';
    input.style.height = 'auto';

    const form = new FormData();
    form.append('message', msg);

    try {
        const res  = await fetch(window.fuConfig.sendUrl, { method: 'POST', body: form });
        const data = await res.json();

        if (data.success) {
            fuAppend({ sender_role: 'applicant', message: msg, created_at: new Date().toISOString().replace('T', ' ').slice(0, 19) });
            fuLastCount++;
        }
    } catch (e) {
        input.value = msg;
    } finally {
        btn.disabled = false;
        input.focus();
    }
}

async function fuPoll() {
    try {
        const res  = await fetch(window.fuConfig.fetchUrl);
        const data = await res.json();

        if (data.success && data.messages.length > fuLastCount) {
            data.messages.slice(fuLastCount).forEach(fuAppend);
            fuLastCount = data.messages.length;
        }
    } catch (e) { /* silent */ }
}

function fuHandleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); fuSend(); }
}

function fuAutoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

document.getElementById('fuMessages').scrollTop = 9999;
setInterval(fuPoll, 2000);
</script>
