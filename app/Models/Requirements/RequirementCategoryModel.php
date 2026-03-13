<?php

namespace App\Models\Requirements;

use CodeIgniter\Model;

class RequirementCategoryModel extends Model
{
    protected $table = 'requirement_categories';
    protected $primaryKey = 'category_id';
}