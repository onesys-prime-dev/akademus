<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;
use RuntimeException;

/**
 * Form presets, the programs offered, and each program's requirement checklist.
 *
 * Needs FormPageSeeder (pages 1-4) and RequirementSeeder (the requirement library) first.
 * A program's checklist is projected from its preset the same way
 * App\Services\ProgramRequirementProjectionService::rebuildForProgram() does it, but only when the
 * program has no checklist yet, so a re-run never resets one an admin has customised.
 */
class ProgramSeeder extends Seeder
{
    use InsertsMissingRows;

    /**
     * preset name => form page ids (in order)
     */
    private const PRESETS = [
        'Computer Science'       => [1, 2, 3, 4],
        'Information Technology' => [1, 2, 3, 4],
    ];

    /**
     * program_code => [program_name, preset name or null]
     */
    private const PROGRAMS = [
        'BSCS' => ['Bachelor of Science in Computer Science', 'Computer Science'],
        'BSIT' => ['Bachelor of Science in Information Technology', 'Information Technology'],
        'BSIS' => ['Bachelor of Science in Information Systems', null],
    ];

    public function run(): void
    {
        $categoryIds = [];

        foreach (array_keys(RequirementSeeder::CATEGORIES) as $category) {
            $row = $this->db->table('requirement_categories')->where('category_name', $category)->get()->getRowArray();
            if ($row === null) {
                throw new RuntimeException("ProgramSeeder: requirement category '{$category}' is missing; run RequirementSeeder first.");
            }
            $categoryIds[] = (int) $row['category_id'];
        }

        $presetIds = [];

        foreach (self::PRESETS as $name => $pageIds) {
            $presetId         = $this->insertIfMissing('form_presets', ['name' => $name], ['description' => '', 'is_global' => 0, 'is_active' => 1]);
            $presetIds[$name] = $presetId;

            foreach ($pageIds as $order => $pageId) {
                $this->insertIfMissing('preset_sections', ['preset_id' => $presetId, 'form_page_id' => $pageId], ['sort_order' => $order, 'is_required' => 1]);
            }

            foreach ($categoryIds as $order => $categoryId) {
                $this->insertIfMissing('preset_requirement_categories', ['preset_id' => $presetId, 'requirement_category_id' => $categoryId], ['sort_order' => $order]);
            }
        }

        foreach (self::PROGRAMS as $code => [$name, $preset]) {
            $programId = $this->insertIfMissing('programs', ['program_code' => $code], [
                'program_name' => $name, 'preset_id' => $preset !== null ? $presetIds[$preset] : null,
            ], 'program_id');

            $this->projectRequirements($programId);
        }
    }

    private function projectRequirements(int $programId): void
    {
        if ($this->db->table('program_requirement_categories')->where('program_id', $programId)->countAllResults() > 0) {
            return;
        }

        $program = $this->db->table('programs')->select('preset_id')->where('program_id', $programId)->get()->getRowArray();
        if (empty($program['preset_id'])) {
            return;
        }

        $now              = date('Y-m-d H:i:s');
        $presetCategories = $this->db->table('preset_requirement_categories')
            ->where('preset_id', $program['preset_id'])->orderBy('sort_order', 'ASC')->get()->getResultArray();

        foreach ($presetCategories as $catIndex => $pc) {
            $this->db->table('program_requirement_categories')->insert([
                'program_id'    => $programId, 'category_id' => $pc['requirement_category_id'],
                'display_order' => $catIndex, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $programCategoryId = (int) $this->db->insertID();

            $requirements = $this->db->table('requirements')
                ->where('category_id', $pc['requirement_category_id'])->where('is_active', 1)
                ->orderBy('display_order', 'ASC')->get()->getResultArray();

            foreach ($requirements as $reqIndex => $req) {
                $this->db->table('program_requirements')->insert([
                    'program_requirement_category_id' => $programCategoryId,
                    'requirement_id'                  => $req['requirement_id'],
                    'display_order'                   => $reqIndex,
                    'is_active'                       => 1,
                    'is_mandatory'                    => $req['is_required'],
                    'is_fast_track_required'          => $req['is_fast_track_required'],
                    'created_at'                      => $now,
                    'updated_at'                      => $now,
                ]);
            }
        }
    }
}
