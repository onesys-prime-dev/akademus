<?php

namespace App\Libraries;

use CodeIgniter\Cache\Handlers\FileHandler;
use CodeIgniter\Throttle\Throttler;
use Config\Cache as CacheConfig;

/**
 * Per-IP rate limiting for login endpoints, using CodeIgniter's built-in
 * token-bucket Throttler.
 *
 * Deliberately does NOT use service('throttler') / service('cache') — this
 * app's Config\Cache::$handler is 'dummy' (a no-op that never persists
 * anything), which would make the throttler silently allow every attempt.
 * Instead this builds its own Throttler backed by a real FileHandler, so
 * rate limiting works regardless of the app's global cache setting.
 */
class LoginThrottle
{
    private const CAPACITY = 5;   // attempts
    private const WINDOW   = 60;  // seconds

    private static ?Throttler $throttler = null;

    /**
     * @param string $scope Distinguishes login surfaces (applicant/officer/admin)
     *                       so exhausting one doesn't block the others.
     */
    public static function tooManyAttempts(string $scope): bool
    {
        // IPv6 addresses (e.g. loopback "::1") contain colons, which are
        // reserved characters CI4's cache key validation rejects — swap
        // anything non-alphanumeric out before it becomes part of the key.
        $ip = preg_replace('/[^a-zA-Z0-9]/', '_', service('request')->getIPAddress());

        return ! self::throttler()->check("login_{$scope}_{$ip}", self::CAPACITY, self::WINDOW);
    }

    /**
     * Seconds until the next attempt is allowed. Only meaningful right after
     * tooManyAttempts() returned true — it's a token bucket, so this is
     * usually WINDOW/CAPACITY (~12s here), not the full window.
     */
    public static function retryAfterSeconds(): int
    {
        return self::throttler()->getTokenTime();
    }

    /**
     * Ready-to-display message for the 429 response, using the real wait time.
     */
    public static function retryMessage(): string
    {
        $seconds = self::retryAfterSeconds();
        $unit    = $seconds === 1 ? 'second' : 'seconds';

        return "Too many login attempts. Please wait {$seconds} {$unit} and try again.";
    }

    private static function throttler(): Throttler
    {
        if (self::$throttler === null) {
            self::$throttler = new Throttler(new FileHandler(config(CacheConfig::class)));
        }

        return self::$throttler;
    }
}
