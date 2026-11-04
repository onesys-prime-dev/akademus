// M3 Follow up  (TC-M3-01 .. TC-M3-04)
// spec: specs/admission-officer.md, specs/applicant-dashboard-and-form.md
// seed: e2e/seed.spec.ts
//
// Each describe logs in once in a beforeEach hook instead of repeating the login in every test.
import { test, expect } from './support/throttle';
import { BASE, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON, pagePost } from './support/applicant';
import { OFFICER_BASE, fetchApplicantRows, hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON } from './support/officer';
import { sessionGet, sessionPostForm } from './support/session-http';

test.describe('Officer follow-up messages', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    await loginAsOfficer(page);
  });

  test('Conversation list and unread count', async ({ page }) => {
    // 1. Open /admission-officer/follow-up (the list itself does not mark anything as read)
    await page.goto(OFFICER_BASE + 'follow-up');
    await expect(page.getByRole('heading', { name: 'Conversations' })).toBeVisible();
    await expect(page.getByText('Select a conversation to view messages')).toBeVisible();

    const convs = await sessionGet(page, 'admission-officer/follow-up/conversations');
    expect(convs.json.success).toBe(true);
    const total = convs.json.conversations.length;
    await expect(page.locator('#fo-conv-count')).toContainText(`${total} applicant${total !== 1 ? 's' : ''}`);
    if (total === 0) {
      await expect(page.getByText('No messages yet.')).toBeVisible();
    } else {
      await expect(page.locator('#foConversations .fo-conv-item')).toHaveCount(total);
    }

    // Unread badge in the header matches /unread-count
    const unread = await sessionGet(page, 'admission-officer/follow-up/unread-count');
    expect(unread.json.success).toBe(true);
    const count = Number(unread.json.count);
    const badge = page.locator('#msgBadge');
    if (count > 0) {
      await expect(badge).toBeVisible();
      await expect(badge).toHaveText(count > 99 ? '99+' : String(count));
    } else {
      await expect(badge).toBeHidden();
    }
  });

  test('Send message to applicant', async ({ page }) => {
    // Server side: an empty message or an unknown applicant is refused (nothing is stored)
    const empty = await sessionPostForm(page, 'admission-officer/follow-up/send', { applicant_id: '1', message: '   ' });
    expect(empty.json).toMatchObject({ success: false, message: 'Invalid payload' });
    const unknown = await sessionPostForm(page, 'admission-officer/follow-up/send', { applicant_id: '999999999', message: 'QA follow-up' });
    expect(unknown.json).toMatchObject({ success: false, message: 'Applicant not found' });

    // 1. Send empty: blocked in the UI before any request. The Message dialog on the applicant profile is used
    // because opening a /follow-up/{id} conversation would mark the applicant's real messages as read.
    const rows = await fetchApplicantRows(page);
    test.skip(rows.length === 0, 'No applicants in the test DB');
    await page.goto(OFFICER_BASE + 'detailed-applicant/' + rows[0].applicant_id);
    let sent = false;
    page.on('request', (r) => { if (r.url().includes('/follow-up/send')) sent = true; });
    await page.locator('#btn-message').click();
    await page.locator('#message-modal-submit').click();
    await expect(page.locator('#message-error')).toHaveText('Please write a message before sending.');
    expect(sent).toBe(false);
    await page.locator('#message-modal-cancel').click();

    // 2. Send 'QA follow-up': message appears and is visible to the applicant
    test.fixme(true, 'A real send stores a message the applicant can see (it cannot be deleted from the UI) and queues a followup.new_message email that cannot be stubbed from the browser; no disposable applicant is provisioned.');
  });

  test('Officer AI assistant (mocked)', async ({ page }) => {
    // Plan discrepancy: the widget (components/ai_assistant.php) posts to /api/groq/chat, not /api/groq/officer.
    // Both are stubbed so nothing can reach Groq whichever endpoint is used.
    const posted: any[] = [];
    await page.route('**/api/groq/**', async (route) => {
      posted.push({ url: route.request().url(), body: route.request().postDataJSON() });
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ choices: [{ message: { role: 'assistant', content: 'Mock reply from QA stub' } }] }),
      });
    });
    const external: string[] = [];
    page.on('request', (r) => { if (/groq\.com/.test(r.url())) external.push(r.url()); });

    // 1. Open the assistant and send a prompt
    await page.locator('#aoFab').click();
    await expect(page.locator('#aoPanel')).toHaveClass(/open/);
    await page.locator('#ao-input').fill('QA prompt: draft a welcome note');
    await page.locator('#ao-send-btn').click();

    // Mock reply rendered; no external call
    await expect(page.locator('.ao-msg.user .ao-bubble').last()).toContainText('QA prompt: draft a welcome note');
    await expect(page.locator('.ao-msg.bot .ao-bubble').last()).toContainText('Mock reply from QA stub');
    expect(posted).toHaveLength(1);
    const userTurn = posted[0].body.messages.at(-1);
    expect(userTurn).toMatchObject({ role: 'user', content: 'QA prompt: draft a welcome note' });
    expect(external).toEqual([]);
  });
});

test.describe('Applicant follow-up messages', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
    await page.goto(BASE + 'follow-up-messages');
  });

  test('Send follow-up message to officer', async ({ page }) => {
    const bubbles = page.locator('#fuMessages .fu-msg.me .fu-msg-bubble');
    const before = await bubbles.count();

    // NOTE: the floating #chatWidgetFab overlaps #fu-send-btn at the default viewport and intercepts
    // pointer events (see the next test), so the send button is triggered with a DOM click event.

    // 1a. Send empty message from the UI: nothing is added
    await page.locator('#fu-send-btn').dispatchEvent('click');
    await expect(bubbles).toHaveCount(before);

    // 1b. The server rejects an empty message
    const empty = await pagePost(page, 'follow-up-messages/send', { message: '' });
    expect(empty.json).toEqual({ success: false, message: 'Message cannot be empty' });

    // 2. Send 'QA test message'
    await page.locator('#fu-input').fill('QA test message');
    await page.locator('#fu-send-btn').dispatchEvent('click');
    await expect(bubbles.last()).toHaveText('QA test message');
    await expect(bubbles).toHaveCount(before + 1);

    // 3. Unread-count endpoint responds
    const unread = await page.evaluate(async (u) => (await fetch(u)).json(), BASE + 'follow-up-messages/unread-count');
    expect(unread).toMatchObject({ success: true });
    expect(typeof unread.count).toBe('number');
  });

  test('Follow-up send button is clickable by a real user', async ({ page }) => {
    test.fail(true, 'Observed live: <button id="chatWidgetFab"> intercepts pointer events over #fu-send-btn, so a real click opens the chat widget instead of sending');

    // 1. Real (trial) click on the send button must not be blocked by another element
    await page.locator('#fu-send-btn').click({ trial: true, timeout: 5000 });
  });
});
