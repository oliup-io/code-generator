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

// Convenience wrapper - delegates to PHPUnit.
// Update snapshots: php tests/run-tests.php --update-snapshots

$update = \in_array('--update-snapshots', $argv ?? [], true);
$env    = $update ? 'UPDATE_SNAPSHOTS=1 ' : '';

\passthru($env . \dirname(__DIR__) . '/vendor/bin/phpunit --testdox --do-not-cache-result', $exitCode);

exit($exitCode);

// Require all *Test.php files in the tests/ directory
foreach (\glob(__DIR__ . '/*Test.php') as $file) {
	require_once $file;
}

use OLIUP\CG\Tests\TestCase;

$totalPassed = 0;
$totalFailed = 0;

foreach (\get_declared_classes() as $class) {
	if (\is_subclass_of($class, TestCase::class)) {
		echo \PHP_EOL . '--- ' . $class . ' ---' . \PHP_EOL;
		$instance = new $class();
		$instance->runTests();
		$totalPassed += $instance->getPassed();
		$totalFailed += $instance->getFailed();
	}
}

$total = $totalPassed + $totalFailed;

echo \PHP_EOL . "Total: {$totalPassed}/{$total} passed";

if ($totalFailed > 0) {
	echo ", {$totalFailed} FAILED";
}

echo \PHP_EOL;

exit($totalFailed > 0 ? 1 : 0);
