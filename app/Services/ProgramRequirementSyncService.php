<?php

namespace App\Services;

class ProgramRequirementSyncService
{
    public function syncRequirement(int $requirementId, int $categoryId): void
    {
        $db = db_connect();

        /*
        |--------------------------------------------------------------------------
        | Get source requirement
        |--------------------------------------------------------------------------
        */
        $requirement = $db->table('requirements')
            ->select('is_required, is_fast_track_required, is_active')
            ->where('requirement_id', $requirementId)
            ->get()
            ->getRowArray();

        if (! $requirement) {
            return;
        }

        $isMandatory        = (int) $requirement['is_required'];
        $isFastTrackRequired = (int) $requirement['is_fast_track_required'];
        $isActive           = (int) $requirement['is_active'];

        /*
        |--------------------------------------------------------------------------
        | Find projected program categories
        |--------------------------------------------------------------------------
        */
        $programCategories = $db->table('program_requirement_categories')
            ->select('program_requirement_category_id')
            ->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        foreach ($programCategories as $pc) {
            $programRequirementCategoryId =
                (int) $pc['program_requirement_category_id'];

            $existing = $db->table('program_requirements')
                ->where([
                    'program_requirement_category_id' => $programRequirementCategoryId,
                    'requirement_id'                 => $requirementId,
                ])
                ->get()
                ->getRowArray();

            /*
            |--------------------------------------------------------------------------
            | UPDATE existing projection
            |--------------------------------------------------------------------------
            */
            if ($existing) {
                $db->table('program_requirements')
                    ->where(
                        'program_requirement_id',
                        $existing['program_requirement_id']
                    )
                    ->update([
                        'is_mandatory'            => $isMandatory,
                        'is_fast_track_required'  => $isFastTrackRequired,
                        'is_active'               => $isActive,
                        'updated_at'              => date('Y-m-d H:i:s'),
                    ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT new projection
            |--------------------------------------------------------------------------
            */
            $maxOrder = $db->table('program_requirements')
                ->selectMax('display_order')
                ->where(
                    'program_requirement_category_id',
                    $programRequirementCategoryId
                )
                ->get()
                ->getRowArray();

            $displayOrder = ($maxOrder['display_order'] ?? 0) + 10;

            $db->table('program_requirements')->insert([
                'program_requirement_category_id' => $programRequirementCategoryId,
                'requirement_id'                 => $requirementId,
                'display_order'                  => $displayOrder,
                'is_active'                      => $isActive,
                'is_mandatory'                   => $isMandatory, // ← synced
                'is_fast_track_required'         => $isFastTrackRequired, // ← synced
            ]);
        }
    }

    public function deactivateRequirement(int $requirementId): void
    {
        db_connect()
            ->table('program_requirements')
            ->where('requirement_id', $requirementId)
            ->update([
                'is_active'  => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}