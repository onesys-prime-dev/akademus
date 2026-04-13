<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixPaymentTables extends Migration
{
    public function up()
    {
        // ── 1. Fix payment table if it has the old 5-column schema ────────────
        // The old schema had: payment_id, amount, payment_date, payment_method,
        // payment_status. The new schema needs: applicant_id, payment_fee_type_id,
        // payment_method_id, payment_method_account_id, reference_num,
        // proof_of_payment, amount_paid, remarks, created_at, updated_at.
        // If the column `applicant_id` already exists the table is up-to-date.
        $hasNewSchema = $this->db->fieldExists('applicant_id', 'payment');

        if (! $hasNewSchema) {
            $this->db->query('DROP TABLE IF EXISTS `payment`');

            $this->forge->addField([
                'payment_id' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'applicant_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => false,
                ],
                'payment_fee_type_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => false,
                ],
                'payment_method_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => false,
                ],
                'payment_method_account_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'default'  => null,
                ],
                'reference_num' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'proof_of_payment' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                ],
                'amount_paid' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => false,
                ],
                'payment_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['PENDING', 'PROCESSING', 'PAID', 'REJECTED'],
                    'default'    => 'PENDING',
                ],
                'payment_date' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                ],
                'remarks' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                ],
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                ],
                'updated_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                ],
            ]);
            $this->forge->addKey('payment_id', true);
            $this->forge->addKey('applicant_id');
            $this->forge->addKey('payment_fee_type_id');
            $this->forge->createTable('payment');
        }

        // ── 2. Create payment_method_account table if missing ─────────────────
        if ($this->db->tableExists('payment_method_account')) {
            return;
        }

        $this->forge->addField([
            'payment_method_account_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'payment_method_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'account_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('payment_method_account_id', true);
        $this->forge->addKey('payment_method_id');
        $this->forge->createTable('payment_method_account');
    }

    public function down()
    {
        $this->forge->dropTable('payment_method_account', true);
        $this->forge->dropTable('payment', true);
    }
}
