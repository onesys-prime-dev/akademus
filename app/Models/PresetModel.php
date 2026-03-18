<?php

namespace App\Models;

use CodeIgniter\Model;

class PresetModel extends Model
{
    protected $table      = 'form_presets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'description', 'is_global', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all active presets
     */
    public function getActivePresets()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get global preset (applies to all programs)
     */
    public function getGlobalPreset()
    {
        return $this->where('is_global', 1)->where('is_active', 1)->first();
    }

    /**
     * Get preset with all its sections
     */
    public function getPresetWithSections($presetId)
    {
        $preset = $this->find($presetId);
        if (!$preset) {
            return null;
        }

        $sectionModel = new PresetSectionModel();
        $preset['sections'] = $sectionModel
            ->where('preset_id', $presetId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $preset;
    }

    /**
     * Get all sections for a preset (useful for loading form)
     */
    public function getSections($presetId)
    {
        $sectionModel = new PresetSectionModel();
        return $sectionModel
            ->where('preset_id', $presetId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Create preset with sections and requirement category assignments.
     */
    public function createPresetWithSections($presetData, $sectionIds, $categoryIds = [])
    {
        $this->db->transStart();

        $this->insert($presetData);
        $presetId = $this->getInsertID();

        $sectionModel = new PresetSectionModel();
        foreach ($sectionIds as $index => $sectionId) {
            $sectionModel->insert([
                'preset_id'    => $presetId,
                'form_page_id' => $sectionId,
                'sort_order'   => $index,
                'is_required'  => 1,
            ]);
        }

        $prcModel = new \App\Models\PresetRequirementCategoryModel();
        foreach ($categoryIds as $index => $catId) {
            if (empty($catId)) continue;
            $prcModel->insert([
                'preset_id'               => $presetId,
                'requirement_category_id' => (int)$catId,
                'sort_order'              => $index,
            ]);
        }

        $this->db->transComplete();

        return $presetId;
    }

    /**
     * Update preset with new sections and requirement category assignments.
     */
    public function updatePresetWithSections($presetId, $presetData, $sectionIds, $categoryIds = [])
    {
        $this->db->transStart();

        $this->update($presetId, $presetData);

        // Replace sections
        $sectionModel = new PresetSectionModel();
        $sectionModel->where('preset_id', $presetId)->delete();
        foreach ($sectionIds as $index => $sectionId) {
            $sectionModel->insert([
                'preset_id'    => $presetId,
                'form_page_id' => $sectionId,
                'sort_order'   => $index,
                'is_required'  => 1,
            ]);
        }

        // Replace requirement category assignments
        $prcModel = new \App\Models\PresetRequirementCategoryModel();
        $prcModel->where('preset_id', $presetId)->delete();
        foreach ($categoryIds as $index => $catId) {
            if (empty($catId)) continue;
            $prcModel->insert([
                'preset_id'               => $presetId,
                'requirement_category_id' => (int)$catId,
                'sort_order'              => $index,
            ]);
        }

        $this->db->transComplete();

        return true;
    }
}
