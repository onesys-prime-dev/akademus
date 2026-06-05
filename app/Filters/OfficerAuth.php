<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OfficerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = ltrim($request->getUri()->getPath(), '/');

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
