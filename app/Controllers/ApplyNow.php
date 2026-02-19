<?php

namespace App\Controllers;

class ApplyNow extends BaseController 
{
    public function index(): string 
    {
        return view('applynowpage.php');       
    }
}