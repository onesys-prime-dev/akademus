<?php

namespace App\Services;

class ProgramRequirementProjectionService
{
    public function rebuildForProgram(int $programId): void
    {
        $db = db_connect();

        $db->transStart();

        // wipe old projection
        $programCategoryIds = $db->table('program_requirement_categories')
            ->select('program_requirement_category_id')
            ->where('program_id', $programId)
            ->get()
            ->getResultArray();

        if ($programCategoryIds) {
            $ids = array_column($programCategoryIds, 'program_requirement_category_id');

            $db->table('program_requirements')
                ->whereIn('program_requirement_category_id', $ids)
                ->delete();
        }

        $db->table('program_requirement_categories')
            ->where('program_id', $programId)
            ->delete();

        // get program preset
        $program = $db->table('programs')
            ->select('preset_id')
            ->where('program_id', $programId)
            ->get()
            ->getRowArray();

        if (!$program || empty($program['preset_id'])) {
            $db->transComplete();
            return;
        }

        // categories from preset
        $presetCategories = $db->table('preset_requirement_categories')
            ->where('preset_id', $program['preset_id'])
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($presetCategories as $catIndex => $pc) {
            // insert program category
            $db->table('program_requirement_categories')->insert([
                'program_id'     => $programId,
                'category_id'    => $pc['requirement_category_id'],
                'display_order'  => $catIndex,
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $programRequirementCategoryId = $db->insertID();

            // requirements under category
            $requirements = $db->table('requirements')
                ->where('category_id', $pc['requirement_category_id'])
                ->where('is_active', 1)
                ->orderBy('display_order', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($requirements as $reqIndex => $req) {
                $db->table('program_requirements')->insert([
                    'program_requirement_category_id' => $programRequirementCategoryId,
                    'requirement_id'                  => $req['requirement_id'],
                    'display_order'                   => $reqIndex,
                    'is_active'                       => 1,
                    'is_mandatory'                    => $req['is_required'],
                    'is_fast_track_required'          => $req['is_fast_track_required'],
                    'created_at'                      => date('Y-m-d H:i:s'),
                    'updated_at'                      => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();
    }

    public function rebuildForPreset(int $presetId): void
    {
        $programs = db_connect()
            ->table('programs')
            ->select('program_id')
            ->where('preset_id', $presetId)
            ->get()
            ->getResultArray();

        foreach ($programs as $program) {
            $this->rebuildForProgram((int) $program['program_id']);
        }
    }

    public function rebuildForProgramsUsingCategory(int $categoryId): void
    {
        $db = db_connect();

        $programs = $db->table('programs p')
            ->select('DISTINCT p.program_id')
            ->join(
                'preset_requirement_categories prc',
                'prc.preset_id = p.preset_id'
            )
            ->where('prc.requirement_category_id', $categoryId)
            ->get()
            ->getResultArray();

        foreach ($programs as $program) {
            $this->rebuildForProgram((int) $program['program_id']);
        }
    }
}