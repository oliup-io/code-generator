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

use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPTrait;
use OLIUP\CG\PHPUseTrait;
use OLIUP\CG\PHPUseTraitMethodRule;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPUseTraitTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testWithoutRules(): void
	{
		$trait = new PHPTrait('TimestampableTrait');
		$u     = new PHPUseTrait($trait);
		$out   = $this->printer->printUseTrait($u);
		$this->assertHasStr('use keyword', 'use TimestampableTrait', $out);
		$this->assertHasStr('semicolon', ';', $out);
		$this->assertNotHasStr('no brace', '{', $out);
	}

	public function testSpaceBeforeBraceInRulesBlock(): void
	{
		$trait = new PHPTrait('A');
		$u     = new PHPUseTrait($trait);
		$rule  = new PHPUseTraitMethodRule('doIt');
		$rule->hideFrom('B');
		$u->addRule($rule);
		$out = $this->printer->printUseTrait($u);
		$this->assertHasStr('space before brace', 'A {', $out);
		$this->assertNotHasStr('no TraitName{', 'A{', $out);
	}

	public function testRulesBlock(): void
	{
		$trait = new PHPTrait('Trait1');
		$u     = new PHPUseTrait($trait);
		$rule  = new PHPUseTraitMethodRule('speak');
		$rule->hideFrom('Trait2');
		$u->addRule($rule);
		$out = $this->printer->printUseTrait($u);
		$this->assertHasStr('insteadof', 'insteadof', $out);
		$this->assertHasStr('closing brace', '}', $out);
	}

	public function testAliasRule(): void
	{
		$trait = new PHPTrait('TraitA');
		$u     = new PHPUseTrait($trait);
		$rule  = new PHPUseTraitMethodRule('hello');
		$rule->setName('hi');
		$u->addRule($rule);
		$out = $this->printer->printUseTrait($u);
		$this->assertHasStr('as keyword', 'as', $out);
	}
}
