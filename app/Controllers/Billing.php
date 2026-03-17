<?php

namespace App\Controllers;

use CodeIgniter\Models;

class Billing extends BaseController
{
    public function billing()
    {
        return view('billing');
    }
}