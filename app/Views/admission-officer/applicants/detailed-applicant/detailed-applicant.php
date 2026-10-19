<?php
$appStatus      = $profile['application_status'] ?? 'pending';
$decisionMade   = in_array($appStatus, ['approved', 'rejected', 'enrolled']);
$isApproved     = in_array($appStatus, ['approved', 'enrolled']);
$isRejected     = $appStatus === 'rejected';
$decisionRemarks = $profile['decision_remarks'] ?? null;

$formStatus      = $profile['form_status'] ?? null;
$revisionPending = $formStatus === 'needs_revision';
?>

<div class="applicants card">
    <div class="applicants__top-actions">
        <div class="applicants__top-actions--back">
            <a href="<?= base_url('admission-officer/applicants') ?>" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:inherit;">
                <img src="<?= base_url('assets/images/icons/less-than.svg') ?>" alt="">
                <span>Applicant Profile</span>
            </a>
        </div>

        <div class="decision-actions">
            <!-- Message button — always visible -->
            <button class="decision-btn decision-btn--message" id="btn-message">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Message
            </button>

            <?php if (!$decisionMade): ?>
                <?php if ($revisionPending): ?>
                    <div class="decision-badge decision-badge--revision">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        Revision Requested
                    </div>
                <?php else: ?>
                    <button class="decision-btn decision-btn--revision" id="btn-revision">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        Request Revision
                    </button>
                <?php endif; ?>
                <!-- Decision buttons — only before a decision -->
                <button class="decision-btn decision-btn--decline" id="btn-decline">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Decline
                </button>
                <button class="decision-btn decision-btn--approve" id="btn-approve">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Approve
                </button>
            <?php else: ?>
                <!-- Decision badge — shows after a decision is made -->
                <div class="decision-badge decision-badge--<?= $isApproved ? 'approved' : 'rejected' ?>">
                    <?php if ($isApproved): ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $appStatus === 'enrolled' ? 'Enrolled' : 'Approved' ?>
                    <?php else: ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Declined
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($decisionMade && $decisionRemarks): ?>
    <!-- Remarks banner — shown after a decision -->
    <div class="decision-remarks-banner decision-remarks-banner--<?= $isApproved ? 'approved' : 'rejected' ?>">
        <div class="decision-remarks-banner__icon">
            <?php if ($isApproved): ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <?php else: ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?php endif; ?>
        </div>
        <div class="decision-remarks-banner__content">
            <span class="decision-remarks-banner__label"><?= $isApproved ? 'Admission Remarks' : 'Reason for Decline' ?></span>
            <span class="decision-remarks-banner__text"><?= esc($decisionRemarks) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($revisionPending): ?>
    <!-- Revision-pending banner — shown while awaiting the applicant's fix -->
    <div class="decision-remarks-banner decision-remarks-banner--revision">
        <div class="decision-remarks-banner__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        </div>
        <div class="decision-remarks-banner__content">
            <span class="decision-remarks-banner__label">Revision Requested — Awaiting Applicant</span>
            <span class="decision-remarks-banner__text"><?= esc($profile['revision_note'] ?? '') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?= $this->include('admission-officer/applicants/detailed-applicant/components/applicant-profile') ?>

    <div class="applicants__application" id="applicants-application">
        <?= $this->include('admission-officer/applicants/detailed-applicant/components/navigation-tabs') ?>
        <?= $this->include('admission-officer/applicants/detailed-applicant/components/application-content') ?>
    </div>
</div>

