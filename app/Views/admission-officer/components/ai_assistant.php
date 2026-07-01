<!-- AKADEMUS | Admission Officer AI Assistant Widget -->
<?php 
    $groqKey = env('api.groqKey');
    $groqURL = env('api.groqURL');
?>

<style>
.ao-fab {
    position: fixed;
    bottom: 28px; right: 28px;
    width: 52px; height: 52px;
    background: var(--primary);
    border-radius: calc(var(--radius) * 0.9);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 6px 24px rgba(0,0,0,0.2);
    z-index: 9998;
    transition: transform 0.2s, background 0.2s;
}
.ao-fab:hover  { background: var(--primary-dark); transform: scale(1.06); }
.ao-fab:active { transform: scale(0.96); }
.ao-fab svg { width: 22px; height: 22px; transition: opacity 0.2s; }
.ao-fab .ao-icon-chat  { opacity: 1; position: absolute; }
.ao-fab .ao-icon-close { opacity: 0; position: absolute; }
.ao-fab.open .ao-icon-chat  { opacity: 0; }
.ao-fab.open .ao-icon-close { opacity: 1; }

.ao-fab-badge {
    position: absolute; top: -4px; right: -4px;
    width: 16px; height: 16px;
    background: var(--accent);
    border-radius: 50%; font-size: 9px; font-weight: 800;
    color: var(--accent-text);
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
    animation: ao-badge-bounce 0.4s ease;
}
@keyframes ao-badge-bounce {
    0%   { transform: scale(0); }
    70%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.ao-panel {
    position: fixed;
    bottom: 92px; right: 28px;
    width: 380px; height: 520px;
    background: #ffffff;
    box-shadow: 0 16px 60px rgba(0,0,0,0.15);
    z-index: 9997;
    display: flex; flex-direction: column;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--radius, 8px) * 1.2);
    transform-origin: bottom right;
    transform: scale(0.85) translateY(20px);
    opacity: 0; pointer-events: none;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s;
}
.ao-panel.open {
    transform: scale(1) translateY(0);
    opacity: 1; pointer-events: all;
}

.ao-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    background: var(--primary);
    flex-shrink: 0;
}
.ao-header-avatar {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.15);
    border-radius: calc(var(--radius, 8px) * 0.7);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}
.ao-header-info p    { font-size: 13px; font-weight: 700; color: #fff; margin: 0; }
.ao-header-info span { font-size: 10.5px; color: rgba(255,255,255,0.65); }
.ao-header-status { width: 6px; height: 6px; background: #4ade80; border-radius: 50%; margin-left: auto; animation: ao-pulse 2s infinite; }
@keyframes ao-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(1.3); }
}

.ao-messages {
    flex: 1; overflow-y: auto;
    padding: 14px;
    display: flex; flex-direction: column; gap: 10px;
    background: #f8fafc;
}
.ao-messages::-webkit-scrollbar { width: 3px; }
.ao-messages::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.ao-msg { display: flex; gap: 8px; align-items: flex-end; max-width: 88%; animation: ao-msg-in 0.2s ease; }
.ao-msg.bot  { align-self: flex-start; }
.ao-msg.user { align-self: flex-end; flex-direction: row-reverse; }
@keyframes ao-msg-in {
    from { opacity: 0; transform: translateY(5px); }
    to   { opacity: 1; transform: translateY(0); }
}

.ao-msg-avatar {
    width: 24px; height: 24px;
    border-radius: calc(var(--radius, 8px) * 0.6);
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; flex-shrink: 0; color: #fff;
}

