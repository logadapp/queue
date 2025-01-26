<?php

declare(strict_types=1);

namespace LogadApp\Queue;

interface StoreInterface
{
	/**
	 * @param string $queue
	 * @param string $payload
	 * @return string The unique ID of the job
	 */
	public function add(string $queue, string $payload): string;

	public function next(string $queue): ?array;

	public function delete(string $queue, string $uid): void;
}