<?php

namespace App\Controllers;

class AboutUsController extends BaseController {
    public function about_us() {
        return view ('about.php');
    }
}