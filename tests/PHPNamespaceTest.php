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
use OLIUP\CG\PHPNamespace;
use OLIUP\CG\PHPPrinter;

class PHPNamespaceTest extends TestCase
{
    private PHPPrinter $printer;

    protected function setUp(): void
    {
        $this->printer = new PHPPrinter();
    }

    public function testRendersNamespaceLine(): void
    {
        $ns  = new PHPNamespace('App\\Models');
        $out = $this->printer->printNamespace($ns);
        $this->assertHasStr('namespace line', 'namespace App\\Models;', $out);
    }

    public function testUseStatements(): void
    {
        $ns = new PHPNamespace('App');
        $ns->use('Some\\Other\\Service');
        $out = $this->printer->printNamespace($ns);
        $this->assertHasStr('use statement', 'use Some\\Other\\Service;', $out);
    }

    public function testAutoAssignsNamespaceOnAddChild(): void
    {
        $ns = new PHPNamespace('App\\Domain');
        $c  = new PHPClass('User');
        $ns->addChild($c);
        $this->assertEq('namespace set', 'App\\Domain', $c->getNamespace()?->getName());
    }

    public function testNewClassCreatesAndRegisters(): void
    {
        $ns = new PHPNamespace('App');
        $c  = $ns->newClass('Order');
        $this->assertEq('class name', 'Order', $c->getName());
        $this->assertEq('namespace set', 'App', $c->getNamespace()?->getName());
    }

    public function testIsGlobal(): void
    {
        $this->assertEq('global namespace', true, (new PHPNamespace(''))->isGlobal());
        $this->assertEq('non-global', false, (new PHPNamespace('App'))->isGlobal());
    }

    public function testIsChildOf(): void
    {
        $ns     = new PHPNamespace('App\\Models\\User');
        $parent = new PHPNamespace('App\\Models');
        $grand  = new PHPNamespace('App');
        $other  = new PHPNamespace('Other');
        $this->assertEq('direct parent', true, $ns->isChildOf($parent));
        $this->assertEq('grandparent', true, $ns->isChildOf($grand));
        $this->assertEq('not related', false, $ns->isChildOf($other));
    }

    public function testGetName(): void
    {
        $ns = new PHPNamespace('App\\Http');
        $this->assertEq('name matches', 'App\\Http', $ns->getName());
    }
}
