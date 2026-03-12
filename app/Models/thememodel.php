<?php

namespace App\Models;

use CodeIgniter\Model;

class ThemeModel extends Model
{
    protected $table      = 'theme_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value'];

    /**
     * Get all settings as a key => value array
     */
    public function getAllSettings(): array
    {
        $rows = $this->findAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    /**
     * Get a single setting value
     */
    public function getSetting(string $key, string $default = ''): string
    {
        $row = $this->where('setting_key', $key)->first();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Save/update a setting
     */
    public function saveSetting(string $key, string $value): void
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            $this->where('setting_key', $key)->set('setting_value', $value)->update();
        } else {
            $this->insert(['setting_key' => $key, 'setting_value' => $value]);
        }
    }

    /**
     * Save multiple settings at once
     */
    public function saveSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->saveSetting($key, $value);
        }
    }
}