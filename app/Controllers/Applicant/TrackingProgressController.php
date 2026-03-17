<?php
// Requirements.php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\ThemeModel;

class TrackingProgressController extends BaseController
{
    public function index() {
        $themeModel = new ThemeModel();
        $theme      = $themeModel->getAllSettings();
        $theme['primary_soft'] = $this->lightenHex($theme['primary_color'] ?? '#0040a6', 0.88);
        
        return view('applicant/main', [
            'title' => 'Track Submission Progress',
            'page_content' => 'applicant/tracking-progress/tracking-progress',
            'theme' => $theme
        ]);
    }

    private function lightenHex(string $hex, float $amount = 0.88): string
    {
        $hex = ltrim($hex, '#');
        $r   = (int)round(hexdec(substr($hex, 0, 2)) + (255 - hexdec(substr($hex, 0, 2))) * $amount);
        $g   = (int)round(hexdec(substr($hex, 2, 2)) + (255 - hexdec(substr($hex, 2, 2))) * $amount);
        $b   = (int)round(hexdec(substr($hex, 4, 2)) + (255 - hexdec(substr($hex, 4, 2))) * $amount);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}