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
use OLIUP\CG\PHPValue;
use OLIUP\CG\PHPVar;

class PHPValueTest extends TestCase
{
    private PHPPrinter $printer;

    protected function setUp(): void
    {
        $this->printer = new PHPPrinter();
    }

    public function testString(): void
    {
        $this->assertEq('quoted', "'hello'", $this->printer->printValue(new PHPValue('hello')));
    }

    public function testInt(): void
    {
        $this->assertEq('integer', '42', $this->printer->printValue(new PHPValue(42)));
    }

    public function testBoolTrue(): void
    {
        $this->assertEq('true', 'true', $this->printer->printValue(new PHPValue(true)));
    }

    public function testBoolFalse(): void
    {
        $this->assertEq('false', 'false', $this->printer->printValue(new PHPValue(false)));
    }

    public function testNull(): void
    {
        $this->assertEq('NULL', 'NULL', $this->printer->printValue(new PHPValue(null)));
    }

    public function testArray(): void
    {
        $out = $this->printer->printValue(new PHPValue([1, 2]));
        $this->assertHasStr('array keyword', 'array', $out);
    }

    public function testSetValue(): void
    {
        $v = new PHPValue('original');
        $v->setValue('updated');
        $this->assertEq('updated', 'updated', $v->getValue());
    }

    public function testPHPVarByReference(): void
    {
        $var = new PHPVar('x');
        $var->reference(true);
        $out = $this->printer->printVar($var);
        $this->assertHasStr('& prefix', '&$x', $out);
        $this->assertNotHasStr('no semicolon for ref', ';', $out);
    }

    public function testPHPVarWithValue(): void
    {
        $var = new PHPVar('count');
        $var->setValue(0);
        $out = $this->printer->printVar($var);
        $this->assertHasStr('value', '= 0', $out);
        $this->assertHasStr('semicolon', ';', $out);
    }
}
