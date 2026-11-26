<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;

/**
 * The requirement library: document categories and the documents in each.
 * Matched by name, so re-running never duplicates or overwrites admin edits.
 */
class RequirementSeeder extends Seeder
{
    use InsertsMissingRows;

    /** category => [description, display_order, [[requirement, description, is_fast_track_required], ...]] */
    public const CATEGORIES = [
        'Personal Documents' => ['Basic personal identification documents', 10, [
            ['Birth Certificate', 'PSA-issued birth certificate', 1],
            ['Certificate of Good Moral Character', 'Issued by previous school', 1],
        ]],
        'Academic Documents' => ['Educational background and records', 20, [
            ['Transcript of Records', 'Official academic transcript', 0],
            ['Diploma', 'High school or previous degree diploma', 0],
            ['Form 137', 'Secondary school permanent record', 0],
        ]],
        'Identification' => ['Government-issued IDs for verification', 30, [
            ['Valid Government ID', 'Any government-issued ID (e.g., Passport, UMID)', 1],
            ['2x2 ID Picture', 'Recent 2x2 photo with white background', 1],
        ]],
        'Medical Requirements' => ['Health-related documents and clearances', 40, [
            ['Medical Certificate', 'General health clearance from a licensed physician', 0],
            ['Chest X-ray Result', 'Recent X-ray result for medical screening', 0],
            ['Drug Test Result', 'Laboratory-issued drug test result', 0],
        ]],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $category => [$description, $order, $requirements]) {
            $categoryId = $this->insertIfMissing('requirement_categories', ['category_name' => $category], [
                'description' => $description, 'display_order' => $order, 'is_active' => 1,
            ], 'category_id');

            foreach ($requirements as $i => [$name, $reqDescription, $fastTrack]) {
                $this->insertIfMissing('requirements', ['category_id' => $categoryId, 'requirement_name' => $name], [
                    'description' => $reqDescription, 'is_required' => 1, 'is_fast_track_required' => $fastTrack,
                    'display_order' => $i + 1, 'is_active' => 1,
                ], 'requirement_id');
            }
        }
    }
}
