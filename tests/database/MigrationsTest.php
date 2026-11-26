<?php

namespace Tests\Database;

use Tests\Support\DatabaseTestCase;

/**
 * The migrations build the complete schema on an empty database.
 *
 * @internal
 */
final class MigrationsTest extends DatabaseTestCase
{
    private const VIEWS = ['vw_applicants', 'vw_program_categories', 'vw_program_requirements'];

    public function testEveryAppMigrationIsApplied(): void
    {
        $files   = service('migrations')->setNamespace('App')->findMigrations();
        $applied = array_column($this->db->table('migrations')->where('namespace', 'App')->get()->getResultArray(), 'class');

        $this->assertNotEmpty($files);

        foreach ($files as $migration) {
            $this->assertContains($migration->class, $applied, "Migration {$migration->class} was not applied.");
        }
    }

    public function testEveryMigrationCreatesItsTable(): void
    {
        $tables = $this->db->listTables();

        foreach (service('migrations')->setNamespace('App')->findMigrations() as $migration) {
            if (preg_match('/^Create(\w+)Table$/', $migration->name ?? '', $m) !== 1) {
                continue;
            }
            $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $m[1]));
            $this->assertContains($table, $tables, "Table {$table} is missing.");
        }
    }

    public function testViewsExistAndCanBeQueried(): void
    {
        foreach (self::VIEWS as $view) {
            $this->assertIsArray($this->db->query("SELECT * FROM `{$view}` LIMIT 1")->getResultArray(), "View {$view} is not queryable.");
        }
    }

    public function testForeignKeysAreEnforced(): void
    {
        $this->assertNotEmpty($this->db->getForeignKeyData('applications'), 'applications should have foreign keys.');
    }
}
