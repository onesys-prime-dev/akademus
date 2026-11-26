<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'payment_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'applicant_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'payment_fee_type_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'payment_method_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'payment_method_account_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'payment_source' => ['type' => 'ENUM', 'constraint' => ['COUNTER', 'ONLINE'], 'null' => false, 'default' => 'COUNTER'],
            'reference_num' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'external_reference' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'smartpay_checkout_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'proof_of_payment' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'amount_paid' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'payment_status' => ['type' => 'ENUM', 'constraint' => ['PENDING', 'PROCESSING', 'PAID', 'REJECTED'], 'null' => false, 'default' => 'PENDING'],
            'payment_date' => ['type' => 'DATETIME', 'null' => true],
            'remarks' => ['type' => 'TEXT', 'null' => true],
            'reviewed_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'reviewed_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('payment_id');
        $this->forge->addKey('applicant_id', false, false, 'applicant_id');
        $this->forge->addKey('payment_fee_type_id', false, false, 'payment_fee_type_id');
        $this->forge->createTable('payment', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment', true);
    }
}
