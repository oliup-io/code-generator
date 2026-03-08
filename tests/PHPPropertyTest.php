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
use OLIUP\CG\PHPProperty;
use OLIUP\CG\PHPValue;

class PHPPropertyTest extends TestCase
{
    private PHPPrinter $printer;

    protected function setUp(): void
    {
        $this->printer = new PHPPrinter();
    }

    public function testConstructorSetsName(): void
    {
        $p = new PHPProperty('myProp');
        $this->assertEq('name set by constructor', 'myProp', $p->getName());
    }

    public function testConstructorSetsValue(): void
    {
        $p = new PHPProperty('count', new PHPValue(0));
        $this->assertEq('value set by constructor', 0, $p->getValue()?->getValue());
    }

    public function testConstructorValueNullByDefault(): void
    {
        $p = new PHPProperty('x');
        $this->assertEq('null by default', null, $p->getValue());
    }

    public function testSetType(): void
    {
        $p   = new PHPProperty('id');
        $out = $this->printer->printProperty($p->setType('int')->public());
        $this->assertHasStr('type', 'int', $out);
        $this->assertHasStr('name', '$id', $out);
    }

    public function testPublicVisibility(): void
    {
        $p   = new PHPProperty('name');
        $out = $this->printer->printProperty($p->public()->setType('string'));
        $this->assertHasStr('public', 'public', $out);
    }

    public function testPrivateVisibility(): void
    {
        $p   = new PHPProperty('secret');
        $out = $this->printer->printProperty($p->private()->setType('string'));
        $this->assertHasStr('private', 'private', $out);
    }

    public function testStaticProperty(): void
    {
        $p   = new PHPProperty('counter');
        $out = $this->printer->printProperty($p->public()->setType('int')->static());
        $this->assertHasStr('static keyword', 'static', $out);
    }

    public function testWithDefaultValue(): void
    {
        $p   = new PHPProperty('flag');
        $out = $this->printer->printProperty($p->public()->setType('bool')->setValue(false));
        $this->assertHasStr('default value', '= false', $out);
    }
}
