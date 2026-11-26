<?php

namespace Tests\Feature;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\ControllerTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\DatabaseTestCase;

/**
 * AdminAuth lets FormFields and ThemeController answer anonymous requests themselves (their JSON
 * actions return 401 for the admin UI). That is only safe while EVERY action they expose under admin/*
 * refuses a visitor without an admin session - this test calls each one directly to prove it.
 *
 * @internal
 */
final class SelfGuardedAdminControllersTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    private const CONTROLLERS = ['\App\Controllers\Admin\FormFields', '\App\Controllers\ThemeController'];

    /**
     * Reachable without a session on purpose (AdminAuth::PUBLIC_PATHS).
     */
    private const PUBLIC = ['admin/login', 'admin/logout'];

    #[DataProvider('provideActionRefusesAnonymousVisitor')]
    public function testActionRefusesAnonymousVisitor(string $controller, string $method, array $args): void
    {
        $result   = $this->controller($controller)->execute($method, ...$args);
        $response = $result->response();

        $refused = $response instanceof RedirectResponse
            ? str_contains($response->getHeaderLine('Location'), 'admin/login')
            : $response->getStatusCode() === 401;

        $this->assertTrue($refused, "{$controller}::{$method}() must redirect to admin/login or answer 401 without an admin session (got {$response->getStatusCode()}).");
    }

    /**
     * @return iterable<string, array{string, string, list<string>}>
     */
    public static function provideActionRefusesAnonymousVisitor(): iterable
    {
        $routes = service('routes')->loadRoutes();

        foreach (['GET', 'POST'] as $verb) {
            foreach ($routes->getRoutes($verb) as $pattern => $handler) {
                if (! is_string($handler) || ! str_starts_with($pattern, 'admin') || in_array($pattern, self::PUBLIC, true)) {
                    continue;
                }
                [$controller, $method] = explode('::', '\App\Controllers\\' . ltrim($handler, '\\')) + [1 => 'index'];
                $controller            = '\\' . ltrim(str_replace('\App\Controllers\App\Controllers', '\App\Controllers', $controller), '\\');
                if (! in_array($controller, self::CONTROLLERS, true)) {
                    continue;
                }
                $method = explode('/', $method)[0];
                $args   = preg_match_all('/\$\d/', (string) $handler) ? array_fill(0, preg_match_all('/\$\d/', (string) $handler), '1') : [];

                yield "{$verb} {$pattern}" => [$controller, $method, $args];
            }
        }
    }
}
