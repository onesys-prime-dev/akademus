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

        $sidebarActive  = $t['sidebar_active_color']   ?? $accent;
        $bannerBg       = $t['banner_bg_color']        ?? $primary;
        $bannerBoxBg    = $t['banner_box_color']       ?? $primaryLight;
        $buttonPrimaryBg   = $t['button_primary_bg']   ?? $primary;
        $buttonPrimaryText = $t['button_primary_text'] ?? '#ffffff';
        $buttonAccentBg    = $t['button_accent_bg']    ?? $accent;
        $buttonAccentText  = $t['button_accent_text']  ?? $accentText;
        $headerText     = $t['header_text_color']      ?? $primary;
        $progressActive = $t['progress_active_color']  ?? $accent;

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
    --sidebar-active:      {$sidebarActive};
    --banner-bg:           {$bannerBg};
    --banner-box-bg:       {$bannerBoxBg};
    --button-primary-bg:   {$buttonPrimaryBg};
    --button-primary-text: {$buttonPrimaryText};
    --button-accent-bg:    {$buttonAccentBg};
    --button-accent-text:  {$buttonAccentText};
    --header-text:         {$headerText};
    --progress-active:     {$progressActive};
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

if (!function_exists('theme_favicon_tags')) {
    /**
     * Browser-tab icon links — uses the admin-uploaded logo if one is set,
     * otherwise falls back to the real Akademus brand mark (not a generic
     * placeholder). Single source of truth so every layout (applicant,
     * auth, admission officer, admin) shows the same, currently-correct
     * favicon without duplicating this logic.
     */
    function theme_favicon_tags(): string
    {
        $logoUrl = theme_get('logo_url', '');

        if (!empty($logoUrl)) {
            $href32 = $href16 = $href180 = esc($logoUrl);
        } else {
            $href32  = base_url('assets/images/favicons/favicon-32x32.png');
            $href16  = base_url('assets/images/favicons/favicon-16x16.png');
            $href180 = base_url('assets/images/favicons/favicon-180x180.png');
        }

        return '<link rel="icon" type="image/png" sizes="32x32" href="' . $href32 . '">' . "\n"
             . '<link rel="icon" type="image/png" sizes="16x16" href="' . $href16 . '">' . "\n"
             . '<link rel="apple-touch-icon" sizes="180x180" href="' . $href180 . '">' . "\n";
    }
}