<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Admin-created (custom) form fields have no dedicated column, so their answers
 * are stored together as one JSON object, keyed by field_key. The column is
 * TEXT, not JSON, because the value is encrypted at rest
 * (see App\Casts\EncryptedJsonCast) and ciphertext is not valid JSON.
 */
class AddCustomFieldsToApplications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applications', [
            'custom_fields' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'is_fast_track',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('applications', 'custom_fields');
    }
}
