<?php


namespace App\Models;

use CodeIgniter\Model;

class FormPageModel extends Model
{
    protected $table      = 'form_pages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'is_enabled', 'is_system', 'sort_order'];
    protected $useTimestamps = true;

    /**
     * All pages ordered by sort_order (both enabled and disabled).
     */
    public function getAllOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * Only enabled pages — what the applicant form renders.
     */
    public function getEnabledOrdered(): array
    {
        return $this->where('is_enabled', 1)->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * Bulk reorder from drag-and-drop payload.
     * $order = [ ['id' => 1, 'sort_order' => 10], ... ]
     */
    public function reorder(array $order): void
    {
        foreach ($order as $item) {
            $this->update((int)$item['id'], ['sort_order' => (int)$item['sort_order']]);
        }
    }

    /**
     * Toggle enabled/disabled. System pages can still be toggled.
     */
    public function toggle(int $id): array
    {
        $page = $this->find($id);
        if (!$page) return ['status' => 'error', 'message' => 'Page not found'];

        $newState = $page['is_enabled'] ? 0 : 1;
        $this->update($id, ['is_enabled' => $newState]);
        return ['status' => 'success', 'is_enabled' => $newState];
    }

    /**
     * Delete only non-system pages.
     */
    public function deletePage(int $id): array
    {
        $page = $this->find($id);
        if (!$page) return ['status' => 'error', 'message' => 'Page not found'];
        if ($page['is_system']) return ['status' => 'error', 'message' => 'System pages cannot be deleted'];

        $this->delete($id);
        return ['status' => 'success'];
    }

    /**
     * Get next sort_order value (appends to end).
     */
    public function nextSortOrder(): int
    {
        $max = $this->selectMax('sort_order')->first();
        return (int)($max['sort_order'] ?? 0) + 10;
    }
}