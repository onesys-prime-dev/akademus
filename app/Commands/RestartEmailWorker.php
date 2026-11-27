<?php

namespace App\Commands;

use App\Libraries\WorkerRestartSignal;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Asks every running "php spark emails:send" worker to exit after its current email. Supervisor then starts it
 * again on the live release. Run by every deploy and rollback (.github/scripts/deploy-remote.sh); no sudo needed.
 */
class RestartEmailWorker extends BaseCommand
{
    protected $group       = 'Notifications';
    protected $name        = 'emails:restart';
    protected $description = 'Signal the email queue workers to restart on the current release';

    /**
     * @param array<int|string, string|null> $params Not used: the command takes no arguments.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function run(array $params)
    {
        (new WorkerRestartSignal())->send();
        CLI::write('Email worker restart signal sent.');

        return EXIT_SUCCESS;
    }
}
