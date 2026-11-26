<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;

/**
 * Default branding (the "classic-blue" preset), portal texts and feature flags.
 * Existing keys are left untouched, so values changed in the Theme Editor / System Settings survive.
 */
class ThemeSettingsSeeder extends Seeder
{
    use InsertsMissingRows;

    private const SETTINGS = [
        // Branding
        'preset'                => 'classic-blue',
        'primary_color'         => '#0040a6',
        'primary_dark'          => '#002d7a',
        'primary_light'         => '#1a55c0',
        'accent_color'          => '#ffb200',
        'accent_text'           => '#002d7a',
        'sidebar_color'         => '#002d7a',
        'sidebar_active_color'  => '#ffb200',
        'banner_bg_color'       => '#0040a6',
        'banner_box_color'      => '#1a55c0',
        'button_primary_bg'     => '#0040a6',
        'button_primary_text'   => '#ffffff',
        'button_accent_bg'      => '#ffb200',
        'button_accent_text'    => '#002d7a',
        'header_text_color'     => '#0040a6',
        'progress_active_color' => '#ffb200',
        'font_family'           => 'Sora',
        'border_radius'         => '8px',
        // Portal identity
        'app_name'         => 'Akademus Portal',
        'logo_text'        => 'AK',
        'logo_url'         => '',
        'school_year'      => 'SY 2025-2026',
        'admission_status' => 'Open for Applications',
        // Feature flags
        'exam_enabled'               => '1',
        'fast_track_enabled'         => '1',
        'tuition_calculator_enabled' => '0',
    ];

    public function run(): void
    {
        foreach (self::SETTINGS as $key => $value) {
            $this->insertIfMissing('theme_settings', ['setting_key' => $key], ['setting_value' => $value]);
        }
    }
}
