<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\DatabaseTestCase;

/**
 * The adminAuth route filter protects every admin/* route, including controllers that skip
 * BaseAdminController's constructor check.
 *
 * @internal
 */
final class AdminAuthFilterTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    public function testAnonymousAdminActionIsRedirectedBeforeItRuns(): void
    {
        $presets = $this->db->table('form_presets')->countAllResults();

        $result = $this->get('admin/presets/1/delete');

        $result->assertRedirectTo(base_url('admin/login'));
        $this->assertSame($presets, $this->db->table('form_presets')->countAllResults(), 'The delete action must not run anonymously.');
    }

    public function testAnonymousAjaxRequestIsRedirectedToo(): void
    {
        $result = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])->get('admin/presets/1/json');

        $result->assertRedirectTo(base_url('admin/login'));
    }

    public function testLoginAndLogoutStayReachable(): void
    {
        $this->get('admin/login')->assertOK();
        $this->get('admin/logout')->assertRedirect();
    }

    public function testAdminSessionIsLetThrough(): void
    {
        $result = $this->withSession(['admin_logged_in' => true, 'admin_id' => 1, 'admin_name' => 'Test Admin'])
            ->get('admin/presets');

        $result->assertOK();
    }

    public function testOfficerSessionIsNotAnAdminSession(): void
    {
        $this->withSession(['ao_logged_in' => true])->get('admin/presets')->assertRedirectTo(base_url('admin/login'));
    }
}
