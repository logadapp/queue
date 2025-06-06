<?php

declare(strict_types=1);

namespace LogadApp\Queue;

use Throwable;

class Worker
{
    private int $sleep = 1;
    private int $memoryLimit;

    public function __construct(int $memoryLimitMB = 128)
    {
        $this->memoryLimit = $memoryLimitMB * 1024 * 1024;
    }
    
    /**
     * Start the worker
     */
    public function work(string $queue = 'default', callable $logger = null): void
    {
        $logger = $logger ?: function($message) {
            echo $message . PHP_EOL;
        };
        
        $logger("Starting worker for queue: {$queue}");
        
        while (true) {
            if ($this->isMemoryExceeded()) {
				$logger("Memory limit exceeded. Stopping..");
                break;
            }
            
            $this->processNextJob($queue, $logger);

            sleep($this->sleep);
        }
    }

    protected function processNextJob(string $queue, callable $logger): void
    {
        $job = Queue::next($queue);
        
        if (!$job) return;

		$attempts = $job->getAttempts() + 1;
        $logger("Processing job ID: {$job->getId()}, Attempt: {$attempts}");
        
        try {
            $job->process();
            $logger("Job completed successfully");
        } catch (Throwable $e) {
            $logger("Job failed with error: {$e->getMessage()}");
        }
    }

	private function isMemoryExceeded(): bool
    {
        return memory_get_usage(true) >= $this->memoryLimit;
    }
}
