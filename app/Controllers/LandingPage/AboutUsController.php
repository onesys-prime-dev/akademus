<?php

namespace App\Controllers\LandingPage;

use App\Controllers\BaseController;

class AboutUsController extends BaseController
{
    public function index(): string
    {
        return view('landing-pages/about-us.php');
    }

}

?>