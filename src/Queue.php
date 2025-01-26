<?php

declare(strict_types=1);

namespace LogadApp\Queue;

use LogadApp\Queue\Stores\FileStore;

class Queue
{
	private static StoreInterface $store;

	/**
	 * Set your own store implementation.
	 *
	 * @param StoreInterface $store
	 * @return void
	 */
	public static function useStore(StoreInterface $store): void
	{
		static::$store = $store;
	}

	/**
	 * Use user-defined store or the default FileStore.
	 * @return StoreInterface
	 */
	private static function getStore(): StoreInterface
	{
		if (!static::$store) {
			static::$store = new FileStore('./tmp/jobs');
		}

		return static::$store;
	}

	public static function add(string $queue, Job $job): void
	{
		static::getStore()->add($queue, serialize($job));
	}

	public static function next(string $queue): ?Job
	{
		$job = static::getStore()->next($queue);
		if (!$job) return null;

		$job = unserialize($job['payload']);
		return $job['job'];
	}

	public static function retry(string $queue, Job $job): void
	{
		// For simplicity, just add the job back to the queue
		static::getStore()->retry($queue, $job);
	}

	public static function delete(string $queue, Job $job): void
	{
		static::getStore()->delete($queue, $job->getId());
	}
}