<?php


use App\Models\FormFieldConfigModel;
use App\Models\FormPageModel;

if (!function_exists('get_field_configs')) {
    /**
     * Returns all field configs for a page_id, keyed by field_key.
     * Cached per request.
     */
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
    /**
     * Returns all enabled form pages ordered by sort_order.
     * Used by the applicant form to build the step list dynamically.
     * Cached per request.
     */
    function get_enabled_pages(): array
    {
        static $pages = null;
        if ($pages === null) {
            $model = new FormPageModel();
            $pages = $model->getEnabledOrdered();
        }
        return $pages;
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
        return isset($configs[$key]) ? esc($configs[$key]['label']) : esc($fallback);
    }
}

if (!function_exists('field_placeholder')) {
    function field_placeholder(array $configs, string $key, string $fallback = ''): string
    {
        return isset($configs[$key]) ? esc($configs[$key]['placeholder']) : esc($fallback);
    }
}