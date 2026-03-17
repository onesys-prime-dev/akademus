<?php

namespace App\Models\Requirements;

use CodeIgniter\Model;

class RequirementCategoryModel extends Model
{
    protected $table = 'vw_program_categories';
    protected $primaryKey = 'program_requirement_category_id';
}