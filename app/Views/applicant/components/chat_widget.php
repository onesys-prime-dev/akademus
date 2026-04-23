<!-- AKADEMUS | Floating Chat Widget -->
<!-- partials/chat_widget.php -->
<!-- Add just before </body> in dashboard/main.php -->

<style>
.chat-widget-fab {
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
.chat-widget-fab:hover  { background: var(--primary-dark); transform: scale(1.06); }
.chat-widget-fab:active { transform: scale(0.96); }

.chat-widget-fab svg { width: 22px; height: 22px; transition: opacity 0.2s; }
.chat-widget-fab .icon-chat  { opacity: 1; position: absolute; }
.chat-widget-fab .icon-close { opacity: 0; position: absolute; }
.chat-widget-fab.open .icon-chat  { opacity: 0; }
.chat-widget-fab.open .icon-close { opacity: 1; }

.chat-widget-badge {
    position: absolute; top: -4px; right: -4px;
    width: 16px; height: 16px;
    background: var(--accent-color);
    border-radius: 50%; font-size: 9px; font-weight: 800;
    color: var(--accent-text);
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
    animation: badge-bounce 0.4s ease;
}
@keyframes badge-bounce {
    0%   { transform: scale(0); }
    70%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.chat-widget-panel {
    position: fixed;
    bottom: 92px; right: 28px;
    width: 360px; height: 500px;
    background: #ffffff;
    border: 1px solid var(--primary-light);
    box-shadow: 0 16px 60px rgba(0,0,0,0.15);
    z-index: 9997;
    display: flex; flex-direction: column;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
    transform-origin: bottom right;
    transform: scale(0.85) translateY(20px);
    opacity: 0; pointer-events: none;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s;
}

.chat-widget-panel.open {
    transform: scale(1) translateY(0);
    opacity: 1; pointer-events: all;
}

.widget-chat-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    background: var(--primary);
}

.widget-avatar {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.15);
    border-radius: calc(var(--border-radius) * 0.7);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}

.widget-header-info p    { font-size: 13px; font-weight: 700; color: #fff; margin: 0; }
.widget-header-info span { font-size: 10.5px; color: rgba(255,255,255,0.65); }
.widget-status { width: 6px; height: 6px; background: #4ade80; border-radius: 50%; margin-left: auto; animation: pulse-dot 2s infinite; }

.widget-chat-messages {
    flex: 1; overflow-y: auto;
    padding: 14px;
    display: flex; flex-direction: column; gap: 10px;
    background: #f8fafc;
}
.widget-chat-messages::-webkit-scrollbar { width: 3px; }
.widget-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.w-msg { display: flex; gap: 8px; align-items: flex-end; max-width: 85%; animation: msg-in 0.2s ease; }
.w-msg.bot  { align-self: flex-start; }
.w-msg.user { align-self: flex-end; flex-direction: row-reverse; }

.w-msg-avatar {
    width: 24px; height: 24px;
    border-radius: calc(var(--border-radius) * 0.6);
    background: var(--primary-color);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; flex-shrink: 0;
}

.w-msg-bubble {
    padding: 8px 12px;
    border-radius: calc(var(--border-radius) * 0.9);
    font-size: 12.5px; line-height: 1.55;
    font-family: var(--font-family, inherit);
}
.w-msg.bot  .w-msg-bubble { background: #fff; color: #1a202c; border: 1.5px solid #e2e8f0; border-bottom-left-radius: 3px; }
.w-msg.user .w-msg-bubble {
  background: var(--primary-color);
  color: #000;
  border: 1px solid #e2e8f0;
}
.w-quick-replies { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 5px; }

.w-quick-btn {
    padding: 4px 10px; font-size: 11px;
    border: 1.5px solid var(--primary);
    background: transparent; color: var(--primary);
    border-radius: 20px; cursor: pointer; font-weight: 500;
    font-family: var(--font-family, inherit);
    transition: background 0.15s, color 0.15s;
}
.w-quick-btn:hover { background: var(--primary-color); color: #fff; }

.w-typing {
    display: flex; gap: 3px; align-items: center;
    padding: 9px 12px; background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--border-radius) * 0.9);
    border-bottom-left-radius: 3px; width: fit-content;
}
.w-typing-dot { width: 5px; height: 5px; background: var(--primary-light); border-radius: 50%; animation: typing 1.2s infinite; }
.w-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.w-typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30%            { transform: translateY(-5px); opacity: 1; }
}

.w-contact-card {
    background: #f0f9f4; border: 1px solid #c6f6d5;
    border-radius: calc(var(--border-radius) * 0.7);
    padding: 8px 10px; margin-top: 4px;
    font-size: 11.5px; color: #2d3748;
}
.w-contact-card a {
    display: inline-block; margin-top: 4px;
    font-size: 11px; font-weight: 600; color: var(--primary-color);
    text-decoration: none; border: 1.5px solid var(--primary-color);
    border-radius: calc(var(--border-radius) * 0.6); padding: 3px 10px;
    transition: background 0.15s, color 0.15s;
}
.w-contact-card a:hover { background: var(--primary-color); color: #fff; }

.w-out-of-scope {
    background: #fffbeb; border: 1px solid #fde68a;
    border-radius: calc(var(--border-radius) * 0.7);
    padding: 7px 10px; margin-top: 4px;
    font-size: 11px; color: #92400e;
}

.widget-input-area {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px; border-top: 1.5px solid #e2e8f0; background: #fff;
}

#widget-input {
    flex: 1; border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--border-radius) * 0.8);
    padding: 8px 11px; font-size: 12.5px; color: #1a202c;
    outline: none; resize: none;
    font-family: var(--font-family, inherit);
    transition: border-color 0.2s;
    max-height: 80px; min-height: 36px; line-height: 1.5;
}
#widget-input:focus { border-color: var(--primary-color); }
#widget-input::placeholder { color: #a0aec0; }

