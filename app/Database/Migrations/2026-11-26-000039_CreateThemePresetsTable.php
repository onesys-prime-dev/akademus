<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThemePresetsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'primary_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'primary_dark' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'primary_light' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'accent_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'accent_text' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'sidebar_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'sidebar_active_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'banner_bg_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'banner_box_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'button_primary_bg' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'button_primary_text' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'button_accent_bg' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'button_accent_text' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'header_text_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'progress_active_color' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('theme_presets', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('theme_presets', true);
    }
}
