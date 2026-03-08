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

use OLIUP\CG\PHPClass;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPType;
use RuntimeException;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPTypeTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testSingleType(): void
	{
		$type = new PHPType('string');
		$this->assertEq('plain name', 'string', $this->printer->printType($type));
	}

	public function testNullableSingleType(): void
	{
		$type = (new PHPType('string'))->nullable();
		$this->assertEq('leading ?', '?string', $this->printer->printType($type));
	}

	public function testUnionType(): void
	{
		$type = new PHPType('string', 'int');
		$this->assertEq('pipe-separated', 'string|int', $this->printer->printType($type));
	}

	public function testNullableUnionType(): void
	{
		$type = (new PHPType('string', 'int'))->nullable();
		$this->assertEq('null prefix on union', 'null|string|int', $this->printer->printType($type));
	}

	public function testQuestionMarkMapsToNull(): void
	{
		$type = new PHPType('?');
		$this->assertEq('? treated as null', true, $type->isNullable());
	}

	public function testNullableToggleOff(): void
	{
		$type = (new PHPType('int'))->nullable()->nullable(false);
		$this->assertEq('null removed', 'int', $this->printer->printType($type));
	}

	public function testIntersectionType(): void
	{
		$itype = PHPType::intersection(new PHPClass('Countable'), new PHPClass('Traversable'));
		$type  = new PHPType($itype);
		$this->assertHasStr('contains &', '&', $this->printer->printType($type));
	}

	public function testIsAllowed(): void
	{
		$type = new PHPType('string', 'int');
		$this->assertEq('string allowed', true, $type->isAllowed('string'));
		$this->assertEq('bool not allowed', false, $type->isAllowed('bool'));
	}

	public function testAnonymousClassNotAllowedAsType(): void
	{
		$this->assertThrows(
			'anonymous class rejected',
			RuntimeException::class,
			static fn () => new PHPType(new PHPClass())
		);
	}

	public function testDuplicateTypeDeduped(): void
	{
		$type = new PHPType('string', 'string');
		$this->assertEq('deduped to one', 1, \count($type->getTypes()));
	}
}
