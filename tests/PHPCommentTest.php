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

use OLIUP\CG\Enums\CommentKindEnum;
use OLIUP\CG\PHPComment;
use OLIUP\CG\PHPPrinter;

/**
 * @internal
 *
 * @coversNothing
 */
final class PHPCommentTest extends TestCase
{
	private PHPPrinter $printer;

	protected function setUp(): void
	{
		$this->printer = new PHPPrinter();
	}

	public function testDocBlock(): void
	{
		$c   = PHPComment::doc('@param int $x');
		$out = $this->printer->printComment($c);
		$this->assertHasStr('opens /**', '/**', $out);
		$this->assertHasStr('closes */', ' */', $out);
	}

	public function testInlineSlash(): void
	{
		$out = $this->printer->printComment(PHPComment::inline('note'));
		$this->assertHasStr('// prefix', '//', $out);
		$this->assertHasStr('content', 'note', $out);
	}

	public function testHash(): void
	{
		$out = $this->printer->printComment(PHPComment::hash('note'));
		$this->assertHasStr('# prefix', '#', $out);
	}

	public function testMultiline(): void
	{
		$out = $this->printer->printComment(PHPComment::multiline('block'));
		$this->assertHasStr('opens /*', '/*', $out);
		$this->assertHasStr('closes */', '*/', $out);
		$this->assertNotHasStr('not a doc block', '/**', $out);
	}

	public function testAddLines(): void
	{
		$c = PHPComment::inline('line1');
		$c->addLines('line2');
		$this->assertHasStr('line2 appended', 'line2', $c->getContent());
	}

	public function testSetContent(): void
	{
		$c = PHPComment::inline('old');
		$c->setContent('new');
		$this->assertEq('content replaced', 'new', $c->getContent());
	}

	public function testSetKind(): void
	{
		$c = new PHPComment('text');
		$c->setKind(CommentKindEnum::SLASH);
		$this->assertEq('kind updated', CommentKindEnum::SLASH, $c->getKind());
	}

	public function testDefaultKindIsDoc(): void
	{
		$c = new PHPComment('text');
		$this->assertEq('default kind', CommentKindEnum::DOC, $c->getKind());
	}
}
