<?php

declare(strict_types=1);

namespace LogadApp\Queue;

use Exception;

abstract class Job
{
	public string $id;
	protected string $queue = 'default';

	protected int $tries = 0; // no retries unless set
	protected int $attempts = 0;
	protected int $retryAfter = 60;

	public static function dispatch(...$params): void
	{
		$job = new static(...$params);
		Queue::add($job->queue, $job);
	}

	final public function process(): void
	{
		try {
			$this->handle();
			Queue::delete($this->queue, $this);
		} catch (Exception $e) {
			$this->attempts++;
			
			if ($this->attempts < $this->tries) {
				$this->retry();
			} else {
				$this->failure($e);
				Queue::delete($this->queue, $this);
			}
		}
	}

	final public function getId(): string
	{
		return $this->id ?? '';
	}

	final public function getAttempts(): int
	{
		return $this->attempts;
	}

	final public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	private function retry(): void
	{
		Queue::retry($this->queue, $this);
	}

	final protected function failure(Exception $exception): void
	{
		error_log("Job failed: " . $exception->getMessage());
	}

	abstract protected function handle(): void;
}
