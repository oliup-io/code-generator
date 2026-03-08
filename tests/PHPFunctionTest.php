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

use OLIUP\CG\PHPFunction;
use OLIUP\CG\PHPPrinter;
use OLIUP\CG\PHPVar;

class PHPFunctionTest extends TestCase
{
    private PHPPrinter $printer;

    protected function setUp(): void
    {
        $this->printer = new PHPPrinter();
    }

    public function testNamedFunction(): void
    {
        $fn = new PHPFunction('add');
        $fn->newArgument('a')->setType('int');
        $fn->newArgument('b')->setType('int');
        $fn->setReturnType('int');
        $fn->addChild('return $a + $b;');
        $out = $this->printer->printFunction($fn);
        $this->assertHasStr('function keyword', 'function add(', $out);
        $this->assertHasStr('arg', 'int $a', $out);
        $this->assertHasStr('return type', ': int', $out);
        $this->assertHasStr('body', 'return $a + $b;', $out);
    }

    public function testAnonymousClosure(): void
    {
        $fn = new PHPFunction();
        $fn->addChild('return 42;');
        $out = $this->printer->printFunction($fn);
        $this->assertHasStr('anonymous function', 'function(', $out);
        $this->assertNotHasStr('no name', 'function (', $out);
    }

    public function testClosureWithUseCaptures(): void
    {
        $fn  = new PHPFunction();
        $var = new PHPVar('x');
        $fn->use($var);
        $fn->addChild('return $x;');
        $out = $this->printer->printFunction($fn);
        $this->assertHasStr('use clause', 'use (', $out);
        $this->assertHasStr('captured var', '$x', $out);
    }

    public function testStaticAnonymous(): void
    {
        $fn = new PHPFunction();
        $fn->static();
        $out = $this->printer->printFunction($fn);
        $this->assertHasStr('static keyword', 'static function', $out);
    }

    public function testFunctionIsAnonymous(): void
    {
        $this->assertEq('anonymous', true, (new PHPFunction())->isAnonymous());
        $this->assertEq('named', false, (new PHPFunction('foo'))->isAnonymous());
    }

    public function testIsUsingVar(): void
    {
        $fn  = new PHPFunction();
        $var = new PHPVar('ctx');
        $fn->use($var);
        $this->assertEq('found by name', true, $fn->isUsing('ctx'));
        $this->assertEq('found by var', true, $fn->isUsing($var));
        $this->assertEq('not found', false, $fn->isUsing('other'));
    }
}
