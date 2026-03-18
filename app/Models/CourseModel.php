<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'code', 'description', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'code' => 'required|min_length[2]|max_length[50]|is_unique[courses.code]',
    ];

    public function getActiveCourses()
    {
        return $this->where('is_active', true)->findAll();
    }

    public function getCourseWithFields($courseId)
    {
        // Returns course with merged form fields (global + course-specific)
        $course = $this->find($courseId);
        if (!$course) return null;

        $course['form_fields'] = model('FormFieldService')
            ->getMergedFormFields($courseId);

        return $course;
    }
}
