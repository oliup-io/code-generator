<?php

/**
 * Copyright (c) 2021-present, Emile Silas Sare
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace OLIUP\CG\Tests;

use InvalidArgumentException;
use OLIUP\CG\PHPClass;
use OLIUP\CG\PHPRaw;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * A node that is its own descendant.
 *
 * Nothing refuses one when the graph is built -- every `validateChild()` answers the child as given
 * -- so the first thing to find out is whatever walks it: the printer, or `clone`.
 *
 * @internal
 */
#[CoversNothing]
final class CyclicGraphTest extends TestCase
{
	public function testANodeThatIsItsOwnChildIsReportedWhenPrinted(): void
	{
		$class = new PHPClass('A');
		$class->addChild($class);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('is already being printed');

		(string) $class;
	}

	public function testATwoNodeCycleIsReportedWhenPrinted(): void
	{
		$a = new PHPClass('A');
		$b = new PHPClass('B');

		$a->addChild($b);
		$b->addChild($a);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('is already being printed');

		(string) $a;
	}

	public function testCloningACyclicGraphIsReportedInsteadOfExhaustingTheStack(): void
	{
		$a = new PHPClass('A');
		$b = new PHPClass('B');

		$a->addChild($b);
		$b->addChild($a);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('the graph holds itself');

		$copy = clone $a;
	}

	public function testTheSameChildAddedTwiceIsHeldOnce(): void
	{
		$class = new PHPClass('A');
		$raw   = new PHPRaw('// once');

		$class->addChild($raw);
		$class->addChild($raw);

		self::assertCount(1, $class->getChildren());
	}

	public function testTwoChildrenThatRenderTheSameAreBothHeld(): void
	{
		// They used to be deduplicated, because comparing them meant casting each to a string, i.e.
		// rendering its whole subtree. Distinct nodes are distinct children.
		$class = new PHPClass('A');

		$class->addChild(new PHPRaw('// twice'));
		$class->addChild(new PHPRaw('// twice'));

		self::assertCount(2, $class->getChildren());
		self::assertStringContainsString("// twice\n\t// twice", (string) $class);
	}

	public function testAnOrdinaryGraphStillPrints(): void
	{
		$class = new PHPClass('Greeter');
		$class->newMethod('hello')
			->setContent(new PHPRaw('return "hi";'));

		$out = (string) $class;

		self::assertStringContainsString('class Greeter', $out);
		self::assertStringContainsString('function hello', $out);
		self::assertStringContainsString('return "hi";', $out);
	}
}
