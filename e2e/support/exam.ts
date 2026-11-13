import { Page } from '@playwright/test';
import { sessionGet, sessionPostForm } from './session-http';

export const EXAM_URL = 'http://localhost/akademus/public/admission-officer/exam-schedule';

const pad = (n: number) => String(n).padStart(2, '0');

// Local calendar date `daysAhead` days from today.
export function dateAhead(daysAhead = 0) {
  const d = new Date();
  d.setDate(d.getDate() + daysAhead);
  return { iso: `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`, day: d.getDate(), month: d.getMonth() + 1, year: d.getFullYear() };
}

export type NewSchedule = { date: string; location: string; slots?: number; start?: string; end?: string };

// Creates a throwaway schedule through the same endpoint the modal posts to. Returns its id.
export async function createScheduleApi(page: Page, s: NewSchedule): Promise<number> {
  const res = await sessionPostForm(page, 'admission-officer/exam-schedule/create', {
    exam_date: s.date,
    start_time: s.start ?? '09:00',
    end_time: s.end ?? '10:00',
    location: s.location,
    max_slots: String(s.slots ?? 3),
  });
  if (!res.json?.success) throw new Error('Could not create exam schedule: ' + res.text.slice(0, 200));
  return Number(res.json.id);
}

export async function deleteScheduleApi(page: Page, id: number | string) {
  return sessionPostForm(page, `admission-officer/exam-schedule/delete/${id}`, {});
}

export async function showScheduleApi(page: Page, id: number | string) {
  const res = await sessionGet(page, `admission-officer/exam-schedule/show/${id}`);
  return res.json as { schedule: any; applicants: any[] };
}
