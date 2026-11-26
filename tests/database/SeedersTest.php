<?php

namespace Tests\Database;

use App\Database\Seeds\DemoUserSeeder;
use App\Database\Seeds\ReferenceDataSeeder;
use Tests\Support\DatabaseTestCase;

/**
 * Reference and demo seeders produce the data the portal needs and are safe to run repeatedly.
 *
 * @internal
 */
final class SeedersTest extends DatabaseTestCase
{
    /**
     * table => expected rows after ReferenceDataSeeder on an empty database
     */
    private const REFERENCE_ROWS = [
        'theme_settings'                 => 26,
        'form_pages'                     => 4,
        'form_field_configs'             => 36,
        'requirement_categories'         => 4,
        'requirements'                   => 10,
        'programs'                       => 3,
        'form_presets'                   => 2,
        'preset_sections'                => 8,
        'preset_requirement_categories'  => 8,
        'program_requirement_categories' => 8,
        'program_requirements'           => 20,
        'payment_fee_type'               => 3,
        'payment_method'                 => 4,
        'payment_method_account'         => 4,
    ];

    public function testReferenceDataIsComplete(): void
    {
        foreach (self::REFERENCE_ROWS as $table => $count) {
            $this->assertSame($count, $this->db->table($table)->countAllResults(), "Unexpected row count in {$table}.");
        }
    }

    public function testReferenceDataSeederIsIdempotent(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        foreach (self::REFERENCE_ROWS as $table => $count) {
            $this->assertSame($count, $this->db->table($table)->countAllResults(), "Re-seeding duplicated rows in {$table}.");
        }
    }

    public function testSystemFormPagesKeepTheirFixedIds(): void
    {
        // ApplicantFormController maps page ids 1-4 to the application, personal, family and education records.
        $titles = array_column($this->db->table('form_pages')->orderBy('id')->get()->getResultArray(), 'title', 'id');

        $this->assertSame([1 => 'Application Type', 2 => 'Personal Information', 3 => 'Family Background', 4 => 'Educational Background'], $titles);
    }

    public function testFeeCodesUsedByThePaymentControllersExist(): void
    {
        foreach (['ADMISSION_FEE', 'RESERVATION_FEE', 'FAST_TRACK_FEE'] as $code) {
            $this->seeInDatabase('payment_fee_type', ['code' => $code, 'is_active' => 1]);
        }
    }

    public function testDemoUsersUseOnlyTheReservedDomain(): void
    {
        $this->seed(DemoUserSeeder::class);
        $this->seed(DemoUserSeeder::class);

        $emails = array_column($this->db->table('users')->like('email', 'demo.', 'after')->get()->getResultArray(), 'email');

        $this->assertCount(5, $emails, 'Demo users must not be duplicated on re-seed.');

        foreach ($emails as $email) {
            $this->assertStringEndsWith('@' . DemoUserSeeder::EMAIL_DOMAIN, $email);
        }
        $this->seeInDatabase('admin_credentials', ['user_id' => $this->db->table('users')->where('email', 'demo.admin@akademus.test')->get()->getRow()->user_id]);
    }
}
