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

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        $path = trim($request->getPath(), '/');

        if (in_array($path, self::PUBLIC_PATHS, true)) {
            return null;
        }

        // Same behaviour as OfficerAuth: every request without an admin session, AJAX included,
        // is redirected to the admin login.
        if (! session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
