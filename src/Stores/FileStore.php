<?php

declare(strict_types=1);

namespace LogadApp\Queue\Stores;

use LogadApp\Queue\Job;
use LogadApp\Queue\StoreInterface;

final class FileStore implements StoreInterface
{
	public function __construct(
		private readonly string $storagePath,
		bool $autoCreate = true
	) {
		// Ensure the storage directory exists
		if (!is_dir($this->storagePath) && $autoCreate) {
			mkdir($this->storagePath, 0755, true);
		} else {
			throw new \Exception('Storage path does not exist');
		}
	}

	public function add(string $queue, string $payload): string
	{
		$id = uniqid('job_', true);

		file_put_contents(
			$this->getFilePath($queue),
			json_encode(['id' => $id, 'payload' => $payload]) . PHP_EOL,
			FILE_APPEND
		);

		return $id;
	}

	public function next(string $queue): ?array
	{
		$file = $this->getFilePath($queue);

		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (empty($lines)) return null;

		$line = array_shift($lines);
        file_put_contents($file, implode(PHP_EOL, $lines));

		return json_decode($line, true);
	}

	public function retry(string $queue, Job $job): void
	{
		$this->delete($queue, $job->id);

		$this->add($queue, $job);
	}

	public function delete(string $queue, string $uid): void
	{
		// No-op for file storage
	}

	private function getFilePath(string $queue): string
	{
		$file = $this->storagePath . '/' . $queue . '.queue';

		if (!file_exists($file)) {
			file_put_contents($file, '');
		}

		return $file;
	}
}