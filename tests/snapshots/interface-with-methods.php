<?php
declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface Repository.
 */
interface Repository extends \App\Contracts\Countable
{
	const DEFAULT_LIMIT = 20;
	public function findById(int $id): mixed;

	public function findAll(int $limit = 20): array;
}
