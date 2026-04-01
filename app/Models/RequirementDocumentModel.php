<?php

namespace App\Models;

use CodeIgniter\Model;

class RequirementDocumentModel extends Model
{
    protected $table      = 'requirements';
    protected $primaryKey = 'requirement_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['category_id', 'requirement_name', 'description', 'is_required', 'display_order', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDocumentsForCategory(int $categoryId): array
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->orderBy('requirement_id', 'ASC')
            ->findAll();
    }

    public function getDocumentsForCategories(array $categoryIds): array
    {
        if (empty($categoryIds)) return [];

        return $this->whereIn('category_id', $categoryIds)
            ->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->orderBy('requirement_id', 'ASC')
            ->findAll();
    }
}
