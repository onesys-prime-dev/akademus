<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseFormFieldModel extends Model
{
    protected $table = 'course_form_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'course_id',
        'field_name',
        'field_type',
        'label',
        'placeholder',
        'is_required',
        'validation_rules',
        'options',
        'display_order',
        'override_global',
        'is_active',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getFieldsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('display_order', 'ASC')
            ->findAll();
    }

    public function getOverriddenFields($courseId)
    {
        $rows = $this->where('course_id', $courseId)
            ->where('override_global', true)
            ->findAll();
        return array_column($rows, 'field_name');
    }
}
