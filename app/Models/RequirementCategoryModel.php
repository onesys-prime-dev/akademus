<?php

namespace App\Models;

use CodeIgniter\Model;

class RequirementCategoryModel extends Model
{
    protected $table      = 'requirement_categories';
    protected $primaryKey = 'category_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['category_name', 'description', 'display_order', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->orderBy('category_id', 'ASC')
            ->findAll();
    }

    public function getCategoriesWithDocumentCount(): array
    {
        return $this->db->table('requirement_categories rc')
            ->select('rc.category_id, rc.category_name, rc.description, rc.display_order, rc.is_active, COUNT(r.requirement_id) as document_count')
            ->join('requirements r', 'r.category_id = rc.category_id AND r.is_active = 1', 'left')
            ->where('rc.is_active', 1)
            ->groupBy('rc.category_id')
            ->orderBy('rc.display_order', 'ASC')
            ->get()->getResultArray();
    }
}
