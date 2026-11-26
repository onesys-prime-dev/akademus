<?php

namespace App\Controllers\LandingPage;

class ContactController extends HomeController
{
    public function index(): string
    {
        return view('landing-pages/contact.php');
    }
}
