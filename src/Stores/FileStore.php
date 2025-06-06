<?php

declare(strict_types=1);

namespace LogadApp\Queue\Stores;

use Exception;
use LogadApp\Queue\Contracts\StoreInterface;

final class FileStore implements StoreInterface
{
	/**
	 * @throws Exception
	 */
	public function __construct(
		private ?string $storagePath = null
	) {
		if ($this->storagePath && !is_dir($this->storagePath)) {
			throw new Exception('Storage path does not exist');
		} elseif (!$this->storagePath) {
			$this->storagePath = sys_get_temp_dir();
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
		
		if (!file_exists($file) || filesize($file) === 0) return null;

		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (empty($lines)) return null;

		$line = array_shift($lines);
		file_put_contents($file, implode(PHP_EOL, $lines));

		return json_decode($line, true);
	}

	public function retry(string $queue, string $payload): string
	{
		return $this->add($queue, $payload);
	}

	public function delete(string $queue, string $uid): void
	{
		//
	}

	private function getFilePath(string $queue): string
	{
		$file = $this->storagePath . '/LogadApp.' . $queue . '.queue';

		if (!file_exists($file)) file_put_contents($file, '');

		return $file;
	}

	// list jobs for debug
	public function listJobs(string $queue): array
	{
		$file = $this->getFilePath($queue);

		if (!file_exists($file) || filesize($file) === 0) {
			return [];
		}

		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return array_map(fn($line) => json_decode($line, true), $lines);
	}
}
