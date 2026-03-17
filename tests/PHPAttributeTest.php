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
use OLIUP\CG\PHPAttribute;
use OLIUP\CG\PHPClass;
use OLIUP\CG\PHPConstant;
use OLIUP\CG\PHPMethod;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPProperty;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPAttributeTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	// --- PHPAttribute unit tests ---

	public function testNameNoArgs(): void
	{
		$a = new PHPAttribute('Override');
		$this->assertEq('getName', 'Override', $a->getName());
		$this->assertEq('empty args', [], $a->getArguments());
	}

	public function testConstructorWithArgs(): void
	{
		$a = new PHPAttribute('Route', "'/api'", "methods: ['GET']");
		$this->assertEq('two args', ["'/api'", "methods: ['GET']"], $a->getArguments());
	}

	public function testAddArgument(): void
	{
		$a = (new PHPAttribute('Attr'))->addArgument('foo')->addArgument('bar');
		$this->assertEq('addArgument accumulates', ['foo', 'bar'], $a->getArguments());
	}

	public function testSetName(): void
	{
		$a = (new PHPAttribute('Old'))->setName('New');
		$this->assertEq('setName', 'New', $a->getName());
	}

	public function testToStringNoArgs(): void
	{
		$out = (string) new PHPAttribute('Override');
		$this->assertEq('no-arg output', '#[Override]', $out);
	}

	public function testToStringWithArgs(): void
	{
		$out = (string) new PHPAttribute('Route', "'/users'", "methods: ['GET']");
		$this->assertEq('with-args output', "#[Route('/users', methods: ['GET'])]", $out);
	}

	public function testToStringAfterAddArgument(): void
	{
		$out = (string) (new PHPAttribute('Attr'))->addArgument("'x'")->addArgument("'y'");
		$this->assertEq('addArgument renders', "#[Attr('x', 'y')]", $out);
	}

	// --- AttributeAwareTrait on PHPClass ---

	public function testAddAttributeWithString(): void
	{
		$c   = (new PHPClass('Foo'))->addAttribute('Override');
		$out = $this->printer->printClass($c);
		$this->assertHasStr('attribute rendered', '#[Override]', $out);
	}

	public function testAddAttributeWithObject(): void
	{
		$c = new PHPClass('Foo');
		$c->addAttribute(new PHPAttribute('Route', "'/foo'"));
		$out = $this->printer->printClass($c);
		$this->assertHasStr('attribute with arg', "#[Route('/foo')]", $out);
	}

	public function testNewAttributeRegistersAndReturns(): void
	{
		$c    = new PHPClass('Foo');
		$attr = $c->newAttribute('Inject', 'true');
		$this->assertEq('returns PHPAttribute', PHPAttribute::class, $attr::class);
		$this->assertEq('registered', 1, \count($c->getAttributes()));
	}

	public function testMultipleAttributesOnClass(): void
	{
		$c = (new PHPClass('Foo'))
			->addAttribute('ORM\Entity')
			->addAttribute(new PHPAttribute('ORM\Table', "name: 'foo'"));
		$out = $this->printer->printClass($c);
		$this->assertHasStr('first attr', '#[ORM\Entity]', $out);
		$this->assertHasStr('second attr', "#[ORM\\Table(name: 'foo')]", $out);
	}

	public function testAttributeAppearsBeforeClassKeyword(): void
	{
		$c   = (new PHPClass('Bar'))->addAttribute('Final');
		$out = $this->printer->printClass($c);
		$this->assertEq(
			'#[Final] before class',
			true,
			\strpos($out, '#[Final]') < \strpos($out, 'class Bar')
		);
	}

	public function testAttributeOnAnonymousClassInline(): void
	{
		$c   = (new PHPClass())->addAttribute('Override');
		$out = $this->printer->printClass($c);
		$this->assertHasStr('inline on new class', 'new #[Override] class', $out);
	}

	public function testGetAttributesEmpty(): void
	{
		$this->assertEq('empty by default', [], (new PHPClass('X'))->getAttributes());
	}

	// --- on PHPMethod ---

	public function testAttributeOnMethod(): void
	{
		$m   = (new PHPMethod('handle'))->public()->addAttribute('Override');
		$out = $this->printer->printMethod($m);
		$this->assertHasStr('attr on method', '#[Override]', $out);
		$this->assertEq(
			'attr before function',
			true,
			\strpos($out, '#[Override]') < \strpos($out, 'function handle')
		);
	}

	// --- on PHPProperty ---

	public function testAttributeOnProperty(): void
	{
		$p   = (new PHPProperty('id'))->public()->setType('int')->addAttribute('ORM\Id');
		$out = $this->printer->printProperty($p);
		$this->assertHasStr('attr on property', '#[ORM\Id]', $out);
		$this->assertEq(
			'attr before visibility',
			true,
			\strpos($out, '#[ORM\Id]') < \strpos($out, 'public')
		);
	}

	// --- on PHPConstant ---

	public function testAttributeOnConstant(): void
	{
		$c   = (new PHPConstant('LIMIT', 50))->addAttribute('Deprecated');
		$out = $this->printer->printConstant($c);
		$this->assertHasStr('attr on constant', '#[Deprecated]', $out);
		$this->assertEq(
			'attr before const keyword',
			true,
			\strpos($out, '#[Deprecated]') < \strpos($out, 'const LIMIT')
		);
	}

	// --- on PHPArgument ---

	public function testAttributeOnArgument(): void
	{
		$m   = (new PHPMethod('show'))->public();
		$arg = $m->newArgument('id')->setType('int')->addAttribute('MapEntity');
		$out = $this->printer->printArgument($arg);
		$this->assertHasStr('attr on arg', '#[MapEntity]', $out);
		$this->assertHasStr('full inline', '#[MapEntity] int $id', $out);
	}

	public function testMultipleAttributesOnArgument(): void
	{
		$arg = (new PHPArgument('val'))->addAttribute('NotNull')->addAttribute('Positive');
		$out = $this->printer->printArgument($arg);
		$this->assertHasStr('two attrs', '#[NotNull] #[Positive]', $out);
	}

	public function testArgumentAttributesOmittedInVirtualMethod(): void
	{
		$m = (new PHPMethod('handle'))->public()->setReturnType('void');
		$m->newArgument('id')->setType('int')->addAttribute('MapEntity');
		$out = $this->printer->printMethod($m, ['virtual' => true]);
		$this->assertHasStr('@method tag', '@method void handle(int $id)', $out);
		$this->assertNotHasStr('no attribute in docblock', '#[', $out);
	}
}
