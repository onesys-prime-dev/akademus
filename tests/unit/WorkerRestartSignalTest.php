<?php

use App\Commands\RestartEmailWorker;
use App\Libraries\WorkerRestartSignal;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * The deploy restarts the email worker without sudo: "spark emails:restart" writes a new signal value and
 * "spark emails:send" exits when the value differs from the one it saw at start-up.
 *
 * @internal
 */
final class WorkerRestartSignalTest extends CIUnitTestCase
{
    private string $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = sys_get_temp_dir() . '/akademus-worker-restart-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
        parent::tearDown();
    }

    public function testNoSignalReadsAsEmpty(): void
    {
        $this->assertSame('', (new WorkerRestartSignal($this->file))->read());
    }

    public function testWorkerKeepsRunningWithoutANewSignal(): void
    {
        $signal = new WorkerRestartSignal($this->file);
        $seen   = $signal->read();

        $this->assertFalse($signal->changedSince($seen));
    }

    public function testSendRequestsARestartOfRunningWorkers(): void
    {
        $worker = new WorkerRestartSignal($this->file);
        $seen   = $worker->read();

        $sent = (new WorkerRestartSignal($this->file))->send();

        $this->assertTrue($worker->changedSince($seen));
        $this->assertSame($sent, $worker->read());
        $this->assertFileDoesNotExist($this->file . '.tmp');
    }

    public function testEverySendIsANewSignal(): void
    {
        $signal = new WorkerRestartSignal($this->file);
        $first  = $signal->send();

        $this->assertFalse($signal->changedSince($first));
        $this->assertNotSame($first, $signal->send());
        $this->assertTrue($signal->changedSince($first));
    }

    public function testDefaultsToTheSharedWritableDirectory(): void
    {
        $this->assertSame(WRITEPATH . 'worker-restart', $this->fileOf(new WorkerRestartSignal()));
    }

    public function testWorkerAndRestartCommandsUseTheSignal(): void
    {
        $worker = (string) file_get_contents(APPPATH . 'Commands/SendEmails.php');
        $this->assertStringContainsString('changedSince(', $worker, 'emails:send must stop when a restart is signalled');

        $this->assertSame('emails:restart', $this->commandName(RestartEmailWorker::class));
    }

    private function fileOf(WorkerRestartSignal $signal): string
    {
        return (string) (new ReflectionProperty($signal, 'file'))->getValue($signal);
    }

    /**
     * @param class-string $class
     */
    private function commandName(string $class): string
    {
        return (string) (new ReflectionClass($class))->getDefaultProperties()['name'];
    }
}
