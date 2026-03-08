<?php
declare(strict_types=1);

namespace App\Config;

/**
 * Holds application config.
 * 
 * @package App\Config
 */
final class Config
{
	/**
	 * Raw config array.
	 */
	private array $data = array (
	);
	/**
	 * Load config from file.
	 * 
	 * @param string $path path to PHP config file
	 */
	public function load(string $path): void
	{
		// validate before readingif (!is_file($path)) { throw new \InvalidArgumentException("Not a file: $path"); }$this->data = require $path;
	}

	public function get(string $key, mixed $default = NULL): mixed
	{
		return $this->data[$key] ?? $default;
	}
}
