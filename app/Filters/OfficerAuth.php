<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OfficerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = ltrim($request->getPath(), '/');

        // Allow the login page itself through
        if ($path === 'admission-officer/login') {
            return;
        }

        if (!session()->get('ao_logged_in')) {
            return redirect()->to(base_url('admission-officer/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
