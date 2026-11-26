<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * payment_method_account and remember_me_tokens were created without a primary key or AUTO_INCREMENT
 * (inherited from the original SQL dump), so every inserted row got id 0. Renumber existing rows,
 * then make the id column an AUTO_INCREMENT primary key.
 */
class AddMissingPrimaryKeys extends Migration
{
    /**
     * table => id column
     */
    private const TABLES = [
        'payment_method_account' => 'payment_method_account_id',
        'remember_me_tokens'     => 'id',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table => $id) {
            $next = (int) ($this->db->query("SELECT COALESCE(MAX(`{$id}`), 0) AS m FROM `{$table}`")->getRow()->m) + 1;

            // Rows are indistinguishable while they share id 0, so give them new ids one at a time.
            while ($this->db->query("SELECT 1 FROM `{$table}` WHERE `{$id}` = 0 LIMIT 1")->getNumRows() > 0) {
                $this->db->query("UPDATE `{$table}` SET `{$id}` = ? WHERE `{$id}` = 0 LIMIT 1", [$next++]);
            }

            $this->db->query("ALTER TABLE `{$table}` ADD PRIMARY KEY (`{$id}`), MODIFY `{$id}` INT(11) NOT NULL AUTO_INCREMENT");
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table => $id) {
            $this->db->query("ALTER TABLE `{$table}` MODIFY `{$id}` INT(11) NOT NULL, DROP PRIMARY KEY");
        }
    }
}
