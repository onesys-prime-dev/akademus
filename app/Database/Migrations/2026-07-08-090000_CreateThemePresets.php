<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThemePresets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'               => ['type' => 'VARCHAR', 'constraint' => 100],
            'primary_color'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'primary_dark'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'primary_light'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'accent_color'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'accent_text'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'sidebar_color'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'sidebar_active_color' => ['type' => 'VARCHAR', 'constraint' => 20],
            'banner_bg_color'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'banner_box_color'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'button_primary_bg'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'button_primary_text' => ['type' => 'VARCHAR', 'constraint' => 20],
            'button_accent_bg'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'button_accent_text' => ['type' => 'VARCHAR', 'constraint' => 20],
            'header_text_color'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'progress_active_color' => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('theme_presets');
    }

    public function down()
    {
        $this->forge->dropTable('theme_presets', true);
    }
}