#widget-send-btn {
    width: 36px; height: 36px;
    border-radius: calc(var(--border-radius) * 0.8);
    background: var(--primary-color); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background 0.15s, transform 0.1s;
}
#widget-send-btn:hover  { background: var(--primary-dark); }
#widget-send-btn:active { transform: scale(0.94); }
#widget-send-btn svg { width: 14px; height: 14px; }
</style>

<!-- FAB -->
<button class="chat-widget-fab" id="chatWidgetFab" onclick="toggleChatWidget()" title="Admissions Assistant">
    <div class="chat-widget-badge" id="chatBadge">1</div>
    <svg class="icon-chat" viewBox="0 0 24 24" fill="none" width="22" height="22">
        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <svg class="icon-close" viewBox="0 0 24 24" fill="none" width="22" height="22">
        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
</button>
<!-- Panel -->
<div class="chat-widget-panel" id="chatWidgetPanel">
    <div class="widget-chat-header">
        <div class="widget-avatar">🎓</div>
        <div class="widget-header-info">
            <p><?= esc($theme['app_name'] ?? 'Akademus') ?> Assistant</p>
            <span>Admissions Support Only</span>
        </div>
        <div class="widget-status"></div>
    </div>

    <div class="widget-chat-messages" id="widgetMessages">
        <div class="w-msg bot">
            <div class="w-msg-avatar">🎓</div>
            <div>
                <div class="w-msg-bubble">Hi! I can answer questions about admissions, programs, and fees. 👋</div>
                <div class="w-quick-replies">
                    <button class="w-quick-btn" onclick="wSendQuick(this)">Requirements?</button>
                    <button class="w-quick-btn" onclick="wSendQuick(this)">Programs?</button>
                    <button class="w-quick-btn" onclick="wSendQuick(this)">How to apply?</button>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-input-area">
        <textarea id="widget-input" placeholder="Ask about admissions..." rows="1"
            onkeydown="handleWidgetKey(event)" oninput="wAutoResize(this)"></textarea>
        <button id="widget-send-btn" onclick="wSendMessage()">
            <svg viewBox="0 0 16 16" fill="none"><path d="M14 8L2 2l2.5 6L2 14l12-6z" fill="white"/></svg>
        </button>
    </div>
</div>

<script>
function toggleChatWidget() {
    const fab   = document.getElementById('chatWidgetFab');
    const panel = document.getElementById('chatWidgetPanel');
    const badge = document.getElementById('chatBadge');
    fab.classList.toggle('open');
    panel.classList.toggle('open');
    if (badge) badge.remove();
}

