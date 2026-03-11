<?php

if (!function_exists('theme_css_vars')) {
    function theme_css_vars(): string
    {
        static $css = null;

        if ($css !== null) {
            return $css;
        }

        try {
            $db = \Config\Database::connect();
            $rows = $db->table('theme_settings')->get()->getResultArray();
            $t = [];
            foreach ($rows as $row) {
                $t[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) {
            $t = [];
        }

        $primary      = $t['primary_color']  ?? '#0040a6';
        $primaryDark  = $t['primary_dark']   ?? '#002d7a';
        $primaryLight = $t['primary_light']  ?? '#1a55c0';
        $accent       = $t['accent_color']   ?? '#ffb200';
        $accentText   = $t['accent_text']    ?? '#002d7a';
        $sidebar      = $t['sidebar_color']  ?? '#002d7a';
        $font         = $t['font_family']    ?? 'Sora';
        $radius       = $t['border_radius']  ?? '8px';

        $css = "
<style>
  :root {
    --primary:       {$primary};
    --primary-dark:  {$primaryDark};
    --primary-light: {$primaryLight};
    --accent:        {$accent};
    --accent-text:   {$accentText};
    --sidebar:       {$sidebar};
    --radius:        {$radius};
    --font:          '{$font}', sans-serif;
    --card-gradient: linear-gradient(180deg, rgba(255,255,255,0.61), {$primaryLight});
  }
</style>
<link href=\"https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap\" rel=\"stylesheet\">
";
        return $css;
    }
}

if (!function_exists('theme_get')) {
    function theme_get(string $key, string $default = ''): string
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $db = \Config\Database::connect();
                $rows = $db->table('theme_settings')->get()->getResultArray();
                $settings = [];
                foreach ($rows as $row) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            } catch (\Exception $e) {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
    }
}