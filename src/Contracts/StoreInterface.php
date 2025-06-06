<?php

declare(strict_types=1);

namespace LogadApp\Queue\Contracts;

interface StoreInterface
{
	/**
	 * @param string $queue queue name
	 * @param string $payload The serialized job w/o payload
	 * @return string The unique ID of the job
	 */
	public function add(string $queue, string $payload): string;

	/**
	 * @param string $queue queue name
	 * @return array|null
	 */
	public function next(string $queue): ?array;

	public function delete(string $queue, string $uid): void;

	public function retry(string $queue, string $payload): string;
}
