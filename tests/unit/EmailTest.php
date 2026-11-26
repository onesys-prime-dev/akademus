<?php

use App\Services\EmailService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Email and notification validation, without sending anything (MockEmail).
 *
 * @internal
 */
final class EmailTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $config            = config('Email');
        $config->fromEmail = 'noreply@akademus.test';
        $config->fromName  = 'Akademus Test';

        $email = new MockEmail($config);
        Services::injectMock('email', $email);
    }

    /**
     * Every email type the app queues (NotificationService::trigger('<type>', ...) or a direct
     * email_queue insert) must be handled by the queue worker (php spark emails:send).
     */
    public function testEveryQueuedEmailTypeHasAHandler(): void
    {
        $worker = (string) file_get_contents(APPPATH . 'Commands/SendEmails.php');
        preg_match_all("/case\\s+'([a-z_]+\\.[a-z_]+)'/", $worker, $m);
        $handled = $m[1];

        $queued = [];
        $files  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APPPATH, FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), 'Database' . DIRECTORY_SEPARATOR . 'Migrations')) {
                continue;
            }
            $code = (string) file_get_contents($file->getPathname());
            preg_match_all("/(?:trigger\\(\\s*|'type'\\s*=>\\s*)'([a-z_]+\\.[a-z_]+)'/", $code, $mm);

            foreach ($mm[1] as $type) {
                $queued[$type] = basename($file->getPathname());
            }
        }

        $this->assertNotEmpty($queued);

        foreach ($queued as $type => $where) {
            $this->assertContains($type, $handled, "Email type '{$type}' (queued in {$where}) has no case in SendEmails.");
        }
    }

    #[DataProvider('provideEveryEmailRenders')]
    public function testEveryEmailRenders(string $method): void
    {
        $args = [];

        foreach ((new ReflectionMethod(EmailService::class, $method))->getParameters() as $param) {
            $type   = (string) $param->getType();
            $args[] = match (true) {
                $param->getName() === 'to' => 'applicant@example.test',
                $type === 'int'            => 1,
                $type === 'array'          => ['Sample message <b>with markup</b>'],
                default                    => 'sample-' . $param->getName(),
            };
        }

        $this->assertTrue(EmailService::$method(...$args), "{$method}() did not send.");

        $sent = service('email');
        $this->assertInstanceOf(MockEmail::class, $sent);
        $this->assertNotSame('', trim((string) $sent->archive['body']), "{$method}() produced an empty body.");
        $this->assertNotSame('', trim((string) $sent->archive['subject']), "{$method}() produced an empty subject.");
        $this->assertSame('noreply@akademus.test', $sent->archive['fromEmail'], "{$method}() must use email.fromEmail from the config.");
        $this->assertStringNotContainsString('<b>with markup</b>', (string) $sent->archive['body'], "{$method}() does not escape user content.");
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEveryEmailRenders(): iterable
    {
        foreach ((new ReflectionClass(EmailService::class))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
            if ($method->isPublic() && $method->isStatic() && str_starts_with($method->getName(), 'send')) {
                yield $method->getName() => [$method->getName()];
            }
        }
    }
}
