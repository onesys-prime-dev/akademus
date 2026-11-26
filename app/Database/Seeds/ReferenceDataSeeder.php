<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Everything the portal needs to work on an empty database, in dependency order.
 * Safe for every environment (production included): no people, no credentials, idempotent,
 * never overwrites existing rows.
 *
 *   php spark db:seed ReferenceDataSeeder
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ThemeSettingsSeeder::class);
        $this->call(FormPageSeeder::class);
        $this->call(RequirementSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(PaymentFeeTypeSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(PaymentMethodAccountSeeder::class);
    }
}