.ao-bubble {
    padding: 8px 12px;
    border-radius: calc(var(--radius, 8px) * 0.9);
    font-size: 12.5px; line-height: 1.6;
    font-family: var(--font-family, inherit);
}
.ao-msg.bot  .ao-bubble { background: #fff; color: #1a202c; border: 1.5px solid #e2e8f0; border-bottom-left-radius: 3px; }
.ao-msg.user .ao-bubble { background: var(--primary); color: #fff; border-bottom-right-radius: 3px; }

.ao-quick-replies { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; }
.ao-quick-btn {
    padding: 4px 10px; font-size: 11px;
    border: 1.5px solid var(--primary);
    background: transparent; color: var(--primary);
    border-radius: 20px; cursor: pointer; font-weight: 500;
    font-family: var(--font-family, inherit);
    transition: background 0.15s, color 0.15s;
}
.ao-quick-btn:hover { background: var(--primary); color: #fff; }

.ao-typing {
    display: flex; gap: 3px; align-items: center;
    padding: 9px 12px; background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--radius, 8px) * 0.9);
    border-bottom-left-radius: 3px; width: fit-content;
}
.ao-typing-dot { width: 5px; height: 5px; background: var(--primary); border-radius: 50%; animation: ao-typing 1.2s infinite; opacity: 0.4; }
.ao-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.ao-typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes ao-typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30%            { transform: translateY(-5px); opacity: 1; }
}

.ao-input-area {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px; border-top: 1.5px solid #e2e8f0; background: #fff;
    flex-shrink: 0;
}
#ao-input {
    flex: 1; border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--radius, 8px) * 0.8);
    padding: 8px 11px; font-size: 12.5px; color: #1a202c;
    outline: none; resize: none;
    font-family: var(--font-family, inherit);
    transition: border-color 0.2s;
    max-height: 80px; min-height: 36px; line-height: 1.5;
}
#ao-input:focus { border-color: var(--primary); }
#ao-input::placeholder { color: #a0aec0; }
#ao-send-btn {
    width: 36px; height: 36px;
    border-radius: calc(var(--radius, 8px) * 0.8);
    background: var(--primary); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background 0.15s, transform 0.1s;
}
#ao-send-btn:hover  { background: var(--primary-dark); }
#ao-send-btn:active { transform: scale(0.94); }
#ao-send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
#ao-send-btn svg { width: 14px; height: 14px; }
</style>

<!-- FAB -->
<button class="ao-fab" id="aoFab" onclick="aoToggle()" title="AI Assistant">
    <div class="ao-fab-badge" id="aoBadge">1</div>
    <svg class="ao-icon-chat" viewBox="0 0 24 24" fill="none" width="22" height="22">
        <path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.37 5.06L2 22l4.94-1.37A9.96 9.96 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8 10h8M8 14h5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
    <svg class="ao-icon-close" viewBox="0 0 24 24" fill="none" width="22" height="22">
        <path d="M18 6L6 18M6 6l12 12" stroke="white" stroke-width="2" stroke-linecap="round"/>
    </svg>
</button>

<!-- Panel -->
<div class="ao-panel" id="aoPanel">
    <div class="ao-header">
        <div class="ao-header-avatar">✨</div>
        <div class="ao-header-info">
            <p>AI Assistant</p>
            <span><?= esc($theme['app_name'] ?? 'Akademus') ?> · Admission Officer</span>
        </div>
        <div class="ao-header-status"></div>
    </div>

    <div class="ao-messages" id="aoMessages">
        <div class="ao-msg bot">
            <div class="ao-msg-avatar">✨</div>
            <div>
                <div class="ao-bubble">Hi! I'm your AI assistant. I can help you draft messages, review applicant info, write communications, or answer anything you need. 👋</div>
                <div class="ao-quick-replies">
                    <button class="ao-quick-btn" onclick="aoSendQuick(this)">Draft acceptance email</button>
                    <button class="ao-quick-btn" onclick="aoSendQuick(this)">Draft rejection letter</button>
                    <button class="ao-quick-btn" onclick="aoSendQuick(this)">Requirements checklist</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ao-input-area">
        <textarea id="ao-input" placeholder="Ask me anything..." rows="1"
            onkeydown="aoHandleKey(event)" oninput="aoResize(this)"></textarea>
        <button id="ao-send-btn" onclick="aoSend()">
            <svg viewBox="0 0 16 16" fill="none"><path d="M14 8L2 2l2.5 6L2 14l12-6z" fill="white"/></svg>
        </button>
    </div>
