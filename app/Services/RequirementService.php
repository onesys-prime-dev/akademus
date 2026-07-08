<?php

namespace App\Services;

class RequirementService
{
    private ?array $categories = null;
    private ?array $requirements = null;
    private $cachedProgId = null;

    private function resetIfNeeded($program_id): void
    {
        if ($this->cachedProgId !== $program_id) {
            $this->cachedProgId = $program_id;
            $this->categories = null;
            $this->requirements = null;
        }
    }

    public function getCategories($program_id): array
    {
        $this->resetIfNeeded($program_id);

        if ($this->categories === null) {
            $this->categories = db_connect()
                ->table('program_requirement_categories prc')
                ->select([
                    'prc.program_requirement_category_id',
                    'rc.category_name',
                    'rc.description',
                    'prc.display_order',
                    'prc.is_active',
                ])
                ->join(
                    'requirement_categories rc',
                    'rc.category_id = prc.category_id'
                )
                ->where('prc.program_id', $program_id)
                ->where('prc.is_active', 1)
                ->where('rc.is_active', 1)
                ->orderBy('prc.display_order', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->categories;
    }

    public function getRequirements($program_id): array
    {
        $this->resetIfNeeded($program_id);

        if ($this->requirements === null) {
            $rows = db_connect()
                ->table('program_requirements pr')
                ->select([
                    'pr.program_requirement_id',
                    'pr.program_requirement_category_id',
                    'r.requirement_name',
                    'r.description',
                    'pr.is_mandatory',
                    'pr.is_fast_track_required',
                    'pr.display_order',
                    'pr.is_active',
                ])
                ->join(
                    'requirements r',
                    'r.requirement_id = pr.requirement_id'
                )
                ->join(
                    'program_requirement_categories prc',
                    'prc.program_requirement_category_id = pr.program_requirement_category_id'
                )
                ->where('prc.program_id', $program_id)
                ->where('prc.is_active', 1)
                ->where('pr.is_active', 1)
                ->where('r.is_active', 1)
                ->orderBy('prc.display_order', 'ASC')
                ->orderBy('pr.display_order', 'ASC')
                ->get()
                ->getResultArray();

            $this->requirements = array_map(
                fn($row) => [
                    // FIXED: real projected ID
                    'program_requirement_id' => (int) $row['program_requirement_id'],

                    'program_requirement_category_id' =>
                        (int) $row['program_requirement_category_id'],

                    'requirement_name' =>
                        $row['requirement_name'],

                    'requirement_description' =>
                        $row['description'] ?? '',

                    'is_required' =>
                        (int) $row['is_mandatory'],

                    'is_fast_track_required' =>
                        (int) $row['is_fast_track_required'],

                    'requirement_order' =>
                        (int) $row['display_order'],

                    'is_active' =>
                        (int) $row['is_active'],
                ],
                $rows
            );
        }

        return $this->requirements;
    }

    public function getStepRequirements($program_id, $step): array
    {
        $categories = $this->getCategories($program_id);
        $category = $categories[$step - 1] ?? null;

        if (!$category) {
            return [];
        }

        return array_values(array_filter(
            $this->getRequirements($program_id),
            fn($req) =>
                $req['program_requirement_category_id']
                == $category['program_requirement_category_id']
        ));
    }
}