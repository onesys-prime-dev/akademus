<?php

namespace App\Controllers\LandingPage;

use App\Controllers\LandingPage\HomeController;

class ContactController extends HomeController
{
    public function index(): string
    {
        return view('landing-pages/contact.php');
    }

}