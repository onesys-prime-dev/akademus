<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwProgramRequirementsView extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `vw_program_requirements` AS select `pr`.`program_requirement_id` AS `program_requirement_id`,`prc`.`program_id` AS `program_id`,`prc`.`program_requirement_category_id` AS `program_requirement_category_id`,`rc`.`category_name` AS `category_name`,`prc`.`display_order` AS `category_order`,`r`.`requirement_id` AS `requirement_id`,`r`.`requirement_name` AS `requirement_name`,`r`.`description` AS `requirement_description`,`pr`.`display_order` AS `requirement_order`,`pr`.`is_active` AS `is_active`,`pr`.`is_mandatory` AS `is_mandatory` from (((`program_requirements` `pr` join `program_requirement_categories` `prc` on(`prc`.`program_requirement_category_id` = `pr`.`program_requirement_category_id`)) join `requirement_categories` `rc` on(`rc`.`category_id` = `prc`.`category_id`)) join `requirements` `r` on(`r`.`requirement_id` = `pr`.`requirement_id`)) order by `prc`.`program_id`,`prc`.`display_order`,`pr`.`display_order`
            SQL);
    }

    public function down(): void
    {
        $this->db->query('DROP VIEW IF EXISTS `vw_program_requirements`');
    }
}
