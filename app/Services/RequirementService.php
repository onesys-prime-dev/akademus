<?php

namespace App\Services;

use App\Models\RequirementCategoryModel;
use App\Models\RequirementDocumentModel;
use App\Models\PresetRequirementCategoryModel;

/**
 * RequirementService
 *
 * Derives document requirements from the requirement categories
 * assigned to a program's preset.
 */
class RequirementService
{
    private $categories   = null;
    private $requirements = null;
    private $cachedProgId = null;

    private function presetService(): PresetService
    {
        static $svc = null;
        if (!$svc) $svc = new PresetService();
        return $svc;
    }

    private function catModel(): RequirementCategoryModel
    {
        static $m = null;
        if (!$m) $m = new RequirementCategoryModel();
        return $m;
    }

    private function docModel(): RequirementDocumentModel
    {
        static $m = null;
        if (!$m) $m = new RequirementDocumentModel();
        return $m;
    }

    private function prcModel(): PresetRequirementCategoryModel
    {
        static $m = null;
        if (!$m) $m = new PresetRequirementCategoryModel();
        return $m;
    }

    private function resetIfNeeded($program_id): void
    {
        if ($this->cachedProgId !== $program_id) {
            $this->categories   = null;
            $this->requirements = null;
            $this->cachedProgId = $program_id;
        }
    }

    /**
     * Get all requirement categories for a program's preset.
     * Each element: program_requirement_category_id, category_name,
     *               category_description, display_order, is_active.
     */
    public function getCategories($program_id): array
    {
        $this->resetIfNeeded($program_id);

        if ($this->categories !== null) {
            return $this->categories;
        }

        $preset = $this->presetService()->getPresetForProgram($program_id);

        if (!$preset) {
            $this->categories = [];
            return [];
        }

        $rows = $this->prcModel()->getCategoriesForPreset($preset['id']);

        $this->categories = array_values(array_map(function ($row, $index) {
            return [
                'program_requirement_category_id' => (int) $row['category_id'],
                'category_name'                   => $row['category_name'],
                'category_description'            => $row['description'] ?? '',
                'display_order'                   => $index + 1,
                'is_active'                       => 1,
            ];
        }, $rows, array_keys($rows)));

        return $this->categories;
    }

    /**
     * Get all requirements (documents) for a program's preset.
     * Each element: program_requirement_id, program_requirement_category_id,
     *               requirement_name, requirement_description, is_required,
     *               requirement_order, is_active.
     */
    public function getRequirements($program_id): array
    {
        $this->resetIfNeeded($program_id);

        if ($this->requirements !== null) {
            return $this->requirements;
        }

        $categories = $this->getCategories($program_id);

        if (empty($categories)) {
            $this->requirements = [];
            return [];
        }

        $catIds = array_column($categories, 'program_requirement_category_id');
        $docs   = $this->docModel()->getDocumentsForCategories($catIds);

        $this->requirements = array_values(array_map(function ($doc) {
            return [
                'program_requirement_id'          => (int) $doc['requirement_id'],
                'program_requirement_category_id' => (int) $doc['category_id'],
                'requirement_name'                => $doc['requirement_name'],
                'requirement_description'         => $doc['description'] ?? '',
                'is_required'                     => (int) ($doc['is_required'] ?? 1),
                'requirement_order'               => (int) $doc['display_order'],
                'is_active'                       => 1,
            ];
        }, $docs));

        return $this->requirements;
    }

    /**
     * Get requirements belonging to the category at step position $step.
     */
    public function getStepRequirements($program_id, $step): array
    {
        $categories = $this->getCategories($program_id);
        $category   = $categories[$step - 1] ?? null;

        if (!$category) {
            return [];
        }

        $categoryId   = $category['program_requirement_category_id'];
        $requirements = $this->getRequirements($program_id);

        return array_values(array_filter($requirements, function ($req) use ($categoryId) {
            return $req['program_requirement_category_id'] == $categoryId;
        }));
    }
}
