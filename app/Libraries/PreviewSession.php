<?php

namespace App\Libraries;

use CodeIgniter\Session\Session;

/**
 * In-memory, request-scoped stand-in for the real session service.
 *
 * Used only by ThemeController::preview() so the Theme Customizer's live
 * preview can render real applicant views (which call session()->get(...))
 * against a fake persona WITHOUT ever reading or writing the real session.
 * It never touches $_SESSION, the session cookie or the session store, so
 * nothing rendered inside the preview iframe can log out, overwrite or
 * otherwise affect the admin's session.
 *
 * The parent constructor is intentionally not called: there is no handler or
 * config to wire up. Only the methods the views/models use are implemented;
 * everything lifecycle-related is a no-op.
 */
class PreviewSession extends Session
{
    /** @var array<string, mixed> */
    private array $store = [];

    /** @param array<string, mixed> $initial */
    public function __construct(array $initial = [])
    {
        $this->store = $initial;
    }

    public function start() { return null; }
    public function regenerate(bool $destroy = false) {}
    public function destroy() {}
    public function close() {}

    public function set($data, $value = null)
    {
        $data = is_array($data) ? $data : [$data => $value];
        foreach ($data as $k => $v) {
            $this->store[$k] = $v;
        }
    }

    public function get(?string $key = null)
    {
        if ($key === null || $key === '') {
            return $this->store;
        }
        return $this->store[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->store[$key]);
    }

    public function remove($key)
    {
        foreach ((array) $key as $k) {
            unset($this->store[$k]);
        }
    }

    public function __set(string $key, $value): void { $this->store[$key] = $value; }
    public function __get(string $key) { return $this->store[$key] ?? null; }
    public function __isset(string $key): bool { return isset($this->store[$key]); }

    public function setFlashdata($data, $value = null) {}
    public function getFlashdata(?string $key = null) { return $key === null ? [] : null; }
}
