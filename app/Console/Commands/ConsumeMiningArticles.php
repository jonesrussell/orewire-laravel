<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MiningIngestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Subscribe to North Cloud Redis pub/sub for mining articles.
 *
 * This command connects to the North Cloud Redis instance and listens
 * for mining articles published to the articles:mining channel.
 * Articles are processed using the existing MiningIngestService.
 *
 * Run as a daemon via systemd for production:
 * systemctl --user start orewire-mining-consumer
 *
 * Note: Redis pub/sub uses blocking I/O. Graceful shutdown relies on
 * the read_timeout configured in the Redis connection. When the timeout
 * expires, the command checks for shutdown signals and reconnects if needed.
 */
class ConsumeMiningArticles extends Command
{
    /**
     * @var string
     */
    protected $signature = 'mining:consume
        {--verbose : Show detailed output for each article}
        {--channel=articles:mining : Redis channel to subscribe to}
        {--connection=northcloud : Redis connection name from config/database.php}';

    /**
     * @var string
     */
    protected $description = 'Subscribe to North Cloud Redis pub/sub for mining articles';

    private int $processedCount = 0;

    private int $updatedCount = 0;

    private int $errorCount = 0;

    private bool $shouldStop = false;

    public function __construct(
        private readonly MiningIngestService $ingestService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->registerSignalHandlers();

        $channel = $this->option('channel');
        $connection = $this->option('connection');

        $this->info('Mining Article Consumer');
        $this->info('=======================');
        $this->info("Channel: {$channel}");
        $this->info("Connection: {$connection}");
        $this->info('Press Ctrl+C to stop gracefully');
        $this->newLine();

        Log::info('Mining consumer started', [
            'channel' => $channel,
            'connection' => $connection,
        ]);

        while (! $this->shouldStop) {
            try {
                $this->subscribeAndListen($channel, $connection);
            } catch (\RedisException $e) {
                // Read timeout is expected - allows checking shouldStop flag
                if (str_contains($e->getMessage(), 'read error') || str_contains($e->getMessage(), 'timed out')) {
                    if ($this->shouldStop) {
                        break;
                    }
                    // Normal timeout, reconnect immediately
                    continue;
                }

                $this->error("Redis connection error: {$e->getMessage()}");
                Log::error('Mining consumer Redis connection error', [
                    'error' => $e->getMessage(),
                    'channel' => $channel,
                ]);

                // Close connection before retry
                try {
                    Redis::connection($connection)->disconnect();
                } catch (\Throwable) {
                    // Ignore disconnect errors
                }

                if (! $this->shouldStop) {
                    $this->info('Reconnecting in 5 seconds...');
                    sleep(5);
                }
            } catch (\Throwable $e) {
                $this->error("Unexpected error: {$e->getMessage()}");
                Log::error('Mining consumer unexpected error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                if (! $this->shouldStop) {
                    sleep(5);
                }
            }
        }

        $this->displaySummary();

        return Command::SUCCESS;
    }

    private function subscribeAndListen(string $channel, string $connection): void
    {
        $this->info("Subscribing to {$channel}...");

        Redis::connection($connection)->subscribe([$channel], function (string $message) {
            if ($this->shouldStop) {
                return;
            }

            $this->processMessage($message);
        });
    }

    private function processMessage(string $message): void
    {
        $start = microtime(true);

        try {
            $article = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

            $articleId = $article['id'] ?? 'unknown';
            $title = $article['title'] ?? 'Untitled';
            $sourceUrl = $article['source'] ?? $article['canonical_url'] ?? null;

            $result = $this->ingestService->handle($article);

            $durationMs = (int) ((microtime(true) - $start) * 1000);
            $status = $result['status'];

            if ($status === 'created') {
                $this->processedCount++;
                if ($this->option('verbose')) {
                    $this->info("[+] Created: {$title}");
                }
            } else {
                $this->updatedCount++;
                if ($this->option('verbose')) {
                    $this->line("[~] Updated: {$title}");
                }
            }

            Log::info('Mining article consumed', [
                'article_id' => $articleId,
                'source' => $sourceUrl,
                'status' => $status,
                'duration_ms' => $durationMs,
            ]);
        } catch (\JsonException $e) {
            $this->errorCount++;
            $this->error('[!] Invalid JSON: '.$e->getMessage());
            Log::error('Mining consumer JSON parse error', [
                'error' => $e->getMessage(),
                'message_preview' => substr($message, 0, 200),
            ]);
        } catch (\Throwable $e) {
            $this->errorCount++;
            $this->error('[!] Processing error: '.$e->getMessage());
            Log::error('Mining consumer processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function registerSignalHandlers(): void
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);

            pcntl_signal(SIGTERM, fn () => $this->shutdown());
            pcntl_signal(SIGINT, fn () => $this->shutdown());
        }
    }

    private function shutdown(): void
    {
        $this->shouldStop = true;
        $this->newLine();
        $this->info('Shutting down gracefully...');

        Log::info('Mining consumer shutdown requested');
    }

    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Created: {$this->processedCount}");
        $this->info("Updated: {$this->updatedCount}");
        $this->info("Errors:  {$this->errorCount}");

        Log::info('Mining consumer stopped', [
            'created' => $this->processedCount,
            'updated' => $this->updatedCount,
            'errors' => $this->errorCount,
        ]);
    }
}
