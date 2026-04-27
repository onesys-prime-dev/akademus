<?php

namespace App\Controllers\LandingPage;

use App\Controllers\BaseController;

class SystemsController extends BaseController
{
    public function akademus(): string
    {
        return view('landing-pages/systems/akademus.php');
    }

    public function mentora(): string
    {
        return view('landing-pages/systems/mentora.php');
    }
    
    public function learnix(): string
    {
        return view('landing-pages/systems/learnix.php');
    }

    public function sis(): string
    {
        return view('landing-pages/systems/sis.php');
    }

    public function attendance(): string
    {
        return view('landing-pages/systems/attendance.php');
    }

    public function otis(): string
    {
        return view('landing-pages/systems/otis.php');
    }
}
