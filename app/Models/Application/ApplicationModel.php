<?php

namespace App\Models\Application;

use App\Casts\EncryptedJsonCast;
use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'application_id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'applicant_type',
        'program_id',
        'school_year',
        'semester',
        'application_status',
        'revision_note',
        'revision_pages',
        'is_fast_track',
        'custom_fields',
    ];

    protected array $castHandlers = [
        'encrypted_json' => EncryptedJsonCast::class,
    ];

    // Answers to admin-created fields, keyed by field_key; encrypted at rest.
    protected array $casts = [
        'custom_fields' => '?encrypted_json',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'application_date';
    protected $updatedField  = 'updated_at';
}