<?php

use App\Models\FormFieldConfigModel;
use App\Models\FormPageModel;

if (!function_exists('get_field_configs')) {
    function get_field_configs(int $pageId): array
    {
        static $cache = [];
        if (!isset($cache[$pageId])) {
            $model = new FormFieldConfigModel();
            $cache[$pageId] = $model->getAllForPage($pageId);
        }
        return $cache[$pageId];
    }
}

if (!function_exists('get_enabled_pages')) {
    function get_enabled_pages(): array
    {
        static $cache = [];
        $programId = session()->get('selected_program_id');
        $key = $programId ? (int)$programId : 'default';

        if (!isset($cache[$key])) {
            if ($programId) {
                $service = new \App\Services\PresetService();
                $pages   = $service->getFormPagesForApplicant($programId);
                if (!empty($pages)) {
                    $cache[$key] = $pages;
                    return $cache[$key];
                }
            }
            $model = new FormPageModel();
            $cache[$key] = $model->getEnabledOrdered();
        }
        return $cache[$key];
    }
}

if (!function_exists('field_visible')) {
    function field_visible(array $configs, string $key): bool
    {
        return isset($configs[$key]) ? (bool)$configs[$key]['is_visible'] : true;
    }
}

if (!function_exists('field_required')) {
    function field_required(array $configs, string $key): bool
    {
        return isset($configs[$key]) ? (bool)$configs[$key]['is_required'] : false;
    }
}

if (!function_exists('field_label')) {
    function field_label(array $configs, string $key, string $fallback = ''): string
    {
        return isset($configs[$key]) ? $configs[$key]['label'] : $fallback;
    }
}

if (!function_exists('field_placeholder')) {
    function field_placeholder(array $configs, string $key, string $fallback = ''): string
    {
        return isset($configs[$key]) ? $configs[$key]['placeholder'] : $fallback;
    }
}