<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwApplicantsView extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `vw_applicants` AS select `a`.`applicant_id` AS `applicant_id`,`u`.`user_id` AS `user_id`,`a`.`program_id` AS `program_id`,`p`.`program_name` AS `program_name`,`u`.`firstname` AS `firstname`,`u`.`lastname` AS `lastname`,`u`.`email` AS `email`,`u`.`mobile` AS `mobile`,`u`.`role` AS `role`,`a`.`applicant_number` AS `applicant_number`,`a`.`password` AS `password`,`a`.`temp_password` AS `temp_password`,`a`.`application_status` AS `application_status`,`a`.`application_date` AS `application_date`,`a`.`created_at` AS `created_at` from ((`applicants` `a` join `users` `u` on(`a`.`user_id` = `u`.`user_id`)) join `programs` `p` on(`a`.`program_id` = `p`.`program_id`))
            SQL);
    }

    public function down(): void
    {
        $this->db->query('DROP VIEW IF EXISTS `vw_applicants`');
    }
}