<!-- ── Decision Modal ── -->
<div class="decision-modal-overlay" id="decision-modal-overlay">
    <div class="decision-modal">

        <!-- Header -->
        <div class="decision-modal__header" id="decision-modal-header">
            <div class="decision-modal__header-icon" id="decision-modal-icon">
                <!-- icon injected by JS -->
            </div>
            <div>
                <h3 class="decision-modal__title" id="decision-modal-title">Approve Application</h3>
                <p class="decision-modal__subtitle"><?= esc($profile['applicant_name'] ?? '') ?> · #<?= esc($profile['applicant_number'] ?? '') ?></p>
            </div>
            <button class="decision-modal__close" id="decision-modal-close">&times;</button>
        </div>

        <!-- Body -->
        <div class="decision-modal__body">

            <!-- Remarks templates -->
            <div class="decision-modal__section-label">Select a template <span>(or write your own below)</span></div>
            <div class="decision-templates" id="decision-templates">
                <!-- populated by JS based on approve/decline -->
            </div>

            <!-- Remarks textarea -->
            <div class="decision-modal__section-label" style="margin-top:16px;">
                Remarks <span>(required)</span>
            </div>
            <textarea class="decision-modal__textarea" id="decision-remarks" rows="4" placeholder="Write your remarks here…"></textarea>
            <p class="decision-modal__error" id="decision-error"></p>

        </div>

        <!-- Footer -->
        <div class="decision-modal__footer">
            <button class="decision-modal__cancel" id="decision-modal-cancel">Cancel</button>
            <button class="decision-modal__submit" id="decision-modal-submit">Submit Decision</button>
        </div>

    </div>
</div>

<!-- ── Message Modal ── -->
<div class="decision-modal-overlay" id="message-modal-overlay">
    <div class="decision-modal">

        <!-- Header -->
        <div class="decision-modal__header decision-modal__header--message">
            <div class="decision-modal__header-icon" style="background:#e0e7ff;color:#4f46e5;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <h3 class="decision-modal__title">Message Applicant</h3>
                <p class="decision-modal__subtitle"><?= esc($profile['applicant_name'] ?? '') ?> · #<?= esc($profile['applicant_number'] ?? '') ?></p>
            </div>
            <button class="decision-modal__close" id="message-modal-close">&times;</button>
        </div>

        <!-- Body -->
        <div class="decision-modal__body">
            <div class="decision-modal__section-label">Quick templates <span>(optional)</span></div>
            <div class="decision-templates" id="message-templates">
                <button class="decision-template-pill" onclick="selectMsgTemplate(this, 'Please complete your missing requirements as soon as possible.')">Please complete your missing requirements as soon as possible.</button>
                <button class="decision-template-pill" onclick="selectMsgTemplate(this, 'Your submitted documents need clarification. Please visit the admissions office or reply here.')">Your submitted documents need clarification. Please visit the admissions office or reply here.</button>
                <button class="decision-template-pill" onclick="selectMsgTemplate(this, 'Congratulations! Your application has been reviewed. Please proceed with the next enrollment steps.')">Congratulations! Your application has been reviewed. Please proceed with the next enrollment steps.</button>
            </div>

            <div class="decision-modal__section-label" style="margin-top:16px;">
                Message <span>(required)</span>
            </div>
            <textarea class="decision-modal__textarea" id="message-text" rows="4" placeholder="Type your message here…"></textarea>
            <p class="decision-modal__error" id="message-error"></p>
        </div>

        <!-- Footer -->
        <div class="decision-modal__footer">
            <button class="decision-modal__cancel" id="message-modal-cancel">Cancel</button>
            <button class="decision-modal__submit decision-modal__submit--message" id="message-modal-submit">Send Message</button>
        </div>

    </div>
</div>