// ── Groq config — same key as faq.php ──
const W_GROQ_KEY = 'secret';  
const W_GROQ_URL = 'https://api.groq.com/openai/v1/chat/completions';

const W_SCHOOL_NAME      = '<?= esc($theme['app_name']         ?? 'the school') ?>';
const W_SCHOOL_YEAR      = '<?= esc($theme['school_year']      ?? '') ?>';
const W_ADMISSION_STATUS = '<?= esc($theme['admission_status'] ?? 'Open for Applications') ?>';

const W_SYSTEM = `You are the official admissions assistant for ${W_SCHOOL_NAME}.

You ONLY answer questions about:
- Admission requirements (documents needed, transferee requirements)
- Programs offered
- Application process and steps
- Tuition fees, processing fees, and scholarships
- How to use the ${W_SCHOOL_NAME} admission portal

Current status: ${W_ADMISSION_STATUS} | ${W_SCHOOL_YEAR}

GREETING HANDLING:
- If the user sends a greeting (hi, hello, hey, good morning, etc.), respond warmly and introduce yourself as the ${W_SCHOOL_NAME} AI Assistant, then ask how you can help with their admission questions.
- Greetings are NOT considered out of scope.

STRICT RULE: If the question is NOT related to admissions or this portal, respond EXACTLY with:
"I'm only able to answer questions related to the admission process. For other concerns, please contact the Admissions Office directly."

Keep responses short (2-3 sentences max). Be friendly. Never invent amounts or dates.`;

let wHistory = [];

function wAppend(role, text, showContact = false, outOfScope = false) {
    const msgs = document.getElementById('widgetMessages');
    const div  = document.createElement('div');
    div.className = `w-msg ${role}`;

    let extraHtml = '';
    if (outOfScope) {
        extraHtml = `<div class="w-out-of-scope">⚠️ Outside my scope. Please contact the Admissions Office.</div>`;
    } else if (showContact) {
        extraHtml = `<div class="w-contact-card">Need more help?<br><a href="<?= base_url('/follow-up-messages') ?>">📬 Contact Support</a></div>`;
    }

    div.innerHTML = `
        ${role === 'bot' ? '<div class="w-msg-avatar">🎓</div>' : ''}
        <div>
            <div class="w-msg-bubble">${text.replace(/\n/g, '<br>')}</div>
            ${extraHtml}
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function wShowTyping() {
    const msgs = document.getElementById('widgetMessages');
    const div  = document.createElement('div');
    div.className = 'w-msg bot'; div.id = 'w-typing';
    div.innerHTML = `<div class="w-msg-avatar">🎓</div>
        <div class="w-typing">
            <div class="w-typing-dot"></div><div class="w-typing-dot"></div><div class="w-typing-dot"></div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function wRemoveTyping() { const t = document.getElementById('w-typing'); if (t) t.remove(); }

async function wSendMessage(text = null) {
    const input   = document.getElementById('widget-input');
    const message = text || input.value.trim();
    if (!message) return;

    input.value = ''; input.style.height = 'auto';
    wAppend('user', message);
    wHistory.push({ role: 'user', content: message });
    wShowTyping();

    try {
        const res = await fetch(W_GROQ_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${W_GROQ_KEY}`
            },
            body: JSON.stringify({
                model: 'llama-3.1-8b-instant',
                messages: [
                    { role: 'system', content: W_SYSTEM },
                    ...wHistory
                ],
                temperature: 0.2,
                max_tokens: 256
            })
        });

        const data = await res.json();
        wRemoveTyping();

        const reply = data.choices?.[0]?.message?.content ?? "Sorry, couldn't process that.";
        const isOutOfScope = reply.includes("only able to answer questions related to the admission");
        const needsContact = !isOutOfScope && (
            reply.toLowerCase().includes("admissions office") ||
            reply.toLowerCase().includes("contact") ||
            reply.toLowerCase().includes("cashier")
        );

        wAppend('bot', reply, needsContact, isOutOfScope);
        wHistory.push({ role: 'assistant', content: reply });

    } catch {
        wRemoveTyping();
        wAppend('bot', "Connection issue. Please try again.", true);
    }
}

function wSendQuick(btn) { wSendMessage(btn.textContent.trim()); }
function handleWidgetKey(e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); wSendMessage(); } }
function wAutoResize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 80) + 'px'; }
</script>