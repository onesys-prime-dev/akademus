<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\App;

/**
 * Basic bootstrap sanity checks.
 *
 * @internal
 */
final class HealthTest extends CIUnitTestCase
{
    public function testIsDefinedAppPath(): void
    {
        $this->assertTrue(defined('APPPATH'));
    }

    public function testRunsInTestingEnvironment(): void
    {
        $this->assertSame('testing', ENVIRONMENT);
    }

    public function testBaseUrlIsAValidUrl(): void
    {
        // app.baseURL is environment-specific (.env / server config); app/Config/App.php leaves it empty on purpose.
        $baseURL = config(App::class)->baseURL;

        $this->assertTrue(service('validation')->check($baseURL, 'valid_url'), "app.baseURL \"{$baseURL}\" is not a valid URL");
    }

    public function testTestsDatabaseGroupIsMysql(): void
    {
        $tests = config('Database')->tests;

        $this->assertSame('MySQLi', $tests['DBDriver']);
        $this->assertSame('', $tests['DBPrefix'], 'Views and raw SQL use unprefixed table names.');
    }
}
