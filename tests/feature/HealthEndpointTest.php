<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class HealthEndpointTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    public function testReportsHealthyWithoutAuthentication(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $this->assertStringContainsString('no-store', $result->response()->getHeaderLine('Cache-Control'));

        $body = json_decode($result->getJSON(), true);
        $this->assertSame('ok', $body['status']);
        $this->assertSame(['database' => 'ok', 'writable' => 'ok', 'migrations' => 'ok'], $body['checks']);
        $this->assertMatchesRegularExpression('/^(dev|[0-9a-f]{7,40})$/', $body['release']);
    }

    public function testExposesNoInternalDetails(): void
    {
        $body = json_decode($this->get('health')->getJSON(), true);

        $this->assertSame(['status', 'release', 'checks'], array_keys($body));
    }

    public function testReportsFailureWhenAMigrationIsPending(): void
    {
        $where = ['class' => 'App\Database\Migrations\CreateUsersTable'];
        $row   = $this->db->table('migrations')->where($where)->get()->getRowArray();
        $this->db->table('migrations')->where($where)->delete();

        try {
            $result = $this->get('health');
            $result->assertStatus(503);
            $this->assertSame('fail', json_decode($result->getJSON(), true)['checks']['migrations']);
        } finally {
            $this->db->table('migrations')->insert($row);
        }
    }
}
