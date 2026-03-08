<?php

/**
 * Copyright (c) OLIUP <dev@oliup.com>.
 *
 * This file is part of the Oliup CodeGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace OLIUP\CG\Tests;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

/**
 * Base test class for all test cases.
 * Runs via: ./vendor/bin/phpunit --testdox
 * Update snapshots: UPDATE_SNAPSHOTS=1 ./vendor/bin/phpunit.
 */
abstract class TestCase extends PHPUnitTestCase
{
	/**
	 * Asserts two values are identical (===).
	 * Description-first wrapper around assertSame().
	 */
	protected function assertEq(string $description, mixed $expected, mixed $actual): void
	{
		self::assertSame($expected, $actual, $description);
	}

	/**
	 * Asserts $needle is found inside $haystack string.
	 */
	protected function assertHasStr(string $description, string $needle, string $haystack): void
	{
		self::assertStringContainsString($needle, $haystack, $description);
	}

	/**
	 * Asserts $needle is NOT found inside $haystack string.
	 */
	protected function assertNotHasStr(string $description, string $needle, string $haystack): void
	{
		self::assertStringNotContainsString($needle, $haystack, $description);
	}

	/**
	 * Asserts that $fn throws an exception of $exceptionClass.
	 */
	protected function assertThrows(string $description, string $exceptionClass, callable $fn): void
	{
		try {
			$fn();
			self::fail($description . ' (no exception thrown)');
		} catch (AssertionFailedError $e) {
			throw $e;
		} catch (Throwable $e) {
			if (!$e instanceof $exceptionClass) {
				self::fail($description . ' (wrong exception: ' . \get_class($e) . ')');
			}
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Compare $actual against a stored snapshot file.
	 * Set env var UPDATE_SNAPSHOTS=1 to regenerate stored snapshots.
	 */
	protected function assertSnapshot(string $snapshotFile, string $actual): void
	{
		$update = (bool) \getenv('UPDATE_SNAPSHOTS')
			|| \in_array('--update-snapshots', $_SERVER['argv'] ?? [], true);

		if (!\file_exists($snapshotFile) || $update) {
			$dir = \dirname($snapshotFile);
			if (!\is_dir($dir)) {
				\mkdir($dir, 0o755, true);
			}
			\file_put_contents($snapshotFile, $actual);

			return;
		}

		self::assertSame((string) \file_get_contents($snapshotFile), $actual, 'snapshot mismatch: ' . \basename($snapshotFile));
	}
}
