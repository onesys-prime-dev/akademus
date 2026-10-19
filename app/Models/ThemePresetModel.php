<?php

namespace App\Models;

use CodeIgniter\Model;

class ThemePresetModel extends Model
{
    protected $table      = 'theme_presets';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'name',
        'primary_color', 'primary_dark', 'primary_light',
        'accent_color', 'accent_text',
        'sidebar_color', 'sidebar_active_color',
        'banner_bg_color', 'banner_box_color',
        'button_primary_bg', 'button_primary_text',
        'button_accent_bg', 'button_accent_text',
        'header_text_color', 'progress_active_color',
    ];

    public function getAll(): array
    {
        return $this->orderBy('created_at', 'ASC')->findAll();
    }

    public function createPreset(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return (int) $this->insert($data);
    }
}
