<!-- AKADEMUS | FAQ & Chat Support Page -->
<!-- dashboard/menu/faq.php -->

<style>
.faq-page {
    display: flex;
    gap: 28px;
    height: calc(100vh - 120px);
    min-height: 500px;
}

.faq-left {
    flex: 0 0 380px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    overflow-y: auto;
    padding-right: 4px;
}

.faq-left::-webkit-scrollbar { width: 4px; }
.faq-left::-webkit-scrollbar-track { background: transparent; }
.faq-left::-webkit-scrollbar-thumb { background: var(--primary-light); border-radius: 4px; }

.faq-section-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: var(--primary-color);
    text-transform: uppercase;
    padding: 0 4px;
    margin-bottom: 2px;
}

.faq-item {
    background: #fff;
    border: 1.5px solid #e0ede6;
    border-radius: calc(var(--border-radius) * 0.8);
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.faq-item.open {
    border-color: var(--primary);
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

.faq-question {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    cursor: pointer;
    gap: 12px;
    user-select: none;
}

.faq-question span {
    font-size: 13.5px;
    font-weight: 600;
    color: #1a2e22;
    line-height: 1.4;
}

.faq-chevron {
    flex-shrink: 0;
    width: 20px; height: 20px;
    border-radius: 50%;
    background: rgba(0,0,0,0.04);
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s, transform 0.3s;
}

.faq-item.open .faq-chevron { background: var(--primary); transform: rotate(180deg); }
.faq-chevron svg { width: 12px; height: 12px; }
.faq-chevron svg path { stroke: var(--primary-color); }
.faq-item.open .faq-chevron svg path { stroke: #fff; }

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, padding 0.2s;
    padding: 0 16px;
}

.faq-item.open .faq-answer { max-height: 300px; padding: 0 16px 14px; }
.faq-answer p { font-size: 13px; color: #4a5568; line-height: 1.65; margin: 0; }

.faq-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1.5px solid #e0ede6;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.chat-header {
    display: flex; align-items: center; gap: 12px;
    padding: 16px 20px;
    background: var(--primary);
}

.chat-avatar {
    width: 38px; height: 38px;
    background: rgba(255,255,255,0.15);
    border-radius: calc(var(--border-radius) * 0.7);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}

.chat-header-info p  { font-size: 14px; font-weight: 700; color: #fff; margin: 0; }
.chat-header-info span { font-size: 11px; color: rgba(255,255,255,0.65); }

.chat-status-dot {
    width: 7px; height: 7px;
    background: #4ade80; border-radius: 50%;
    margin-left: auto;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 2px rgba(74,222,128,0.25); }
    50%       { box-shadow: 0 0 0 5px rgba(74,222,128,0.1); }
}

.chat-messages {
    flex: 1; overflow-y: auto;
    padding: 20px;
    display: flex; flex-direction: column; gap: 14px;
    background: #f8fafc;
}

.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }

.msg {
    display: flex; gap: 10px; align-items: flex-end;
    max-width: 82%;
    animation: msg-in 0.25s ease;
}

@keyframes msg-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.msg.bot  { align-self: flex-start; }
.msg.user { align-self: flex-end; flex-direction: row-reverse; }

.msg-avatar {
    width: 28px; height: 28px;
    border-radius: calc(var(--border-radius) * 0.6);
    background: var(--primary-color);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}

.msg-bubble {
    padding: 10px 14px;
    border-radius: calc(var(--border-radius) * 0.9);
    font-size: 13.5px; line-height: 1.6;
}

.msg.bot  .msg-bubble { background: #fff; color: #1a202c; border: 1.5px solid #e2e8f0; border-bottom-left-radius: 4px; }
.msg.user .msg-bubble { background:  var(--primary-dark); color: #fff; border-bottom-right-radius: 4px; }
.msg-time { font-size: 10px; color: #a0aec0; margin-top: 3px; padding: 0 4px; }

.typing-indicator {
    display: flex; gap: 4px; align-items: center;
    padding: 12px 14px; background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--border-radius) * 0.9);
    border-bottom-left-radius: 4px;
    width: fit-content;
}

.typing-dot {
    width: 6px; height: 6px;
    background: var(--primary-light); border-radius: 50%;
    animation: typing 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30%            { transform: translateY(-5px); opacity: 1; }
}

.quick-replies { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }

.quick-reply-btn {
    padding: 5px 12px; font-size: 12px;
    border: 1.5px solid var(--primary);
    background: transparent; color: var(--primary);
    border-radius: 20px; cursor: pointer; font-weight: 500;
    font-family: var(--font-family, inherit);
    transition: background 0.15s, color 0.15s;
}
.quick-reply-btn:hover { background: var(--primary-color); color: #fff; }

.chat-input-area {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    border-top: 1.5px solid #e2e8f0;
    background: #fff;
}

#chat-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: calc(var(--border-radius) * 0.8);
    padding: 10px 14px; font-size: 13.5px; color: #1a202c;
    outline: none; resize: none;
    font-family: var(--font-family, inherit);
    transition: border-color 0.2s;
    max-height: 100px; min-height: 40px; line-height: 1.5;
}
#chat-input:focus { border-color: var(--primary); }
#chat-input::placeholder { color: #a0aec0; }

#chat-send-btn {
    width: 40px; height: 40px;
    border-radius: calc(var(--border-radius) * 0.8);
    background: var(--primary); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background 0.15s, transform 0.1s;
}
#chat-send-btn:hover  { background: var(--primary-dark); }
#chat-send-btn:active { transform: scale(0.95); }
#chat-send-btn svg { width: 16px; height: 16px; }

.contact-support-card {
    background: #f0f9f4; border: 1.5px solid #c6f6d5;
    border-radius: calc(var(--border-radius) * 0.8);
    padding: 12px 14px; margin-top: 4px;
}
.contact-support-card p { font-size: 13px; color: #2d3748; margin: 0 0 8px; font-weight: 500; }
.contact-support-card a {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; color: var(--primary-color); font-weight: 600;
    text-decoration: none; padding: 5px 12px;
    border: 1.5px solid var(--primary-color);
    border-radius: calc(var(--border-radius) * 0.6);
    transition: background 0.15s, color 0.15s;
}
.contact-support-card a:hover { background: var(--primary-color); color: #fff; }

.out-of-scope {
    background: #fffbeb; border: 1.5px solid #fde68a;
    border-radius: calc(var(--border-radius) * 0.8);
    padding: 10px 13px; margin-top: 4px;
    font-size: 12.5px; color: #92400e;
}
</style>

<div class="faq-page">

    <!-- LEFT: FAQ Accordion -->
    <div class="faq-left">
        <p class="faq-section-title">📋 Admission Requirements</p>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>What documents do I need to submit?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>You need to submit: (1) Original Form 138 / Report Card, (2) PSA Birth Certificate, (3) Certificate of Good Moral Character, (4) 2x2 ID photo, and (5) Accomplished application form. Additional documents may be required depending on your applicant type.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>What are the requirements for transferees?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>Transferees must submit: Transcript of Records (TOR) or True Copy of Grades, Certificate of Transfer Credential (Honorable Dismissal), and all standard admission requirements. A validation exam may also be required.</p>
            </div>
        </div>

        <p class="faq-section-title">🎓 Programs Offered</p>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>What programs are available for enrollment?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>The school offers programs across various colleges. Please check with the Registrar or Admissions Office for the complete and updated list of programs currently open for enrollment.</p>
            </div>
        </div>

        <p class="faq-section-title">📅 Application Process</p>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How does the application process work?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>The process is: (1) Create an account and fill out the online application form, (2) Submit required documents, (3) Pay the admission processing fee, (4) Take the entrance exam (if applicable), (5) Await results and enrollment schedule.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>When is the application deadline?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>Application deadlines vary per semester. Please monitor announcements on the portal or contact the Admissions Office directly for the latest schedule.</p>
            </div>
        </div>

        <p class="faq-section-title">💰 Tuition & Fees</p>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How much is the admission processing fee?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>The admission processing fee varies by program. Please check the Payments section of the portal or contact the Cashier's Office for the exact amount.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Are there available scholarships?</span>
                <div class="faq-chevron"><svg viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="faq-answer">
                <p>Yes, scholarships are available including academic, athletic, and financial assistance grants. Contact the Scholarship Office or Student Affairs for eligibility details.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT: AI Chat -->
    <div class="faq-right">
        <div class="chat-header">
            <div class="chat-avatar">🎓</div>
            <div class="chat-header-info">
                <p><?= esc($theme['app_name'] ?? 'Akademus') ?> AI Assistant</p>
                <span>Admissions Support · <?= esc($theme['school_year'] ?? '') ?></span>
            </div>
            <div class="chat-status-dot"></div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="msg bot">
                <div class="msg-avatar">🎓</div>
                <div>
                    <div class="msg-bubble">
                        Hi! I'm the <?= esc($theme['app_name'] ?? 'Akademus') ?> AI Assistant. I can answer questions about admission requirements, programs offered, the application process, and fees. How can I help you?
                    </div>
                    <div class="quick-replies">
                        <button class="quick-reply-btn" onclick="sendQuickReply(this)">What are the requirements?</button>
                        <button class="quick-reply-btn" onclick="sendQuickReply(this)">Programs offered?</button>
                        <button class="quick-reply-btn" onclick="sendQuickReply(this)">Application deadline?</button>
                        <button class="quick-reply-btn" onclick="sendQuickReply(this)">Tuition fees?</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-input-area">
            <textarea id="chat-input" placeholder="Ask about admissions, programs, fees..." rows="1"
                onkeydown="handleChatKey(event)" oninput="autoResize(this)"></textarea>
            <button id="chat-send-btn" onclick="sendMessage()">
                <svg viewBox="0 0 16 16" fill="none"><path d="M14 8L2 2l2.5 6L2 14l12-6z" fill="white"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
// ── FAQ Accordion ──
function toggleFaq(el) {
    const item   = el.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}

// ── Dynamic tenant identity from PHP $theme ──
const SCHOOL_NAME      = '<?= esc($theme['app_name']         ?? 'the school') ?>';
const SCHOOL_YEAR      = '<?= esc($theme['school_year']      ?? '') ?>';
const ADMISSION_STATUS = '<?= esc($theme['admission_status'] ?? 'Open for Applications') ?>';

// ── Groq config — paste your key from console.groq.com ──
const FAQ_GROQ_KEY = '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';
const FAQ_GROQ_URL = 'https://api.groq.com/openai/v1/chat/completions';

// ── Strict admission-only system prompt ──
const SYSTEM_PROMPT = `You are the official admissions assistant for ${SCHOOL_NAME}, an online admission portal system.

YOUR ROLE:
You ONLY answer questions directly related to the admission process of ${SCHOOL_NAME}. Nothing else.

WHAT YOU CAN ANSWER:
1. ADMISSION REQUIREMENTS
   - Freshmen: Form 138/Report Card, PSA Birth Certificate, Certificate of Good Moral Character, 2x2 ID photo, accomplished application form
   - Transferees: TOR or True Copy of Grades, Certificate of Transfer Credential (Honorable Dismissal), plus all standard requirements
   - Additional documents may be required per applicant type

2. PROGRAMS OFFERED
   - The school offers programs across various colleges
   - For the complete and updated list, direct to the Admissions Office or Registrar

3. APPLICATION PROCESS
   Step 1: Create an account on the ${SCHOOL_NAME} portal
   Step 2: Fill out the online application form
   Step 3: Submit required documents
   Step 4: Pay the admission processing fee
   Step 5: Take the entrance exam (if required by the program)
   Step 6: Await results and enrollment schedule
   Current admission status: ${ADMISSION_STATUS} | ${SCHOOL_YEAR}

4. TUITION AND FEES
   - Fees vary by program — direct to Cashier's Office for exact amounts
   - Reservation fee is required after acceptance
   - Scholarships available: academic merit, athletic, financial assistance grants
   - Contact the Scholarship Office for eligibility requirements

5. PORTAL NAVIGATION
   - How to use the Akademus portal, submit documents, track application status, pay fees online

STRICT RULES:
- If the user sends a greeting (hi, hello, hey, good morning, etc.), respond warmly and introduce yourself as the ${SCHOOL_NAME} AI Assistant, then ask how you can help with their admission questions.
- Greetings are NOT considered out of scope.
- If the question is NOT about admissions, programs, fees, or the application process, respond EXACTLY with: "I'm only able to answer questions related to the admission process. For other concerns, please contact the Admissions Office directly."
- NEVER answer questions about general knowledge, news, math, coding, other schools, or anything unrelated to this admission system
- NEVER make up specific amounts, exact dates, or program names you are not certain about
- Always recommend contacting the Admissions Office for questions requiring specific or official confirmation
- Keep answers concise, friendly, and easy to understand
- Use numbered lists when explaining multi-step processes`;

let conversationHistory = [];
// Start chat scrolled to top so welcome message is visible
document.getElementById('chatMessages').scrollTop = 0;
function getTime() {
    return new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function appendMessage(role, text, showContact = false, outOfScope = false) {
    const msgs = document.getElementById('chatMessages');
    const div  = document.createElement('div');
    div.className = `msg ${role}`;

    let extraHtml = '';
    if (outOfScope) {
        extraHtml = `<div class="out-of-scope">⚠️ This question is outside my scope. Please contact the Admissions Office for assistance.</div>`;
    } else if (showContact) {
        extraHtml = `
        <div class="contact-support-card">
            <p>Need further assistance?</p>
            <a href="<?= base_url('/follow-up-messages') ?>">📬 Contact Support</a>
        </div>`;
    }

    div.innerHTML = `
        ${role === 'bot' ? '<div class="msg-avatar">🎓</div>' : ''}
        <div>
            <div class="msg-bubble">${text.replace(/\n/g, '<br>')}</div>
            ${extraHtml}
            <div class="msg-time">${getTime()}</div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function showTyping() {
    const msgs = document.getElementById('chatMessages');
    const div  = document.createElement('div');
    div.className = 'msg bot';
    div.id = 'typing-indicator';
    div.innerHTML = `
        <div class="msg-avatar">🎓</div>
        <div class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function removeTyping() {
    const t = document.getElementById('typing-indicator');
    if (t) t.remove();
}

async function sendMessage(text = null) {
    const input   = document.getElementById('chat-input');
    const message = text || input.value.trim();
    if (!message) return;

    input.value = '';
    input.style.height = 'auto';

    appendMessage('user', message);
    conversationHistory.push({ role: 'user', content: message });
    showTyping();

    try {
        const res = await fetch(FAQ_GROQ_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${FAQ_GROQ_KEY}`
            },
            body: JSON.stringify({
                model: 'llama-3.1-8b-instant',
                messages: [
                    { role: 'system', content: SYSTEM_PROMPT },
                    ...conversationHistory
                ],
                temperature: 0.2,
                max_tokens: 512
            })
        });

        const data = await res.json();
        removeTyping();

        const reply = data.choices?.[0]?.message?.content
            ?? "Sorry, I couldn't process that. Please try again.";

        const isOutOfScope = reply.includes("only able to answer questions related to the admission");
        const needsContact = !isOutOfScope && (
            reply.toLowerCase().includes("admissions office") ||
            reply.toLowerCase().includes("contact") ||
            reply.toLowerCase().includes("cashier") ||
            reply.toLowerCase().includes("registrar")
        );

        appendMessage('bot', reply, needsContact, isOutOfScope);
        conversationHistory.push({ role: 'assistant', content: reply });

    } catch (err) {
        removeTyping();
        appendMessage('bot', "I'm having trouble connecting right now. Please try again shortly.", true);
    }
}

function sendQuickReply(btn) { sendMessage(btn.textContent.trim()); }

function handleChatKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}
</script>