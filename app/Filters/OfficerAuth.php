<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OfficerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Filters only ever run within the HTTP request cycle, so $request is
        // always an IncomingRequest in practice — this narrows the type for
        // getPath(), which RequestInterface itself doesn't declare.
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        $path = ltrim($request->getPath(), '/');

        // Allow the login page itself through
        if ($path === 'admission-officer/login') {
            return null;
        }

        if (!session()->get('ao_logged_in')) {
            return redirect()->to(base_url('admission-officer/login'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
