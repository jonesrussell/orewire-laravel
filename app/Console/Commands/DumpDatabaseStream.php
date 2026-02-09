<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

class DumpDatabaseStream extends Command
{
    protected $signature = 'db:dump-stream';

    protected $description = 'Stream database dump to stdout (for use over SSH with ddev pull-db)';

    public function handle(): int
    {
        $connection = Config::get('database.default');
        $config = Config::get("database.connections.{$connection}");

        if ($config === null || ! in_array($config['driver'] ?? '', ['mysql', 'mariadb'], true)) {
            $this->error("Database connection '{$connection}' is not MySQL or MariaDB.");

            return Command::FAILURE;
        }

        $binary = $config['driver'] === 'mariadb' ? 'mariadb-dump' : 'mysqldump';

        $args = [
            $binary,
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--skip-lock-tables',
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            $config['database'] ?? 'laravel',
        ];

        if (! empty($config['unix_socket'])) {
            $args[] = '--socket='.$config['unix_socket'];
        }

        $env = array_merge($_ENV, ['MYSQL_PWD' => $config['password'] ?? '']);

        $process = new Process(
            $args,
            base_path(),
            $env,
            null,
            null
        );
        $process->setTimeout(null);

        $process->run(function (string $type, string $buffer): void {
            echo $buffer;
        });

        if (! $process->isSuccessful()) {
            $this->error('Dump failed: '.$process->getErrorOutput());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
