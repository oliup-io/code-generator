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
use RuntimeException;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPMethodTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testBasicPublicMethod(): void
	{
		$m = new PHPMethod('greet');
		$m->public()->setReturnType('string')->addChild('return "hello";');
		$out = $this->printer->printMethod($m);
		$this->assertHasStr('public', 'public', $out);
		$this->assertHasStr('function name', 'function greet(', $out);
		$this->assertHasStr('return type', ': string', $out);
		$this->assertHasStr('body', 'return "hello";', $out);
	}

	public function testDeclarationOnlyHasNoBody(): void
	{
		$m = new PHPMethod('run');
		$m->public()->setReturnType('void');
		$out = $this->printer->printMethod($m, ['declaration' => true]);
		$this->assertHasStr('ends with semicolon', 'run(): void;', $out);
		$this->assertNotHasStr('no body braces', '{', $out);
	}

	public function testAbstractMethodHasNoBody(): void
	{
		$m = new PHPMethod('handle');
		$m->public()->abstract();
		$out = $this->printer->printMethod($m);
		$this->assertHasStr('abstract keyword', 'abstract', $out);
		$this->assertHasStr('ends with ;', ';', $out);
		$this->assertNotHasStr('no braces', '{', $out);
	}

	public function testStaticMethod(): void
	{
		$m = new PHPMethod('create');
		$m->public()->static()->setReturnType('static');
		$this->assertHasStr('static keyword', 'static', $this->printer->printMethod($m));
	}

	public function testFinalAbstractConflict(): void
	{
		$m = new PHPMethod('x');
		$m->abstract()->final();
		$this->assertThrows('abstract+final throws', RuntimeException::class, static fn () => $m->validate());
	}

	public function testVirtualDocTag(): void
	{
		$m = new PHPMethod('getId');
		$m->public()->setReturnType('int');
		$out = $this->printer->printMethod($m, ['virtual' => true]);
		$this->assertHasStr('@method tag', '@method', $out);
		$this->assertNotHasStr('no body', '{', $out);
	}

	public function testMethodWithArguments(): void
	{
		$m = new PHPMethod('setName');
		$m->public()->setReturnType('void');
		$m->newArgument('name')->setType('string');
		$out = $this->printer->printMethod($m);
		$this->assertHasStr('arg type', 'string $name', $out);
	}

	public function testConstructorPromotionInMethod(): void
	{
		$m = new PHPMethod('__construct');
		$m->public();
		$m->newArgument('id')->setType('int')->public();
		$out = $this->printer->printMethod($m);
		$this->assertHasStr('promoted arg', 'public int $id', $out);
	}
}
