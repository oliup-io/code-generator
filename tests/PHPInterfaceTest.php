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

use OLIUP\CG\PHPInterface;
use OLIUP\CG\PHPMethod;
use OLIUP\CG\PHPPrinter;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPInterfaceTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testBasic(): void
	{
		$i   = new PHPInterface('Runnable');
		$out = $this->printer->printInterface($i);
		$this->assertHasStr('interface keyword', 'interface Runnable', $out);
		$this->assertHasStr('opening brace', '{', $out);
	}

	public function testMethodRenderedAsDeclaration(): void
	{
		$i = new PHPInterface('Shape');
		$m = new PHPMethod('area');
		$m->public()->setReturnType('float');
		$i->addChild($m);
		$out = $this->printer->printInterface($i);
		$this->assertHasStr('method signature', 'public function area(): float;', $out);
		$this->assertNotHasStr('no body braces', '{}', $out);
	}

	public function testExtends(): void
	{
		$i = new PHPInterface('Extended');
		$i->extends('Base');
		$out = $this->printer->printInterface($i);
		$this->assertHasStr('extends', 'extends', $out);
	}

	public function testNoSpaceBeforeCommaInExtends(): void
	{
		$i = new PHPInterface('Multi');
		$i->extends('A');
		$i->extends('B');
		$out = $this->printer->printInterface($i);
		$this->assertNotHasStr('no space before comma', ' , ', $out);
		$this->assertHasStr('comma', 'A, B', $out);
	}

	public function testAddChildMethod(): void
	{
		$i = new PHPInterface('Foo');
		$i->addChild(new PHPMethod('doIt'));
		$this->assertEq('method registered', true, $i->hasMethod('doIt'));
	}
}
