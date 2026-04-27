<?php

namespace App\Controllers\LandingPage;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        return view('landing-pages/homepage.php');
    }

}
