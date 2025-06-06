<?php

declare(strict_types=1);

namespace LogadApp\Queue;

use Exception;
use LogadApp\Queue\Contracts\StoreInterface;
use LogadApp\Queue\Stores\FileStore;
use RuntimeException;

/**
 * @todo Remove "delete", should update the job status instead
 * @todo Retry shouldn't delete the job but reset it and push it again
 * @todo Update the job fail reason along with failure
 */
class Queue
{
	private static ?StoreInterface $store = null;

	public static function useStore(StoreInterface $store): void
	{
		static::$store = $store;
	}

	private static function getStore(): StoreInterface
	{
		if (!isset(static::$store)) {
			static::$store = new FileStore();
		}

		return static::$store;
	}

	public static function add(string $queue, Job $job): string
	{
		$id = static::getStore()->add($queue, serialize($job));
		$job->setId($id);
		return $id;
	}

	public static function next(string $queue): ?Job
	{
		$job = static::getStore()->next($queue);
		
		if (!$job) return null;

		try {
			$jobObject = unserialize($job['payload']);
			
			if (!$jobObject instanceof Job) {
				throw new RuntimeException('Invalid job payload');
			}
			
			$jobObject->setId($job['id']);

			return $jobObject;
		} catch (Exception $e) {
			error_log("Failed to unserialize job: " . $e->getMessage());

			static::getStore()->delete($queue, $job['id']);
		}

		return null;
	}

	public static function retry(string $queue, Job $job): void
	{
		$newId = static::getStore()->retry($queue, serialize($job));
		$job->setId($newId);
	}

	public static function delete(string $queue, Job $job): void
	{
		static::getStore()->delete($queue, $job->getId());
	}

	// list jos for debug
	public static function list(string $queue = 'default'): array
	{
		return static::getStore()->listJobs($queue);
	}
}
