<?php

namespace Tests\Support;

use App\Database\Seeds\ReferenceDataSeeder;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Base class for tests that need the real schema: the `tests` database group (MySQL) is rolled back,
 * migrated with the App migrations and seeded with reference data once per test class.
 *
 * Locally: create an empty database and point database.tests.* in .env at it (see tests/README.md).
 * CI: the database_tests_* environment variables point at the MySQL service container.
 */
abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = 'App';
    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $seed        = ReferenceDataSeeder::class;
    protected $seedOnce    = true;
}
