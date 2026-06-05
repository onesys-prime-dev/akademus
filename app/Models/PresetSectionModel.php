<?php

namespace App\Models;

use CodeIgniter\Model;

class PresetSectionModel extends Model
{
    protected $table      = 'preset_sections';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['preset_id', 'form_page_id', 'sort_order', 'is_required'];

    /**
     * Get sections with page details
     */
    public function getSectionsWithDetails($presetId)
    {
        return $this->db->table('preset_sections')
            ->select('preset_sections.*, form_pages.title as section_title, form_pages.description')
            ->join('form_pages', 'form_pages.id = preset_sections.form_page_id', 'LEFT')
            ->where('preset_sections.preset_id', $presetId)
            ->orderBy('preset_sections.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get form page IDs for a preset
     */
    public function getPageIds($presetId)
    {
        return $this->select('form_page_id')
            ->where('preset_id', $presetId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Update section order
     */
    public function updateSectionOrder($presetId, $sections)
    {
        foreach ($sections as $order => $sectionId) {
            $this->where('preset_id', $presetId)
                ->where('id', $sectionId)
                ->update(null, ['sort_order' => $order]);
        }
    }
}
