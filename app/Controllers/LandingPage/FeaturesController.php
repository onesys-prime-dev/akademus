<?php

namespace App\Controllers\LandingPage;

class FeaturesController extends HomeController
{
    public function index(): string
    {
        return view('landing-pages/features.php');
    }
}
