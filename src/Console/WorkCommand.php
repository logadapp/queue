<?php

declare(strict_types=1);

namespace LogadApp\Queue\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'queue:work',
    description: 'Process jobs from the queue.',
    hidden: false
)]
final class Work extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln("Starting queue worker...");

		while (true) {
			// Check memory usage
			if ($this->memoryExceeded()) {
				$output->writeln('Memory limit exceeded. Restarting worker...');
				break;
			}

			// Process jobs
			$this->processNextJob($output);

			// Sleep for a while before checking for new jobs
			sleep(1);
		}

        return Command::SUCCESS;
    }

	protected function processNextJob(OutputInterface $output)
	{
		$job = Capsule::table('jobs')
			->where('queue', 'default')
			->where('available_at', '<=', time())
			->whereNull('reserved_at')
			->orderBy('id', 'asc')
			->first();

		if ($job) {
			// Mark the job as reserved
			Capsule::table('jobs')
				->where('id', $job->id)
				->update(['reserved_at' => time()]);

			// Unserialize and process the job
			try {
				$jobInstance = unserialize($job->payload);
				$jobInstance->id = $job->id;
				$jobInstance->attempts = $job->attempts;
				$jobInstance->process();
			} catch (Exception $e) {
				$output->writeln("Job failed: " . $e->getMessage());
			}
		}
	}
}
