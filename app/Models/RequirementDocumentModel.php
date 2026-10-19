<?php

namespace App\Models;

use CodeIgniter\Model;

class RequirementDocumentModel extends Model
{
    protected $table      = 'requirements';
    protected $primaryKey = 'requirement_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['category_id', 'requirement_name', 'description', 'is_required', 'is_fast_track_required', 'display_order', 'is_active'];
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

    /**
     * Every active document across every category, with its current Fast
     * Track flag — for the single-page Fast Track document configuration
     * screen (as opposed to the other methods here, which are scoped to
     * one category at a time for the per-category edit screens).
     */
    public function getAllFastTrackFlaggableDocuments(): array
    {
        return $this->db->table('requirements r')
            ->select('r.requirement_id, r.requirement_name, r.description, r.is_required, r.is_fast_track_required, rc.category_id, rc.category_name')
            ->join('requirement_categories rc', 'rc.category_id = r.category_id')
            ->where('r.is_active', 1)
            ->where('rc.is_active', 1)
            ->orderBy('rc.display_order', 'ASC')
            ->orderBy('r.display_order', 'ASC')
            ->get()->getResultArray();
    }
}
