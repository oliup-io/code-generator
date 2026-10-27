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

use OLIUP\CG\Enums\PropertyHookKindEnum;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPProperty;
use OLIUP\CG\PHPPropertyHook;
use PHPUnit\Framework\Attributes\CoversNothing;
use Probe;
use RuntimeException;

/**
 * @internal
 */
#[CoversNothing]
final class PHPPropertyHookTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testGetHookAsExpression(): void
	{
		$p = new PHPProperty('id')->public()
			->setType('?int');
		$p->newGetHook()
			->setExpression('$this->read(self::COL_ID)');

		$this->assertEq(
			'expression body printed with a fat arrow',
			'public ?int $id {' . "\n\t" . 'get => $this->read(self::COL_ID);' . "\n" . '}',
			$this->printer->printProperty($p)
		);
	}

	public function testGetHookAsBlock(): void
	{
		$p = new PHPProperty('id')->public()
			->setType('?int');
		$p->newGetHook()
			->setContent('return $this->read(self::COL_ID);');

		$out = $this->printer->printProperty($p);
		$this->assertHasStr('block body opens a brace', 'get {', $out);
		$this->assertHasStr('block body carries the statement', 'return $this->read(self::COL_ID);', $out);
	}

	public function testSetHookDeclaresItsParameterType(): void
	{
		$p = new PHPProperty('data')->public()
			->setType('\PHPUtils\Store\Map');
		$p->newSetHook()
			->setExpression('$this->write(self::COL_DATA, $value)')
			->newArgument('value')
			->setType('array|\PHPUtils\Store\Map');

		$this->assertHasStr(
			'the set parameter is printed with its type',
			'set(array|\PHPUtils\Store\Map $value) => $this->write(self::COL_DATA, $value);',
			$this->printer->printProperty($p)
		);
	}

	public function testSetHookWithoutParameterTakesValueImplicitly(): void
	{
		$p = new PHPProperty('name')->public()
			->setType('string');
		$p->newSetHook()
			->setContent('$this->write(self::COL_NAME, $value);');

		$out = $this->printer->printProperty($p);
		$this->assertHasStr('no parentheses when no parameter is named', 'set {', $out);
		$this->assertNotHasStr('no empty parentheses', 'set()', $out);
	}

	public function testFinalHook(): void
	{
		$p = new PHPProperty('id')->public()
			->setType('int');
		$p->newGetHook()
			->setExpression('1')
			->final();

		$this->assertHasStr('final keyword', 'final get =>', $this->printer->printProperty($p));
	}

	public function testBothHooksOnOneProperty(): void
	{
		$p = new PHPProperty('name')->public()
			->setType('string');
		$p->newGetHook()
			->setExpression('$this->n');
		$p->newSetHook()
			->setExpression('$this->n = $value');

		$out = $this->printer->printProperty($p);
		$this->assertHasStr('get present', 'get =>', $out);
		$this->assertHasStr('set present', 'set =>', $out);
	}

	public function testSettingOneKindTwiceReplacesIt(): void
	{
		$p = new PHPProperty('name')->public()
			->setType('string');
		$p->newGetHook()
			->setExpression('"first"');
		$p->newGetHook()
			->setExpression('"second"');

		$out = $this->printer->printProperty($p);
		$this->assertEq('one hook kept', 1, \count($p->getHooks()));
		$this->assertHasStr('the last one wins', '"second"', $out);
		$this->assertNotHasStr('the first one is gone', '"first"', $out);
	}

	public function testPropertyWithoutHooksIsUnchanged(): void
	{
		$p = new PHPProperty('name')->public()
			->setType('string');

		$this->assertEq(
			'a plain property still ends with a semicolon',
			'public string $name;',
			$this->printer->printProperty($p)
		);
		$this->assertEq('no hooks', false, $p->hasHooks());
	}

	public function testGetHookRefusesAParameter(): void
	{
		$p = new PHPProperty('id')->public()
			->setType('int');
		$hook = $p->newGetHook();
		$hook->setExpression('1')
			->newArgument('value')
			->setType('int');

		$this->assertThrows(
			'a get hook takes no parameter',
			RuntimeException::class,
			fn () => $this->printer->printProperty($p)
		);
	}

	public function testHookRefusesTwoBodies(): void
	{
		$hook = new PHPPropertyHook(PropertyHookKindEnum::GET);
		$hook->setExpression('1')
			->setContent('return 1;');

		$this->assertThrows(
			'an expression and a block are two bodies',
			RuntimeException::class,
			fn () => $this->printer->printPropertyHook($hook)
		);
	}

	public function testHookRefusesNoBody(): void
	{
		$this->assertThrows(
			'a hook with no body is not printable',
			RuntimeException::class,
			fn () => $this->printer->printPropertyHook(new PHPPropertyHook(PropertyHookKindEnum::SET))
		);
	}

	public function testStaticPropertyRefusesHooks(): void
	{
		$p = new PHPProperty('id')->public()
			->setType('int')
			->static();
		$p->newGetHook()
			->setExpression('1');

		$this->assertThrows(
			'PHP has no static hooked property',
			RuntimeException::class,
			fn () => $this->printer->printProperty($p)
		);
	}

	public function testGeneratedHooksAreValidPhp(): void
	{
		$p = new PHPProperty('data')->public()
			->setType('array');
		$p->newGetHook()
			->setExpression('$this->row[\'data\'] ?? []');
		$p->newSetHook()
			->setExpression('$this->row[\'data\'] = $value')
			->newArgument('value')
			->setType('array');

		$source = 'class Probe { private array $row = []; '
			. $this->printer->printProperty($p)
			. ' }';

		$this->assertEq('the printed property compiles', true, false !== @eval($source));

		$probe       = new Probe();
		$probe->data = ['a' => 1];
		$this->assertEq('the set hook ran', ['a' => 1], $probe->data);
	}
}
