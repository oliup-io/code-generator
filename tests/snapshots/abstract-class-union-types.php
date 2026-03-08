<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Base handler.
 */
abstract class AbstractHandler
{
	abstract public function handle(mixed $payload): null|string|int;

	protected function log(string $message): void
	{
		echo $message;
	}

	public static function create(): static
	{
		return new static();
	}
}
