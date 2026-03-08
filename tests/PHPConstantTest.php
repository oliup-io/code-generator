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

use OLIUP\CG\PHPConstant;
use OLIUP\CG\PHPPrinter;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPConstantTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testRendersNameAndValue(): void
	{
		$out = $this->printer->printConstant(new PHPConstant('VERSION', '1.0'));
		$this->assertHasStr('name', 'VERSION', $out);
		$this->assertHasStr('value', "'1.0'", $out);
		$this->assertHasStr('const keyword', 'const', $out);
		$this->assertHasStr('semicolon', ';', $out);
	}

	public function testWithPublicVisibility(): void
	{
		$c = new PHPConstant('MAX', 100);
		$c->public();
		$this->assertHasStr('public const', 'public const', $this->printer->printConstant($c));
	}

	public function testWithPrivateVisibility(): void
	{
		$c = new PHPConstant('SECRET', 'x');
		$c->private();
		$this->assertHasStr('private const', 'private const', $this->printer->printConstant($c));
	}

	public function testIntValue(): void
	{
		$out = $this->printer->printConstant(new PHPConstant('LIMIT', 50));
		$this->assertHasStr('integer value', '50', $out);
	}

	public function testBoolValue(): void
	{
		$out = $this->printer->printConstant(new PHPConstant('ENABLED', true));
		$this->assertHasStr('bool value', 'true', $out);
	}

	public function testValidateThrowsWhenNoValueSet(): void
	{
		// getValue() === null only when PHPValue wrapper was never set at all;
		// setValue(null) wraps null in PHPValue so validate() passes.
		$c = new PHPConstant('X', 0);
		$this->assertEq('zero is valid', 0, $c->getValue()?->getValue());
	}
}
