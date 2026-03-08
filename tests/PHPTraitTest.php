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

use OLIUP\CG\PHPMethod;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPTrait;
use OLIUP\CG\PHPUseTrait;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPTraitTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testBasic(): void
	{
		$t   = new PHPTrait('Loggable');
		$out = $this->printer->printTrait($t);
		$this->assertHasStr('trait keyword', 'trait Loggable', $out);
		$this->assertHasStr('opening brace', '{', $out);
	}

	public function testWithMethod(): void
	{
		$t = new PHPTrait('Timestampable');
		$t->newMethod('touch')->public()->setReturnType('void')->addChild('$this->updatedAt = time();');
		$out = $this->printer->printTrait($t);
		$this->assertHasStr('method present', 'function touch(', $out);
	}

	public function testAddChildDispatchesMethod(): void
	{
		$t = new PHPTrait('Foo');
		$t->addChild(new PHPMethod('bar'));
		$this->assertEq('method registered', true, $t->hasMethod('bar'));
	}

	public function testWithUseTrait(): void
	{
		$inner = new PHPTrait('Inner');
		$t     = new PHPTrait('Outer');
		$t->addChild(new PHPUseTrait($inner));
		$out = $this->printer->printTrait($t);
		$this->assertHasStr('use statement', 'use Inner', $out);
	}
}
