<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Widens PII columns to TEXT (encrypted values are far longer than the
 * plaintext) and encrypts any existing plaintext data in place, so it
 * matches what App\Casts\EncryptedCast expects to find on read.
 */
class EncryptPersonalAndFamilyPii extends Migration
{
    private const PERSONAL_FIELDS = [
        'firstname',
        'middlename',
        'lastname',
        'date_of_birth',
        'place_of_birth',
        'home_address',
        'contact_number',
        'email_address',
    ];

    private const FAMILY_FIELDS = [
        'father_firstname',
        'father_middlename',
        'father_lastname',
        'father_occupation',
        'father_contact_number',
        'mother_firstname',
        'mother_middlename',
        'mother_lastname',
        'mother_occupation',
        'mother_contact_number',
        'guardian_name',
        'guardian_relationship',
        'guardian_contact_number',
        'annual_family_income',
    ];

    public function up()
    {
        $this->widenColumns('personal_informations', self::PERSONAL_FIELDS);
        $this->widenColumns('family_backgrounds', self::FAMILY_FIELDS);

        $this->encryptExistingRows('personal_informations', 'id', self::PERSONAL_FIELDS);
        $this->encryptExistingRows('family_backgrounds', 'id', self::FAMILY_FIELDS);
    }

    public function down()
    {
        $this->decryptExistingRows('personal_informations', 'id', self::PERSONAL_FIELDS);
        $this->decryptExistingRows('family_backgrounds', 'id', self::FAMILY_FIELDS);

        // Columns are left as TEXT; the original VARCHAR/DATE lengths are not restored.
    }

    private function widenColumns(string $table, array $fields): void
    {
        $fieldSpec = [];

        foreach ($fields as $field) {
            $fieldSpec[$field] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        $this->forge->modifyColumn($table, $fieldSpec);
    }

    private function encryptExistingRows(string $table, string $primaryKey, array $fields): void
    {
        $encrypter = service('encrypter');
        $rows      = $this->db->table($table)->get()->getResultArray();

        foreach ($rows as $row) {
            $update = [];

            foreach ($fields as $field) {
                $value = $row[$field] ?? null;

                $update[$field] = ($value === null || $value === '')
                    ? $value
                    : base64_encode($encrypter->encrypt((string) $value));
            }

            $this->db->table($table)->where($primaryKey, $row[$primaryKey])->update($update);
        }
    }

    private function decryptExistingRows(string $table, string $primaryKey, array $fields): void
    {
        $encrypter = service('encrypter');
        $rows      = $this->db->table($table)->get()->getResultArray();

        foreach ($rows as $row) {
            $update = [];

            foreach ($fields as $field) {
                $value = $row[$field] ?? null;

                $update[$field] = ($value === null || $value === '')
                    ? $value
                    : $encrypter->decrypt(base64_decode((string) $value, true));
            }

            $this->db->table($table)->where($primaryKey, $row[$primaryKey])->update($update);
        }
    }
}
