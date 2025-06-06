<?php

declare(strict_types=1);

namespace LogadApp\Queue\Examples\Jobs;

use Exception;
use LogadApp\Queue\Job;

final class NewUserJob extends Job
{
	public function __construct(
		private readonly string $email,
		private readonly string $firstName,
	) {}

	/**
	 * @throws Exception
	 */
	protected function handle(): void
	{
		echo "New user job: {$this->firstName} ({$this->email})\n";

		if (rand(1, 5) === 1) {
			throw new Exception("Failure simulated");
		}

		echo "success\n";
	}
}