<!-- ── Request Revision Modal ── -->
<div class="decision-modal-overlay" id="revision-modal-overlay">
    <div class="decision-modal">

        <!-- Header -->
        <div class="decision-modal__header decision-modal__header--revision">
            <div class="decision-modal__header-icon" style="background:#ffedd5;color:#c2410c;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </div>
            <div>
                <h3 class="decision-modal__title">Request Revision</h3>
                <p class="decision-modal__subtitle"><?= esc($profile['applicant_name'] ?? '') ?> · #<?= esc($profile['applicant_number'] ?? '') ?></p>
            </div>
            <button class="decision-modal__close" id="revision-modal-close">&times;</button>
        </div>

        <!-- Body -->
        <div class="decision-modal__body">
            <div class="decision-modal__section-label">Which step(s) need revision? <span>(required)</span></div>
            <div class="revision-step-checklist" id="revision-step-checklist">
                <?php foreach (($formPages ?? []) as $page): ?>
                    <label class="revision-step-item">
                        <input type="checkbox" name="page_ids[]" value="<?= (int) $page['id'] ?>">
                        <span><?= esc($page['title']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="decision-modal__section-label" style="margin-top:16px;">
                Note to applicant <span>(required)</span>
            </div>
            <textarea class="decision-modal__textarea" id="revision-note" rows="4" placeholder="Explain exactly what needs to be corrected…"></textarea>
            <p class="decision-modal__error" id="revision-error"></p>
        </div>

        <!-- Footer -->
        <div class="decision-modal__footer">
            <button class="decision-modal__cancel" id="revision-modal-cancel">Cancel</button>
            <button class="decision-modal__submit decision-modal__submit--revision" id="revision-modal-submit">Send Revision Request</button>
        </div>

    </div>
</div>

<script>
window.applicantsConfig = <?= json_encode([
    'baseUrl'     => base_url(),
    'applicantId' => $applicantId,
    'activeTab'   => $activeTab ?? 'requirements',
]) ?>;

(function () {
    const DECIDE_URL   = '<?= base_url('admission-officer/applicants/' . $applicantId . '/decide') ?>';
    const MESSAGE_URL  = '<?= base_url('admission-officer/follow-up/send') ?>';
    const CONV_URL     = '<?= base_url('admission-officer/follow-up/' . $applicantId) ?>';
    const REVISION_URL = '<?= base_url('admission-officer/applicants/' . $applicantId . '/request-revision') ?>';

    const TEMPLATES = {
        approved: [
            'All submitted documents have been reviewed and verified. The application meets all admission requirements.',
            'Congratulations! Your documents are complete and your application has been approved. Please proceed to the next step.',
            'After thorough review, the application has been approved. The applicant may now proceed with enrollment.',
        ],
        rejected: [
            'The application has been declined due to incomplete or missing required documents. Please resubmit with complete requirements.',
            'The submitted documents do not meet the required standards. Please review the requirements and resubmit.',
            'After careful review, the application does not meet the program\'s admission requirements at this time.',
        ],
    };

    const ICONS = {
        approved: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
        rejected:  `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    };

    let currentDecision = null;

    // Decision modal elements
    const overlay    = document.getElementById('decision-modal-overlay');
    const header     = document.getElementById('decision-modal-header');
    const iconEl     = document.getElementById('decision-modal-icon');
    const titleEl    = document.getElementById('decision-modal-title');
    const templates  = document.getElementById('decision-templates');
    const textarea   = document.getElementById('decision-remarks');
    const errorEl    = document.getElementById('decision-error');
    const submitBtn  = document.getElementById('decision-modal-submit');

    function openModal(decision) {
        currentDecision = decision;
        errorEl.textContent = '';
        textarea.value = '';

        const isApprove = decision === 'approved';

        header.className = 'decision-modal__header decision-modal__header--' + (isApprove ? 'approve' : 'decline');
        iconEl.innerHTML = ICONS[decision];
        titleEl.textContent = isApprove ? 'Approve Application' : 'Decline Application';

        submitBtn.className = 'decision-modal__submit decision-modal__submit--' + (isApprove ? 'approve' : 'decline');
        submitBtn.textContent = isApprove ? 'Approve' : 'Decline';

        templates.innerHTML = TEMPLATES[decision].map((t, i) =>
            `<button class="decision-template-pill" data-index="${i}" onclick="selectTemplate(this, \`${t.replace(/`/g, '\\`')}\`)">${t}</button>`
        ).join('');

        overlay.classList.add('active');
        textarea.focus();
    }

    window.selectTemplate = function (btn, text) {
        document.querySelectorAll('#decision-templates .decision-template-pill').forEach(p => p.classList.remove('selected'));
        btn.classList.add('selected');
        textarea.value = text;
        errorEl.textContent = '';
    };

    function closeDecisionModal() {
        overlay.classList.remove('active');
        currentDecision = null;
    }

    async function submitDecision() {
        const remarks = textarea.value.trim();
        if (!remarks) {
            errorEl.textContent = 'Please add remarks before submitting.';
            textarea.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting…';

        const form = new FormData();
        form.append('status',  currentDecision);
        form.append('remarks', remarks);

        try {
            const res  = await fetch(DECIDE_URL, { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                closeDecisionModal();
                // Reload to show the remarks banner and hide buttons
                location.reload();
            } else {
                errorEl.textContent = data.message || 'Something went wrong. Please try again.';
            }
        } catch (e) {
            errorEl.textContent = 'Network error. Please try again.';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = currentDecision === 'approved' ? 'Approve' : 'Decline';
        }
    }

    // Decision modal wiring
    const btnApprove = document.getElementById('btn-approve');
    const btnDecline = document.getElementById('btn-decline');
    if (btnApprove) btnApprove.addEventListener('click', () => openModal('approved'));
    if (btnDecline) btnDecline.addEventListener('click', () => openModal('rejected'));
    document.getElementById('decision-modal-close').addEventListener('click', closeDecisionModal);
    document.getElementById('decision-modal-cancel').addEventListener('click', closeDecisionModal);
    submitBtn.addEventListener('click', submitDecision);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeDecisionModal(); });

    // ── Message modal ──────────────────────────────────────────────
    const msgOverlay   = document.getElementById('message-modal-overlay');
    const msgTextarea  = document.getElementById('message-text');
    const msgErrorEl   = document.getElementById('message-error');
    const msgSubmitBtn = document.getElementById('message-modal-submit');

    window.selectMsgTemplate = function (btn, text) {
        document.querySelectorAll('#message-templates .decision-template-pill').forEach(p => p.classList.remove('selected'));
        btn.classList.add('selected');
        msgTextarea.value = text;
        msgErrorEl.textContent = '';
    };

    function openMessageModal() {
        msgTextarea.value = '';
        msgErrorEl.textContent = '';
        document.querySelectorAll('#message-templates .decision-template-pill').forEach(p => p.classList.remove('selected'));
        msgOverlay.classList.add('active');
        msgTextarea.focus();
    }

    function closeMessageModal() {
        msgOverlay.classList.remove('active');
    }

    async function sendMessage() {
        const message = msgTextarea.value.trim();
        if (!message) {
            msgErrorEl.textContent = 'Please write a message before sending.';
            msgTextarea.focus();
            return;
        }

        msgSubmitBtn.disabled = true;
        msgSubmitBtn.textContent = 'Sending…';

        const form = new FormData();
        form.append('applicant_id', <?= (int) $applicantId ?>);
        form.append('message', message);

        try {
            const res  = await fetch(MESSAGE_URL, { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                closeMessageModal();
                // Show a brief toast then optionally redirect to conversation
                showToast('Message sent!');
            } else {
                msgErrorEl.textContent = data.message || 'Something went wrong. Please try again.';
            }
        } catch (e) {
            msgErrorEl.textContent = 'Network error. Please try again.';
        } finally {
            msgSubmitBtn.disabled = false;
            msgSubmitBtn.textContent = 'Send Message';
        }
    }

    document.getElementById('btn-message').addEventListener('click', openMessageModal);
    document.getElementById('message-modal-close').addEventListener('click', closeMessageModal);
    document.getElementById('message-modal-cancel').addEventListener('click', closeMessageModal);
    msgSubmitBtn.addEventListener('click', sendMessage);
    msgOverlay.addEventListener('click', e => { if (e.target === msgOverlay) closeMessageModal(); });

    // ── Revision modal ──────────────────────────────────────────────
    const revOverlay = document.getElementById('revision-modal-overlay');
    const revNote    = document.getElementById('revision-note');
    const revErrorEl = document.getElementById('revision-error');
    const revSubmit  = document.getElementById('revision-modal-submit');

    function openRevisionModal() {
        revNote.value = '';
        revErrorEl.textContent = '';
        document.querySelectorAll('#revision-step-checklist input').forEach(cb => cb.checked = false);
        revOverlay.classList.add('active');
    }

    function closeRevisionModal() {
        revOverlay.classList.remove('active');
    }

    async function submitRevision() {
        const checked = [...document.querySelectorAll('#revision-step-checklist input:checked')].map(cb => cb.value);
        const note = revNote.value.trim();

        if (!checked.length) { revErrorEl.textContent = 'Select at least one step.'; return; }
        if (!note) { revErrorEl.textContent = 'Please add a note explaining the issue.'; revNote.focus(); return; }

        revSubmit.disabled = true;
        revSubmit.textContent = 'Sending…';

        const form = new FormData();
        checked.forEach(id => form.append('page_ids[]', id));
        form.append('note', note);

        try {
            const res  = await fetch(REVISION_URL, { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                closeRevisionModal();
                location.reload();
            } else {
                revErrorEl.textContent = data.message || 'Something went wrong. Please try again.';
            }
        } catch (e) {
            revErrorEl.textContent = 'Network error. Please try again.';
        } finally {
            revSubmit.disabled = false;
            revSubmit.textContent = 'Send Revision Request';
        }
    }

    const btnRevision = document.getElementById('btn-revision');
    if (btnRevision) btnRevision.addEventListener('click', openRevisionModal);
    document.getElementById('revision-modal-close').addEventListener('click', closeRevisionModal);
    document.getElementById('revision-modal-cancel').addEventListener('click', closeRevisionModal);
    revSubmit.addEventListener('click', submitRevision);
    revOverlay.addEventListener('click', e => { if (e.target === revOverlay) closeRevisionModal(); });

    // Global ESC closes whichever modal is open
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeDecisionModal(); closeMessageModal(); closeRevisionModal(); }
    });

    // ── Toast ──────────────────────────────────────────────────────
    function showToast(msg) {
        let t = document.getElementById('ao-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'ao-toast';
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#15803d;color:#fff;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:600;z-index:99999;box-shadow:0 8px 24px rgba(0,0,0,.15);transition:opacity .25s;opacity:0;pointer-events:none;';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        setTimeout(() => { t.style.opacity = '0'; }, 2500);
    }
})();
</script>

<script>
function openExamResultModal() {
    document.getElementById('examResultModal').classList.add('active');
}

function closeExamResultModal() {
    document.getElementById('examResultModal').classList.remove('active');
}

async function submitExamResult() {
    const bookingId = document.getElementById('examBookingId').value;
    const status = document.getElementById('resultStatus').value;
    const score = document.getElementById('resultScore').value;
    const remarks = document.getElementById('resultRemarks').value;
    const error = document.getElementById('examResultError');

    error.textContent = '';

    if (!status) {
        error.textContent = 'Result status is required.';
        return;
    }

    const formData = new FormData();
    formData.append('result_status', status);
    formData.append('score', score);
    formData.append('result_remarks', remarks);

    const response = await fetch(`<?= base_url('admission-officer/exam-bookings') ?>/${bookingId}/result`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const data = await response.json();

    if (!data.success) {
        error.textContent = data.message || 'Failed to save result.';
        return;
    }

    closeExamResultModal();
    location.reload();
}
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/applicants/main.js') ?>?v=<?= time() ?>"></script>
