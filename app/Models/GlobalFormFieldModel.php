<?php

namespace App\Models;

use CodeIgniter\Model;

class GlobalFormFieldModel extends Model
{
    protected $table = 'global_form_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'field_name',
        'field_type',
        'label',
        'placeholder',
        'is_required',
        'validation_rules',
        'options',
        'display_order',
        'is_active',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveFields()
    {
        return $this->where('is_active', true)
            ->orderBy('display_order', 'ASC')
            ->findAll();
    }

    public function getFieldByName($fieldName)
    {
        return $this->where('field_name', $fieldName)
            ->where('is_active', true)
            ->first();
    }
}
