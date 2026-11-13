<?php
namespace App\Models;

use CodeIgniter\Model;

class FormFieldOptionConfigModel extends Model
{
    protected $table         = 'form_field_option_config';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['form_field_id', 'option_name'];

    /** Option names for one field, in the order they were added. */
    public function getNamesForField(int $fieldId): array
    {
        return array_column(
            $this->where('form_field_id', $fieldId)->orderBy('id')->findAll(),
            'option_name'
        );
    }

    /** Option names for many fields at once: [form_field_id => [option_name, ...]]. */
    public function getNamesForFields(array $fieldIds): array
    {
        if (!$fieldIds) {
            return [];
        }

        $grouped = [];
        foreach ($this->whereIn('form_field_id', $fieldIds)->orderBy('id')->findAll() as $row) {
            $grouped[(int) $row['form_field_id']][] = $row['option_name'];
        }
        return $grouped;
    }
}
