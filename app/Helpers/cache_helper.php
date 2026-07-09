<?php

if (!function_exists('cache_remember')) {
    /**
     * Safe replacement for CacheInterface::remember().
     *
     * CI4's DummyHandler::remember() unconditionally returns null instead of
     * running the callback — so if the configured cache handler ever falls
     * back to 'dummy' (e.g. the primary handler becomes unavailable), every
     * caller relying on remember() would get null where it expected real
     * data, and crash. get()/save() on DummyHandler both correctly no-op
     * (get() returns null, save() is a harmless true), so building on those
     * instead degrades gracefully to "always run the query fresh" — exactly
     * the safe fallback behavior we want — regardless of which handler is
     * actually active.
     */
    function cache_remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = cache()->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        cache()->save($key, $value, $ttl);

        return $value;
    }
}
