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

use OLIUP\CG\PHPArgument;
use OLIUP\CG\PHPPrinter;
use RuntimeException;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPArgumentTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testBasicTypedArg(): void
	{
		$arg = new PHPArgument('name', 'string');
		$this->assertEq('typed arg', 'string $name', $this->printer->printArgument($arg));
	}

	public function testUntypedArg(): void
	{
		$arg = new PHPArgument('value');
		$this->assertEq('untyped arg', '$value', $this->printer->printArgument($arg));
	}

	public function testVariadic(): void
	{
		$arg = new PHPArgument('items');
		$arg->setVariadic(true);
		$this->assertHasStr('variadic spreads', '...', $this->printer->printArgument($arg));
	}

	public function testByReference(): void
	{
		$arg = new PHPArgument('ref');
		$arg->reference(true);
		$this->assertHasStr('& prefix', '&', $this->printer->printArgument($arg));
	}

	public function testDefaultValue(): void
	{
		$arg = new PHPArgument('limit', 'int');
		$arg->setValue(10);
		$this->assertHasStr('default', '= 10', $this->printer->printArgument($arg));
	}

	public function testConstructorPromotion(): void
	{
		// public() calls validateVisibility() which auto-sets promoted = true
		$arg = new PHPArgument('id', 'int');
		$arg->public();
		$this->assertEq('promoted flag', true, $arg->isPromoted());
		$out = $this->printer->printArgument($arg, allow_promoted: true);
		$this->assertHasStr('public keyword', 'public', $out);
	}

	public function testVariadicAndPromotedConflict(): void
	{
		$arg = new PHPArgument('x');
		$arg->setVariadic(true);
		$this->assertThrows('variadic + promoted throws', RuntimeException::class, static fn () => $arg->setPromoted(true));
	}

	public function testPromotedAndVariadicConflict(): void
	{
		$arg = new PHPArgument('x');
		$arg->setPromoted(true);
		$this->assertThrows('promoted + variadic throws', RuntimeException::class, static fn () => $arg->setVariadic(true));
	}
}
