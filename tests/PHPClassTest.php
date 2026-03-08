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
use OLIUP\CG\PHPConstant;
use OLIUP\CG\PHPMethod;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPProperty;
use RuntimeException;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPClassTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testBasic(): void
	{
		$this->assertHasStr('class keyword', 'class Foo', $this->printer->printClass(new PHPClass('Foo')));
	}

	public function testFinal(): void
	{
		$c = (new PHPClass('Bar'))->final();
		$this->assertHasStr('final', 'final class Bar', $this->printer->printClass($c));
	}

	public function testAbstract(): void
	{
		$c = (new PHPClass('Base'))->abstract();
		$this->assertHasStr('abstract', 'abstract class Base', $this->printer->printClass($c));
	}

	public function testExtends(): void
	{
		$c = (new PHPClass('Child'))->extends('Parent');
		$this->assertHasStr('extends', 'extends', $this->printer->printClass($c));
	}

	public function testImplementsSingle(): void
	{
		$c = (new PHPClass('MyClass'))->implements('Stringable');
		$this->assertHasStr('implements', 'implements', $this->printer->printClass($c));
	}

	public function testImplementsMultipleNoSpaceBeforeComma(): void
	{
		$c   = (new PHPClass('Multi'))->implements('Countable')->implements('Stringable');
		$out = $this->printer->printClass($c);
		$this->assertHasStr('comma without leading space', 'Countable, Stringable', $out);
		$this->assertNotHasStr('no space before comma', ' , ', $out);
	}

	public function testAnonymousRendersAsNewClass(): void
	{
		$this->assertHasStr('new class', 'new class', $this->printer->printClass(new PHPClass()));
	}

	public function testFinalAbstractConflict(): void
	{
		$c = (new PHPClass('X'))->final()->abstract();
		$this->assertThrows('final+abstract throws', RuntimeException::class, static fn () => $c->validate());
	}

	public function testAddChildDispatchesConstant(): void
	{
		$c = new PHPClass('X');
		$c->addChild(new PHPConstant('FLAG', true));
		$this->assertEq('constant registered', true, $c->hasConstant('FLAG'));
	}

	public function testAddChildDispatchesProperty(): void
	{
		$c = new PHPClass('X');
		$p = new PHPProperty('foo');
		$c->addChild($p);
		$this->assertEq('property registered', true, $c->hasProperty('foo'));
	}

	public function testAddChildDispatchesMethod(): void
	{
		$c = new PHPClass('X');
		$c->addChild(new PHPMethod('bar'));
		$this->assertEq('method registered', true, $c->hasMethod('bar'));
	}

	public function testIsAnonymous(): void
	{
		$this->assertEq('empty name = anonymous', true, (new PHPClass())->isAnonymous());
		$this->assertEq('named = not anonymous', false, (new PHPClass('Foo'))->isAnonymous());
	}

	public function testGetExtends(): void
	{
		$c = (new PHPClass('Child'))->extends('App\Base');
		$this->assertEq('parent name', 'Base', $c->getExtends()?->getName());
	}
}
