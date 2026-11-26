<?php

namespace Tests\Feature;

use App\Controllers\ThemeController;
use CodeIgniter\Test\ControllerTestTrait;
use Tests\Support\DatabaseTestCase;

/**
 * Logo uploads that PHP itself rejects (larger than upload_max_filesize) must be reported as
 * "too large" (422) - not as a generic success-shaped response.
 *
 * @internal
 */
final class ThemeLogoUploadTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        session()->set('admin_logged_in', true);
    }

    protected function tearDown(): void
    {
        service('superglobals')->setFilesArray([]);
        session()->remove('admin_logged_in');
        parent::tearDown();
    }

    private function upload(int $error): array
    {
        // CodeIgniter 4.7 reads uploads from the superglobals service snapshot.
        service('superglobals')->setFilesArray(['logo_file' => ['name' => 'logo.png', 'type' => 'image/png', 'tmp_name' => '', 'error' => $error, 'size' => 0]]);

        $result = $this->controller(ThemeController::class)->execute('uploadLogo');

        return [$result->response()->getStatusCode(), json_decode((string) $result->response()->getBody(), true)];
    }

    public function testFileOverPhpUploadLimitIsReportedAsTooLarge(): void
    {
        [$status, $json] = $this->upload(UPLOAD_ERR_INI_SIZE);

        $this->assertSame(422, $status, json_encode($json));
        $this->assertSame('error', $json['status']);
        $this->assertStringContainsString('under 2 MB', $json['message']);
    }

    public function testOtherUploadErrorsAreBadRequests(): void
    {
        [$status, $json] = $this->upload(UPLOAD_ERR_PARTIAL);

        $this->assertSame(400, $status);
        $this->assertSame('error', $json['status']);
        $this->assertNotEmpty($json['message']);
    }
}
