<div class="applicants card">
    <div class="applicants__top-actions">
        <div class="applicants__top-actions--back">
            <a href="<?= base_url('admission-officer/applicants') ?>" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:inherit;">
                <img src="<?= base_url('assets/images/icons/less-than.svg') ?>" alt="">
                <span>Applicant Profile</span>
            </a>
        </div>

        <div class="decision-actions">
            <button class="decision-btn decision-btn--decline" id="btn-decline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Decline
            </button>
            <button class="decision-btn decision-btn--approve" id="btn-approve">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approve
            </button>
        </div>
    </div>

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

<script>
window.applicantsConfig = <?= json_encode([
    'baseUrl'     => base_url(),
    'applicantId' => $applicantId,
    'activeTab'   => $activeTab ?? 'requirements',
]) ?>;

(function () {
    const DECIDE_URL = '<?= base_url('admission-officer/applicants/' . $applicantId . '/decide') ?>';

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

    const overlay   = document.getElementById('decision-modal-overlay');
    const header    = document.getElementById('decision-modal-header');
    const iconEl    = document.getElementById('decision-modal-icon');
    const titleEl   = document.getElementById('decision-modal-title');
    const templates = document.getElementById('decision-templates');
    const textarea  = document.getElementById('decision-remarks');
    const errorEl   = document.getElementById('decision-error');
    const submitBtn = document.getElementById('decision-modal-submit');

    function openModal(decision) {
        currentDecision = decision;
        errorEl.textContent = '';
        textarea.value = '';

        const isApprove = decision === 'approved';

        // Header style
        header.className = 'decision-modal__header decision-modal__header--' + (isApprove ? 'approve' : 'decline');
        iconEl.innerHTML = ICONS[decision];
        titleEl.textContent = isApprove ? 'Approve Application' : 'Decline Application';

        // Submit button style
        submitBtn.className = 'decision-modal__submit decision-modal__submit--' + (isApprove ? 'approve' : 'decline');
        submitBtn.textContent = isApprove ? 'Approve' : 'Decline';

        // Templates
        templates.innerHTML = TEMPLATES[decision].map((t, i) =>
            `<button class="decision-template-pill" data-index="${i}" onclick="selectTemplate(this, \`${t.replace(/`/g, '\\`')}\`)">${t}</button>`
        ).join('');

        overlay.classList.add('active');
        textarea.focus();
    }

    window.selectTemplate = function (btn, text) {
        document.querySelectorAll('.decision-template-pill').forEach(p => p.classList.remove('selected'));
        btn.classList.add('selected');
        textarea.value = text;
        errorEl.textContent = '';
    };

    function closeModal() {
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
                closeModal();
                // Update status badge in the profile header
                const badge = document.querySelector('.applicant-profile__status');
                if (badge) {
                    badge.className = 'applicant-profile__status status-badge status-badge--' + data.status;
                    badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                }
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

    document.getElementById('btn-approve').addEventListener('click', () => openModal('approved'));
    document.getElementById('btn-decline').addEventListener('click', () => openModal('rejected'));
    document.getElementById('decision-modal-close').addEventListener('click', closeModal);
    document.getElementById('decision-modal-cancel').addEventListener('click', closeModal);
    document.getElementById('decision-modal-submit').addEventListener('click', submitDecision);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
})();
</script>

<script type="module" src="<?= base_url('assets/js/admission-officer/applicants/main.js') ?>?v=<?= time() ?>"></script>
