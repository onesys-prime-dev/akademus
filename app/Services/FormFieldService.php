<?php

namespace App\Services;

use Config\Database;

class FormFieldService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Get merged form fields for a program
     * Uses form_field_configs as base + program_form_field_overrides for customization
     *
     * @param mixed $programId
     */
    public function getMergedFormFields($programId)
    {
        // Admin "what's actually active for this program" view — visible fields only.
        $fields = array_filter(
            $this->mergedFieldRows((int) $programId),
            static fn ($f) => (int) $f['is_visible'] === 1,
        );

        // Group by section for easier frontend rendering
        return $this->groupFieldsBySection($fields);
    }

    /**
     * Get merged (base + program override) field configs for a single page,
     * keyed by field_key — the shape the applicant-facing form expects
     * (see FormFieldConfigModel::getAllForPage()). Unlike getMergedFormFields(),
     * this returns hidden fields too: callers (field_visible(), custom-fields.php)
     * decide what to do with is_visible themselves, same as the non-override path.
     */
    public function getMergedFieldConfigsForPage(int $programId, int $pageId): array
    {
        $result = [];

        foreach ($this->mergedFieldRows($programId, $pageId) as $row) {
            $result[$row['field_key']] = $row;
        }

        return $result;
    }

    /**
     * Shared query behind getMergedFormFields()/getMergedFieldConfigsForPage():
     * base form_field_configs left-joined to this program's overrides, with
     * each overridable column coalesced down to the base value when no
     * override row (or no override for that column) exists.
     */
    private function mergedFieldRows(int $programId, ?int $pageId = null): array
    {
        // select() takes one comma-joined string (or array), not a variadic
        // argument list — passing the columns as separate args silently kept
        // only the first ("ffc.id") and dropped the rest.
        $query = $this->db->table('form_field_configs ffc')
            ->select([
                'ffc.id',
                'ffc.page_id',
                'ffc.field_key',
                'ffc.step',
                'ffc.section',
                'ffc.label',
                'ffc.field_type',
                'ffc.placeholder',
                'ffc.is_system',
                'COALESCE(pffo.is_visible, ffc.is_visible) as is_visible',
                'COALESCE(pffo.is_required, ffc.is_required) as is_required',
                'COALESCE(pffo.sort_order, ffc.sort_order) as sort_order',
            ])
            ->join(
                'program_form_field_overrides pffo',
                'pffo.field_config_id = ffc.id AND pffo.program_id = ' . $programId,
                'left',
            );

        if ($pageId !== null) {
            $query->where('ffc.page_id', $pageId);
        }

        // Order by the coalesce expression itself, not the bare "sort_order" alias —
        // both ffc and pffo have a real sort_order column, so the bare name is
        // ambiguous once they're joined (MySQL prefers the column over the alias).
        return $query
            ->orderBy('COALESCE(pffo.sort_order, ffc.sort_order)', 'ASC', false)
            ->orderBy('ffc.id', 'ASC')
            ->get()
            ->getResultArray();
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
     *
     * @param mixed $programId
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
     *
     * @param mixed $programId
     * @param mixed $fieldConfigId
     * @param mixed $overrides
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
     *
     * @param mixed $programId
     * @param mixed $fieldConfigId
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
     *
     * @param mixed $programId
     * @param mixed $data
     */
    public function validateSubmission($programId, $data)
    {
        $fields = $this->getMergedFormFields($programId);
        $errors = [];

        foreach ($fields as $section) {
            foreach ($section as $field) {
                $fieldKey = $field['field_key'];
                $value    = $data[$fieldKey] ?? null;

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
     *
     * @param mixed $fields
     */
    private function groupFieldsBySection($fields)
    {
        $grouped = [];

        foreach ($fields as $field) {
            $section = $field['section'] ?? 'general';
            if (! isset($grouped[$section])) {
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
