<?php

namespace App\Libraries;

/**
 * Tells long-running workers (php spark emails:send) to exit so their process manager (Supervisor)
 * starts them again on the release that "current" points to now. Works like Laravel's queue:restart:
 * the deploy writes a new value, a worker compares it with the value it saw at start-up.
 *
 * The file lives in writable/, which is shared by all releases, so the old and the new release see it.
 */
class WorkerRestartSignal
{
    private readonly string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?? WRITEPATH . 'worker-restart';
    }

    /**
     * Current signal value ('' if no restart was ever requested).
     */
    public function read(): string
    {
        clearstatcache(true, $this->file);

        if (! is_file($this->file) || ! is_readable($this->file)) {
            return '';
        }

        $value = file_get_contents($this->file);

        return $value === false ? '' : trim($value);
    }

    /**
     * Requests a restart of every worker and returns the new signal value.
     */
    public function send(): string
    {
        $value = sprintf('%.6F-%s', microtime(true), bin2hex(random_bytes(4)));
        $tmp   = $this->file . '.tmp';

        file_put_contents($tmp, $value . "\n");
        rename($tmp, $this->file); // atomic: a worker never reads a half-written value

        return $value;
    }

    /**
     * True when a restart was requested after the worker read $seenAtStart.
     */
    public function changedSince(string $seenAtStart): bool
    {
        return $this->read() !== $seenAtStart;
    }
}
