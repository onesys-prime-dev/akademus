<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Requires an administrator session for every admin/* route (see Config\Filters::$filters).
 *
 * Applied at the route level so a controller that forgets the login check (e.g. one that overrides
 * BaseAdminController::__construct() without calling the parent) is still protected.
 *
 * @SuppressWarnings("PHPMD.UnusedFormalParameter") signatures are fixed by FilterInterface
 */
class AdminAuth implements FilterInterface
{
    /**
     * Reachable without an admin session.
     */
    private const PUBLIC_PATHS = ['admin/login', 'admin/logout'];

    /**
     * Controllers that enforce the admin session in every action on their own
     * (class names as strings: filters must not depend on the controller layer).
     */
    private const SELF_GUARDED_CONTROLLERS = [
        'App\Controllers\Admin\FormFields',
        'App\Controllers\ThemeController',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        $path = trim($request->getPath(), '/');

        if (in_array($path, self::PUBLIC_PATHS, true)) {
            return null;
        }

        if (session()->get('admin_logged_in')) {
            return null;
        }

        // These controllers check the admin session in every action themselves and answer their
        // JSON actions with 401 {"status":"error","message":"Unauthorized"}, which the admin UI shows.
        // Let them respond so that contract is kept (AdminAuthFilterTest covers them).
        $controller = ltrim((string) service('router')->controllerName(), '\\');
        if (in_array($controller, self::SELF_GUARDED_CONTROLLERS, true)) {
            return null;
        }

        // Same behaviour as OfficerAuth: every other request without an admin session is redirected.
        return redirect()->to(base_url('admin/login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
