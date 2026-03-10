<?php

/**
 * Theme Helper
 * 
 * Loads saved theme settings from DB and returns CSS custom properties.
 * 
 * USAGE in any view:
 *   <?= theme_css_vars() ?>
 * 
 * Then use CSS variables anywhere:
 *   background: var(--primary);
 *   color: var(--accent);
 *   font-family: var(--font);
 * 
 * HOW TO AUTOLOAD:
 *   In app/Config/Autoload.php → add 'theme' to $helpers array:
 *   public $helpers = ['theme'];
 */

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
            // Fallback to defaults if DB fails
            $t = [];
        }

        $primary      = $t['primary_color']    ?? '#0040a6';
        $primaryDark  = $t['primary_dark']      ?? '#002d7a';
        $primaryLight = $t['primary_light']     ?? '#1a55c0';
        $accent       = $t['accent_color']      ?? '#ffb200';
        $accentText   = $t['accent_text']       ?? '#002d7a';
        $sidebar      = $t['sidebar_color']     ?? '#002d7a';
        $font         = $t['font_family']       ?? 'Sora';
        $radius       = $t['border_radius']     ?? '8px';
        $appName      = $t['app_name']          ?? 'Akademus Portal';

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
  }

  :root{
    --primary: <?= $theme['primary_color'] ?? '#0040A6' ?>;
    --primary-dark: <?= $theme['primary_dark'] ?? '#003080' ?>;
    --primary-light: <?= $theme['primary_light'] ?? '#8a8cca' ?>;

    --card-gradient:
        linear-gradient(
            180deg,
            rgba(255,255,255,0.61),
            var(--primary-light)
        );
}
</style>
<link href=\"https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap\" rel=\"stylesheet\">
";
        return $css;
    }
}

if (!function_exists('theme_get')) {
    /**
     * Get a single theme value
     * 
     * Usage: <?= theme_get('app_name', 'Akademus Portal') ?>
     */
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