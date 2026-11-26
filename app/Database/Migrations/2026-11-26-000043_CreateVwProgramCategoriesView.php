<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwProgramCategoriesView extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `vw_program_categories` AS select `prc`.`program_requirement_category_id` AS `program_requirement_category_id`,`prc`.`program_id` AS `program_id`,`prc`.`category_id` AS `category_id`,`rc`.`category_name` AS `category_name`,`rc`.`description` AS `category_description`,`prc`.`display_order` AS `display_order`,`prc`.`is_active` AS `is_active` from (`program_requirement_categories` `prc` join `requirement_categories` `rc` on(`rc`.`category_id` = `prc`.`category_id`)) order by `prc`.`program_id`,`prc`.`display_order`
            SQL);
    }

    public function down(): void
    {
        $this->db->query('DROP VIEW IF EXISTS `vw_program_categories`');
    }
}
