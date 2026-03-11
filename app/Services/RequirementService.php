<?php

namespace App\Services;

use App\Models\Requirements\RequirementCategoryModel;
use App\Models\Requirements\ProgramRequirementViewModel;

class RequirementService
{
    protected $categoryModel;
    protected $requirementModel;

    private $categories = null;
    private $requirements = null;

    public function __construct() {
        $this->categoryModel = new RequirementCategoryModel();
        $this->requirementModel = new ProgramRequirementViewModel();
    }

    /**
     * Get all requirement categories ordered by display order
     */
    public function getCategories() {
        if ($this->categories === null) {
            $this->categories = $this->categoryModel
                ->orderBy('display_order','ASC')
                ->where('is_active',1)
                ->findAll();
        }

        return $this->categories;
    }

    public function getRequirements($program_id) {
        if ($this->requirements === null) {
            $this->requirements = $this->requirementModel
                ->orderBy('category_id', 'ASC')
                ->orderBy('display_order', 'ASC')
                ->where('program_id', $program_id)
                ->findAll();
        }

        return $this->requirements;
    }

    public function getStepRequirements($program_id, $step) {
        $categories = $this->getCategories();
        $requirements = $this->getRequirements($program_id);

        $category = $categories[$step - 1] ?? null;

        if (!$category) {
            return [];
        }

        $categoryId = $category['category_id'];

        return array_values(array_filter($requirements, function ($req) use ($categoryId) {
            return $req['category_id'] == $categoryId;
        }));
    }
}