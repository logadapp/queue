<?php

declare(strict_types=1);

namespace LogadApp\Queue;

class Job
{
	public string $id;

	/**
	 * The name of the queue the job should be sent to.
	 *
	 * @var string
	 */
	protected string $queue = 'default';

	// protected int $tries = 3;
	// private int $retryAfter = 100;


	public static function dispatch(...$params): void
	{
		$job = new static(...$params);
		Queue::add($job->queue, $job);
	}

	public function process(): void
	{
		try {
			$this->handle();
		} catch (\Exception $e) {
			/*if ($this->tries > 0) {
				$this->retry($this->retryAfter);
			} else {
				$this->fail();
			}*/
			$this->fail();
		}
	}

	/**
	 * Retry the job after a delay.
	 *
	 * @return void
	 */
	protected function retry(): void
	{
		Queue::retry($this->queue, $this);
	}

	protected function fail(): void
	{
		//
	}
}