</div>

<script>
const AO_SCHOOL   = '<?= esc($theme['app_name'] ?? 'the school') ?>';
const AO_YEAR     = '<?= esc($theme['school_year'] ?? '') ?>';
const AO_STATUS   = '<?= esc($theme['admission_status'] ?? 'Open') ?>';

const AO_SYSTEM = `You are a smart, professional AI assistant helping an Admission Officer at ${AO_SCHOOL}.

Current school year: ${AO_YEAR} | Admission status: ${AO_STATUS}

You can help with anything the officer needs, including:
- Drafting emails, letters, and messages to applicants (acceptance, rejection, waitlist, document requests, etc.)
- Writing formal communications and announcements
- Summarizing or explaining admission policies and procedures
- Answering general knowledge questions
- Brainstorming ideas for improving admission workflows
- Proofreading or improving text the officer provides

Be professional, concise, and practical. When drafting documents, produce ready-to-use text the officer can copy directly. Always address the officer as a colleague.`;

let aoHistory = [];

function aoToggle() {
    const fab   = document.getElementById('aoFab');
    const panel = document.getElementById('aoPanel');
    const badge = document.getElementById('aoBadge');
    fab.classList.toggle('open');
    panel.classList.toggle('open');
    if (badge) badge.remove();
    if (panel.classList.contains('open')) {
        document.getElementById('ao-input').focus();
    }
}

function aoAppend(role, html) {
    const msgs = document.getElementById('aoMessages');
    const div  = document.createElement('div');
    div.className = `ao-msg ${role}`;
    div.innerHTML = `
        ${role === 'bot' ? '<div class="ao-msg-avatar">✨</div>' : ''}
        <div><div class="ao-bubble">${html}</div></div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function aoShowTyping() {
    const msgs = document.getElementById('aoMessages');
    const div  = document.createElement('div');
    div.className = 'ao-msg bot'; div.id = 'ao-typing';
    div.innerHTML = `<div class="ao-msg-avatar">✨</div>
        <div class="ao-typing">
            <div class="ao-typing-dot"></div>
            <div class="ao-typing-dot"></div>
            <div class="ao-typing-dot"></div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function aoRemoveTyping() { const t = document.getElementById('ao-typing'); if (t) t.remove(); }

function aoEsc(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function aoSend(text = null) {
    const input = document.getElementById('ao-input');
    const btn   = document.getElementById('ao-send-btn');
    const msg   = text || input.value.trim();
    if (!msg) return;

    input.value = ''; input.style.height = 'auto';
    btn.disabled = true;

    aoAppend('user', aoEsc(msg).replace(/\n/g, '<br>'));
    aoHistory.push({ role: 'user', content: msg });
    aoShowTyping();

    try {
        btn.disabled = true;

        const res = await fetch('<?= base_url('api/groq/chat') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                messages: [
                    { role: 'system', content: AO_SYSTEM },
                    ...aoHistory
                ]
            })
        });

        if (!res.ok) throw new Error('Server error');

        const data = await res.json();
        aoRemoveTyping();

        const reply =
            data.choices?.[0]?.message?.content ||
            data.error?.message ||
            "Sorry, I couldn't process that.";

        aoAppend('bot', aoEsc(reply).replace(/\n/g, '<br>'));
        aoHistory.push({ role: 'assistant', content: reply });

    } catch (err) {
        aoRemoveTyping();
        aoAppend('bot', 'Connection issue. Please try again.');
    } finally {
        btn.disabled = false;
        input.focus();
    }
}

function aoSendQuick(btn) { aoSend(btn.textContent.trim()); }
function aoHandleKey(e)   { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); aoSend(); } }
function aoResize(el)     { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 80) + 'px'; }
</script>
