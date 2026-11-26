<?php

namespace Tests\Feature;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\DatabaseTestCase;

/**
 * Every GET route that is not explicitly public must refuse an anonymous visitor
 * (redirect to a login page, or 401/403/404) instead of rendering its content.
 *
 * A new route fails this test until it is either protected or added to PUBLIC_ROUTES on purpose.
 *
 * @internal
 */
final class AnonymousAccessTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    /**
     * Routes that are meant to be reachable without logging in (route pattern as defined in Routes.php).
     */
    private const PUBLIC_ROUTES = [
        '/', 'homepage', 'about', 'about-us', 'features', 'contact', 'applynowpage', 'faq',
        'systems/akademus', 'systems/mentora', 'systems/learnix', 'systems/sis', 'systems/attendance', 'systems/otis',
        'sign-in', 'sign-up', 'admin/login', 'admission-officer/login', 'reset-password/([^/]+)',
        'health',
        // Read-only reference data (program names; form layout and requirement categories, no personal data)
        'get-programs', 'api/presets/program/([0-9]+)',
    ];

    /**
     * JSON endpoints that refuse anonymous visitors in the body ({"success": false}) instead of the status code.
     */
    private const REFUSE_IN_BODY = ['follow-up-messages/fetch', 'follow-up-messages/unread-count'];

    #[DataProvider('provideAnonymousVisitorIsRefused')]
    public function testAnonymousVisitorIsRefused(string $pattern, string $uri): void
    {
        try {
            $result = $this->get($uri);
        } catch (PageNotFoundException) {
            $this->addToAssertionCount(1); // 404: nothing is shown to the anonymous visitor

            return;
        }
        $status = $result->response()->getStatusCode();

        if (in_array($pattern, self::REFUSE_IN_BODY, true)) {
            $body = json_decode((string) $result->response()->getBody(), true);
            $this->assertFalse($body['success'] ?? null, "GET {$uri} must answer success=false to an anonymous visitor.");
            $this->assertEmpty($body['notifications'] ?? [], "GET {$uri} must not return data to an anonymous visitor.");

            return;
        }

        $this->assertTrue(
            $result->isRedirect() || in_array($status, [401, 403, 404], true),
            "GET {$uri} (route {$pattern}) answered {$status} to an anonymous visitor. Protect it, or add it to PUBLIC_ROUTES if it is public by design.",
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideAnonymousVisitorIsRefused(): iterable
    {
        $routes = service('routes')->loadRoutes()->getRoutes('GET');

        foreach (array_keys($routes) as $pattern) {
            if (in_array($pattern, self::PUBLIC_ROUTES, true)) {
                continue;
            }
            $uri = preg_replace(['/\(\[0-9\]\+\)/', '/\(\[\^\/\]\+\)/', '/\(:\w+\)/'], ['1', 'x', '1'], $pattern);

            yield $pattern => [$pattern, $uri];
        }
    }
}
