// App root URL with a trailing slash. Defaults to the XAMPP setup; CI sets E2E_BASE_URL (php spark serve).
export const BASE = (process.env.E2E_BASE_URL ?? 'http://localhost/akademus/public/').replace(/\/?$/, '/');
export const SIGN_IN = BASE + 'sign-in';
