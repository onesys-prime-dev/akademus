<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;

class FormFieldService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Get merged form fields for a program
     * Uses form_field_configs as base + program_form_field_overrides for customization
     */
    public function getMergedFormFields($programId)
    {
        $programId = (int) $programId;

        $query = $this->db->table('form_field_configs ffc')
            ->select(
                'ffc.id',
                'ffc.page_id',
                'ffc.field_key',
                'ffc.step',
                'ffc.section',
                'ffc.label',
                'ffc.field_type',
                'ffc.placeholder',
                'COALESCE(pffo.is_visible, ffc.is_visible) as is_visible',
                'COALESCE(pffo.is_required, ffc.is_required) as is_required',
                'COALESCE(pffo.sort_order, ffc.sort_order) as sort_order',
                'ffc.is_system'
            )
            ->leftJoin(
                'program_form_field_overrides pffo',
                "pffo.field_config_id = ffc.id AND pffo.program_id = {$programId}",
                'left'
            )
            ->where('ffc.is_visible', 1)
            ->orWhere('COALESCE(pffo.is_visible, ffc.is_visible)', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('ffc.id', 'ASC');

        $fields = $query->get()->getResultArray();

        // Group by section for easier frontend rendering
        return $this->groupFieldsBySection($fields);
    }

    /**
     * Get BASE form fields (without overrides) for a program
     */
    public function getBaseFormFields()
    {
        return $this->db->table('form_field_configs')
            ->where('is_visible', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get program-specific overrides only
     */
    public function getProgramOverrides($programId)
    {
        return $this->db->table('program_form_field_overrides')
            ->where('program_id', $programId)
            ->get()
            ->getResultArray();
    }

    /**
     * Save or update field override for a program
     */
    public function saveProgramFieldOverride($programId, $fieldConfigId, $overrides)
    {
        $existing = $this->db->table('program_form_field_overrides')
            ->where('program_id', $programId)
            ->where('field_config_id', $fieldConfigId)
            ->first();

        $data = array_merge(['program_id' => $programId, 'field_config_id' => $fieldConfigId], $overrides);

        if ($existing) {
            return $this->db->table('program_form_field_overrides')
                ->where('program_id', $programId)
                ->where('field_config_id', $fieldConfigId)
                ->update($data);
        }

        return $this->db->table('program_form_field_overrides')->insert($data);
    }

    /**
     * Remove field override for a program
     */
    public function removeProgramFieldOverride($programId, $fieldConfigId)
    {
        return $this->db->table('program_form_field_overrides')
            ->where('program_id', $programId)
            ->where('field_config_id', $fieldConfigId)
            ->delete();
    }

    /**
     * Validate form submission against field rules
     */
    public function validateSubmission($programId, $data)
    {
        $fields = $this->getMergedFormFields($programId);
        $errors = [];

        foreach ($fields as $section) {
            foreach ($section as $field) {
                $fieldKey = $field['field_key'];
                $value = $data[$fieldKey] ?? null;

                // Check required
                if ($field['is_required'] && empty($value)) {
                    $errors[$fieldKey] = "{$field['label']} is required";
                    continue;
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Group fields by section for better frontend organization
     */
    private function groupFieldsBySection($fields)
    {
        $grouped = [];
        foreach ($fields as $field) {
            $section = $field['section'] ?? 'general';
            if (!isset($grouped[$section])) {
                $grouped[$section] = [];
            }
            $grouped[$section][] = $field;
        }
        return $grouped;
    }

    /**
     * Get forms pages (steps)
     */
    public function getFormPages()
    {
        return $this->db->table('form_pages')
            ->where('is_enabled', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all form field configs (global/base)
     */
    public function getAllFormFieldConfigs()
    {
        return $this->db->table('form_field_configs')
            ->where('is_visible', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }
}
