<?php
namespace App\Models;

use CodeIgniter\Model;

class FormFieldConfigModel extends Model
{
    protected $table      = 'form_field_configs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'page_id', 'field_key', 'step', 'section', 'label', 'field_type',
        'placeholder', 'is_visible', 'is_required', 'is_system', 'sort_order'
    ];
    protected $useTimestamps = true;

    /* Get all fields grouped by page_id and section. */
    public function getGroupedByPage(): array
    {
        $rows = $this->orderBy('page_id')->orderBy('section')->orderBy('sort_order')->findAll();
        // Attach the option list of select fields so the admin editor can show/edit it.
        $selectIds = array_column(array_filter($rows, fn($r) => $r['field_type'] === 'select'), 'id');
        $options   = (new FormFieldOptionConfigModel())->getNamesForFields(array_map('intval', $selectIds));

        $grouped = [];
        foreach ($rows as $row) {
            $row['options'] = $options[(int) $row['id']] ?? [];
            $grouped[$row['page_id']][$row['section']][] = $row;
        }
        return $grouped;
    }

    /**
     * Replaces the stored options of a custom select field with the given list
     * (trimmed, de-duplicated case-insensitively, order preserved).
     */
    private function syncOptions(int $fieldId, array $names): void
    {
        $clean = [];
        foreach ($names as $name) {
            $name = is_string($name) ? trim($name) : '';
            if ($name === '' || mb_strlen($name) > 255) {
                continue;
            }
            $clean[mb_strtolower($name)] ??= $name;
        }

        $optionModel = new FormFieldOptionConfigModel();
        $optionModel->where('form_field_id', $fieldId)->delete();
        foreach ($clean as $name) {
            $optionModel->insert(['form_field_id' => $fieldId, 'option_name' => $name]);
        }
    }

    /**
     * All fields for a page_id, keyed by field_key.
     * Used by applicant form step views.
     */
    public function getAllForPage(int $pageId): array
    {
        $rows = $this->where('page_id', $pageId)->orderBy('sort_order')->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['field_key']] = $row;
        }
        return $result;
    }

    /*Bulk save from Form Field Manager.*/
    public function bulkSave(array $fields): void
    {
        foreach ($fields as $f) {
            $key = trim($f['key'] ?? '');
            if (!$key) continue;

            $existing = $this->where('field_key', $key)->first();

            if ($existing) {
                $this->update($existing['id'], [
                    'label'       => $f['label']       ?? $existing['label'],
                    'placeholder' => $f['placeholder']  ?? $existing['placeholder'],
                    'is_visible'  => isset($f['visible'])  ? (int)(bool)$f['visible']  : $existing['is_visible'],
                    'is_required' => isset($f['required']) ? (int)(bool)$f['required'] : $existing['is_required'],
                ]);

                // System selects have hardcoded options in the views, so only custom ones are editable.
                if (empty($existing['is_system']) && $existing['field_type'] === 'select' && is_array($f['options'] ?? null)) {
                    $this->syncOptions((int) $existing['id'], $f['options']);
                }
            } else {
                $pageId = (int)($f['page_id'] ?? $f['step'] ?? 1);
                $newId = $this->insert([
                    'page_id'     => $pageId,
                    'step'        => $pageId,
                    'field_key'   => $key,
                    'section'     => $f['section'] ?? ('page-' . $pageId . '-custom'),
                    'label'       => $f['label']   ?? $key,
                    'field_type'  => $f['type']    ?? 'text',
                    'placeholder' => $f['placeholder'] ?? '',
                    'is_visible'  => 1,
                    'is_required' => 0,
                    'is_system'   => 0,
                    'sort_order'  => 999,
                ]);

                if (($f['type'] ?? 'text') === 'select' && is_array($f['options'] ?? null)) {
                    $this->syncOptions((int) $newId, $f['options']);
                }
            }
        }
    }

    /**
     * Deletes custom (non-system) fields by key. A field's options are removed
     * first, then the field itself, all in one transaction. System fields are
     * never deleted, whatever keys are passed in.
     */
    public function deleteCustomFields(array $keys): void
    {
        $keys = array_values(array_filter(array_map(
            fn($k) => is_string($k) ? trim($k) : '',
            $keys
        )));
        if (!$keys) {
            return;
        }

        $ids = array_column(
            $this->select('id')->whereIn('field_key', $keys)->where('is_system', 0)->findAll(),
            'id'
        );
        if (!$ids) {
            return;
        }

        $this->db->transStart();
        (new FormFieldOptionConfigModel())->whereIn('form_field_id', $ids)->delete();
        $this->whereIn('id', $ids)->delete();
        $this->db->transComplete();
    }

    /*Reset: delete custom fields, re-enable all system fields.*/
    public function resetToDefaults(): void
    {
        $this->where('is_system', 0)->delete();
        $this->where('is_system', 1)->set(['is_visible' => 1])->update();
    }
}