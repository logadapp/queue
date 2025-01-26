<?php

declare(strict_types=1);

namespace tests\Helpers;

use LogadApp\Queue\Job;

class SampleJob extends Job
{
	public function __construct(
		private readonly string $name
	) {}

	public function handle(): void
	{
		echo "Sample job executed", PHP_EOL;
		echo "Name: {$this->name}", PHP_EOL;
	}
}