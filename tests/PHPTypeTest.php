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

	// --- generic annotation stripping ---

	public function testUnionOfGenericsStripped(): void
	{
		$type = new PHPType('array<string,mixed>|Map<string,mixed>');
		$this->assertEq('union of generics stripped', 'array|Map', $this->printer->printType($type));
	}

	public function testUnionOfGenericsKeptWhenOptedIn(): void
	{
		$type = new PHPType('array<string,mixed>|Map<string,mixed>');
		$this->assertEq('union of generics retained', 'array<string,mixed>|Map<string,mixed>', $this->printer->printType($type, ['with_generics' => true]));
	}

	public function testGenericsStrippedByDefault(): void
	{
		$type = new PHPType('array<string,mixed>');
		$this->assertEq('bare type without generics', 'array', $this->printer->printType($type));
	}

	public function testGenericsKeptWhenOptedIn(): void
	{
		$type = new PHPType('array<string,mixed>');
		$this->assertEq('generic retained', 'array<string,mixed>', $this->printer->printType($type, ['with_generics' => true]));
	}

	public function testNestedGenericsStripped(): void
	{
		$type = new PHPType('array<string,array<int,mixed>>');
		$this->assertEq('nested generics stripped', 'array', $this->printer->printType($type));
	}

	public function testNonGenericTypeUnchanged(): void
	{
		$type = new PHPType('string');
		$this->assertEq('plain type unchanged', 'string', $this->printer->printType($type));
	}

	public function testNullableGenericStripped(): void
	{
		$type = (new PHPType('array<string,int>'))->nullable();
		$this->assertEq('nullable generic stripped', '?array', $this->printer->printType($type));
	}

	public function testNullableGenericKeptWhenOptedIn(): void
	{
		$type = (new PHPType('array<string,int>'))->nullable();
		$this->assertEq('nullable generic retained', '?array<string,int>', $this->printer->printType($type, ['with_generics' => true]));
	}

	public function testInlineUnionWithGenericsStripped(): void
	{
		// string form: 'array<string,mixed>|Map<string,mixed>' stored as one entry in PHPType
		$type = new PHPType('array<string,mixed>|Map<string,mixed>');
		$this->assertEq('union generics stripped', 'array|Map', $this->printer->printType($type));
	}

	public function testInlineUnionWithGenericsKeptWhenOptedIn(): void
	{
		$type = new PHPType('array<string,mixed>|Map<string,mixed>');
		$this->assertEq('union generics retained', 'array<string,mixed>|Map<string,mixed>', $this->printer->printType($type, ['with_generics' => true]));
	}

	public function testNestedGenericInUnionStripped(): void
	{
		$type = new PHPType('array<string,array<int,mixed>>|Collection<string>');
		$this->assertEq('nested union generics stripped', 'array|Collection', $this->printer->printType($type));
	}
}
