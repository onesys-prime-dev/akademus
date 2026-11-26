<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;

/**
 * The four system pages of the applicant form and their system fields.
 *
 * Page ids 1-4 are fixed: ApplicantFormController maps page_id 1-4 to the application, personal,
 * family and education records, and form_field_configs.step equals the page id.
 */
class FormPageSeeder extends Seeder
{
    use InsertsMissingRows;

    private const PAGES = [
        1 => ['Application Type', 'Select applicant type, program, and school year.', 20],
        2 => ['Personal Information', 'Full name, date of birth, contact details, and address.', 30],
        3 => ['Family Background', 'Father, mother, guardian info, and family income.', 40],
        4 => ['Educational Background', 'Most recent senior high school information.', 50],
    ];

    /**
     * page_id => [field_key, section, label, field_type, placeholder, is_required, sort_order]
     */
    private const FIELDS = [
        1 => [
            ['applicant_type', 'step1-core', 'Applicant Type', 'radio', '', 1, 10],
            ['program_id', 'step1-core', 'Confirmed Program', 'select', 'Select a Program', 1, 20],
            ['school_year', 'step1-core', 'School Year', 'select', 'Select School Year', 1, 30],
            ['semester', 'step1-core', 'Semester', 'select', 'Select Semester', 1, 40],
        ],
        2 => [
            ['gender', 'step2-details', 'Gender', 'select', 'Select Gender', 1, 50],
            ['date_of_birth', 'step2-details', 'Date of Birth', 'date', '', 1, 60],
            ['place_of_birth', 'step2-details', 'Place of Birth', 'text', 'City, Province', 1, 70],
            ['citizenship', 'step2-details', 'Citizenship', 'text', 'e.g., Filipino', 1, 80],
            ['home_address', 'step2-details', 'Home Address', 'text', 'Street, Barangay, City, Province', 1, 90],
            ['contact_number', 'step2-details', 'Contact Number', 'tel', '+63 9XX XXX XXXX', 1, 100],
            ['email_address', 'step2-details', 'Email Address', 'email', 'e.g., sample@email.com', 1, 110],
            ['firstname', 'step2-name', 'First Name', 'text', 'e.g., Juan', 1, 10],
            ['middlename', 'step2-name', 'Middle Name', 'text', 'e.g., Sanchez', 0, 20],
            ['lastname', 'step2-name', 'Last Name', 'text', 'e.g., Dela Cruz', 1, 30],
            ['suffix', 'step2-name', 'Suffix', 'select', '', 0, 40],
        ],
        3 => [
            ['father_firstname', 'step3-father', "Father's First Name", 'text', 'e.g., Juan', 0, 10],
            ['father_middlename', 'step3-father', "Father's Middle Name", 'text', 'e.g., Sanchez', 0, 20],
            ['father_lastname', 'step3-father', "Father's Last Name", 'text', 'e.g., Dela Cruz', 0, 30],
            ['father_occupation', 'step3-father', "Father's Occupation", 'text', 'e.g., Accountant', 0, 40],
            ['father_contact_number', 'step3-father', "Father's Contact Number", 'tel', '+63 9XX XXX XXXX', 0, 50],
            ['guardian_name', 'step3-guardian', 'Guardian Name', 'text', 'If Different from Parents', 0, 10],
            ['guardian_relationship', 'step3-guardian', 'Relationship', 'select', 'Select relationship', 0, 20],
            ['guardian_contact_number', 'step3-guardian', 'Guardian Contact Number', 'tel', '+63 9XX XXX XXXX', 0, 30],
            ['annual_family_income', 'step3-guardian', 'Annual Family Income', 'select', 'Select income range', 0, 40],
            ['mother_firstname', 'step3-mother', "Mother's First Name", 'text', 'e.g., Mary', 0, 10],
            ['mother_middlename', 'step3-mother', "Mother's Middle Name", 'text', 'e.g., Alcantara', 0, 20],
            ['mother_lastname', 'step3-mother', "Mother's Last Name", 'text', 'e.g., Delos Santos', 0, 30],
            ['mother_occupation', 'step3-mother', "Mother's Occupation", 'text', 'e.g., Accountant', 0, 40],
            ['mother_contact_number', 'step3-mother', "Mother's Contact Number", 'tel', '+63 9XX XXX XXXX', 0, 50],
        ],
        4 => [
            ['school_name', 'step4-main', 'School Name', 'text', 'Full Name of your Senior High School', 1, 10],
            ['school_location', 'step4-main', 'Location', 'text', 'City, Province', 1, 20],
            ['strand', 'step4-main', 'Track / Strand', 'select', 'Select track / strand', 1, 30],
            ['school_year_start', 'step4-main', 'School Year Start', 'number', 'e.g. 2024', 1, 40],
            ['school_year_end', 'step4-main', 'School Year End', 'number', 'e.g. 2025', 1, 50],
            ['gwa', 'step4-main', 'General Average (GWA)', 'text', 'e.g., 92.3', 1, 60],
            ['school_type', 'step4-main', 'School Type', 'select', 'Select school type', 1, 70],
        ],
    ];

    public function run(): void
    {
        foreach (self::PAGES as $id => [$title, $description, $sort]) {
            $this->insertIfMissing('form_pages', ['id' => $id], [
                'title' => $title, 'description' => $description, 'is_enabled' => 1, 'is_system' => 1, 'sort_order' => $sort,
            ]);
        }

        foreach (self::FIELDS as $pageId => $fields) {
            foreach ($fields as [$key, $section, $label, $type, $placeholder, $required, $sort]) {
                $this->insertIfMissing('form_field_configs', ['page_id' => $pageId, 'field_key' => $key], [
                    'step'        => $pageId, 'section' => $section, 'label' => $label, 'field_type' => $type,
                    'placeholder' => $placeholder, 'is_visible' => 1, 'is_required' => $required,
                    'is_system'   => 1, 'sort_order' => $sort,
                ]);
            }
        }
    }
}
