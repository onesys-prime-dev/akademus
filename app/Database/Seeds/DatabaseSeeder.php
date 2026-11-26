<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Single entry point that seeds a freshly migrated database with everything appropriate for it:
 *
 *   php spark migrate && php spark db:seed DatabaseSeeder
 *
 * 1. ReferenceDataSeeder  - always (settings, form pages, requirements, programs, fees, payment methods).
 * 2. DemoUserSeeder       - every environment except production (fictional demo accounts).
 * 3. E2ETestSeeder        - only when this connection and the `tests` group (database.tests.* in .env)
 *                           both point at the E2E database (akademus_test); it needs the .env.e2e credentials.
 *
 * E2EExamResetSeeder is not called: it is a reset tool Playwright runs around each suite, not seed data.
 * Every seeder is idempotent, so running this again is safe.
 */
class DatabaseSeeder extends Seeder
{
    private const E2E_DATABASE = 'akademus_test';

    public function run(): void
    {
        $this->call(ReferenceDataSeeder::class);

        if (ENVIRONMENT !== 'production') {
            $this->call(DemoUserSeeder::class);
        }

        // E2ETestSeeder always connects through the `tests` group, so both must point at the E2E database.
        $testsDatabase = config('Database')->tests['database'] ?? null;
        if ($this->db->getDatabase() === self::E2E_DATABASE && $testsDatabase === self::E2E_DATABASE) {
            $this->call(E2ETestSeeder::class);
        }
    }
}
