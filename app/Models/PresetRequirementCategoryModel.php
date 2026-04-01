<?php

namespace App\Models;

use CodeIgniter\Model;

class PresetRequirementCategoryModel extends Model
{
    protected $table      = 'preset_requirement_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['preset_id', 'requirement_category_id', 'sort_order'];

    public function getCategoriesForPreset(int $presetId): array
    {
        return $this->db->table('preset_requirement_categories prc')
            ->select('rc.category_id, rc.category_name, rc.description, prc.sort_order as preset_sort_order')
            ->join('requirement_categories rc', 'rc.category_id = prc.requirement_category_id')
            ->where('prc.preset_id', $presetId)
            ->orderBy('prc.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    public function getCategoryIdsForPreset(int $presetId): array
    {
        $rows = $this->where('preset_id', $presetId)->findAll();
        return array_column($rows, 'requirement_category_id');
    }
}
