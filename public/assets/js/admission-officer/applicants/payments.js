import { config } from './state.js';
import { updatePaymentUI, revertPaymentUI } from './ui.js';

const { baseUrl } = config;

export async function handlePaymentAction(paymentId, action, panelId) {
  const remarksEl  = document.getElementById('pr-remarks-' + paymentId);
  const errEl      = document.getElementById('pr-err-' + paymentId);
  const approveBtn = document.querySelector(`#pr-action-${paymentId} .pr-btn--approve`);
  const rejectBtn  = document.querySelector(`#pr-action-${paymentId} .pr-btn--reject`);

  const remarks = remarksEl?.value.trim() ?? '';

  if (action === 'reject' && !remarks) {
    if (errEl) errEl.textContent = 'Remarks are required when rejecting a payment.';
    return;
  }

  if (errEl) errEl.textContent = '';

  const clickedBtn = action === 'approve' ? approveBtn : rejectBtn;
  
  clickedBtn?.classList.add('loading');

  // =========================
  // 🚀 OPTIMISTIC UPDATE
  // =========================
  updatePaymentUI(paymentId, action, remarks);

  const url = `${baseUrl}/admission-officer/payments/${paymentId}/${action}`;
  const form = new FormData();
  form.append('remarks', remarks);

  try {
    const res  = await fetch(url, { method: 'POST', body: form });
    const data = await res.json();

    if (!data.success) {
      throw new Error(data.message || 'Failed');
    }

  } catch (err) {
    console.error('[Payment Action Error]', err);

    if (errEl) errEl.textContent = 'Failed to process. Reverting...';

    // remove loading
    clickedBtn?.classList.remove('loading');

    // 🔁 rollback
    revertPaymentUI(paymentId);
  }
}