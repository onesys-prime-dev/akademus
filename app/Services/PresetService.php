<?php

namespace App\Services;

use App\Models\PresetModel;
use App\Models\PresetSectionModel;
use App\Models\ProgramModel;
use App\Models\FormPageModel;
use App\Services\ProgramRequirementProjectionService;

class PresetService
{
    protected $presetModel;
    protected $presetSectionModel;
    protected $programModel;
    protected $formPageModel;

    public function __construct()
    {
        $this->presetModel = new PresetModel();
        $this->presetSectionModel = new PresetSectionModel();
        $this->programModel = new ProgramModel();
        $this->formPageModel = new FormPageModel();
    }

    /**
     * Get the preset for a given program
     * Returns the program-specific preset or falls back to global preset
     */
    public function getPresetForProgram($programId)
    {
        // Get program
        $program = $this->programModel->find($programId);
        if (!$program) {
            return null;
        }

        // If program has a specific preset, use it
        if (!empty($program['preset_id'])) {
            return $this->presetModel->getPresetWithSections($program['preset_id']);
        }

        // Fall back to global preset
        return $this->getGlobalPreset();
    }

    /**
     * Get the global preset (applies to all programs)
     */
    public function getGlobalPreset()
    {
        $preset = $this->presetModel->getGlobalPreset();
        if ($preset) {
            $preset['sections'] = $this->presetSectionModel
                ->getSectionsWithDetails($preset['id']);
        }
        return $preset;
    }

    /**
     * Get all form sections for a given preset
     */
    public function getPresetSections($presetId)
    {
        return $this->presetSectionModel->getSectionsWithDetails($presetId);
    }

    /**
     * Get form pages for applicant based on their program
     * This is what the applicant form will load
     */
    public function getFormPagesForApplicant($programId)
    {
        $preset = $this->getPresetForProgram($programId);

        if (!$preset) {
            // If no preset found, return all active form pages
            return $this->formPageModel->getActivePages();
        }

        // Get the form page IDs from the preset
        $pageIds = array_map(function ($section) {
            return $section['form_page_id'];
        }, $preset['sections']);

        if (empty($pageIds)) {
            return [];
        }

        // Get the full page details in order
        $pages = [];
        foreach ($pageIds as $pageId) {
            $page = $this->formPageModel->find($pageId);
            if ($page && $page['is_enabled']) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    /**
     * Create a new preset with requirement category assignments.
     */
    public function createPreset($name, $description, $isGlobal, $formPageIds, $categoryIds = [])
    {
        $presetData = [
            'name'        => $name,
            'description' => $description,
            'is_global'   => $isGlobal ? 1 : 0,
            'is_active'   => 1,
        ];

        $presetId = $this->presetModel->createPresetWithSections(
            $presetData,
            $formPageIds,
            $categoryIds
        );

        // rebuild projection for programs already using this preset
        (new ProgramRequirementProjectionService())
            ->rebuildForPreset((int) $presetId);

        return $presetId;
    }

    /**
     * Update an existing preset with requirement category assignments.
     */
    public function updatePreset($presetId, $name, $description, $isGlobal, $formPageIds, $categoryIds = [])
    {
        $presetData = [
            'name'        => $name,
            'description' => $description,
            'is_global'   => $isGlobal ? 1 : 0,
        ];

        $ok = $this->presetModel->updatePresetWithSections(
            $presetId,
            $presetData,
            $formPageIds,
            $categoryIds
        );

        if ($ok) {
            (new ProgramRequirementProjectionService())
                ->rebuildForPreset((int) $presetId);
        }

        return $ok;
    }

    /**
     * Get requirement categories assigned to a program's preset.
     */
    public function getPresetRequirementCategories(int $programId): array
    {
        $preset = $this->getPresetForProgram($programId);
        if (!$preset) {
            return [];
        }

        return (new \App\Models\PresetRequirementCategoryModel())->getCategoriesForPreset($preset['id']);
    }

    /**
     * Assign preset to program
     */
    public function assignPresetToProgram($programId, $presetId)
    {
        $ok = \Config\Database::connect()
            ->table('programs')
            ->where('program_id', (int)$programId)
            ->update(['preset_id' => (int)$presetId]);

        if ($ok) {
            (new ProgramRequirementProjectionService())
                ->rebuildForProgram((int)$programId);
        }

        return $ok;
    }

    /**
     * Remove preset assignment from program (will use global preset)
     */
    public function removePresetFromProgram($programId)
    {
        return \Config\Database::connect()
            ->table('programs')
            ->where('program_id', (int)$programId)
            ->update(['preset_id' => null]);
    }

    /**
     * Get all available presets
     */
    public function getAllPresets()
    {
        return $this->presetModel->getActivePresets();
    }

    /**
     * Deactivate a preset
     */
    public function deactivatePreset($presetId)
    {
        return $this->presetModel->update($presetId, ['is_active' => 0]);
    }

    /**
     * Delete a preset
     */
    public function deletePreset($presetId)
    {
        $db = \Config\Database::connect();

        // Remove preset assignment from programs (bypass model field protection)
        $db->table('programs')
            ->where('preset_id', (int)$presetId)
            ->update(['preset_id' => null]);

        // Delete sections, requirement category links, then the preset
        $db->table('preset_sections')->where('preset_id', (int)$presetId)->delete();
        $db->table('preset_requirement_categories')->where('preset_id', (int)$presetId)->delete();
        $db->table('form_presets')->where('id', (int)$presetId)->delete();

        return true;
    }
}
