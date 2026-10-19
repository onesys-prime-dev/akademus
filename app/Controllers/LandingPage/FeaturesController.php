<?php

namespace App\Controllers\LandingPage;

use App\Controllers\LandingPage\HomeController;

class FeaturesController extends HomeController
{
    public function index(): string
    {
        return view('landing-pages/features.php');
    }

}