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
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['page_id']][$row['section']][] = $row;
        }
        return $grouped;
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
            } else {
                $pageId = (int)($f['page_id'] ?? $f['step'] ?? 1);
                $this->insert([
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
            }
        }
    }

    /*Reset: delete custom fields, re-enable all system fields.*/
    public function resetToDefaults(): void
    {
        $this->where('is_system', 0)->delete();
        $this->where('is_system', 1)->set(['is_visible' => 1])->update();
    }
}