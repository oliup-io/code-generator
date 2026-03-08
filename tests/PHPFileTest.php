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

use OLIUP\CG\PHPComment;
use OLIUP\CG\PHPFile;
use OLIUP\CG\PHPNamespace;
use OLIUP\CG\PHPPrinter;

class PHPFileTest extends TestCase
{
    private PHPPrinter $printer;

    protected function setUp(): void
    {
        $this->printer = new PHPPrinter();
    }

    public function testStrictModeEmitsDeclare(): void
    {
        $f = new PHPFile();
        $this->assertEq('strict by default', true, $f->isStrict());
        $out = $this->printer->printFile($f);
        $this->assertHasStr('declare strict_types', 'declare(strict_types=1);', $out);
    }

    public function testNonStrictMode(): void
    {
        $f   = new PHPFile(false);
        $out = $this->printer->printFile($f);
        $this->assertNotHasStr('no strict_types', 'strict_types', $out);
    }

    public function testToStringEquivalence(): void
    {
        $f  = new PHPFile();
        $ns = new PHPNamespace('App');
        $ns->newClass('Hello');
        $f->addChild($ns);
        $this->assertEq('toString == printFile', $this->printer->printFile($f), (string) $f);
    }

    public function testWithComment(): void
    {
        $f = new PHPFile();
        $f->setComment(PHPComment::doc('My package'));
        $out = $this->printer->printFile($f);
        $this->assertHasStr('comment', 'My package', $out);
    }

    public function testNamespaceAddedViaAddChild(): void
    {
        $f  = new PHPFile();
        $ns = new PHPNamespace('App');
        $f->addChild($ns);
        $out = $this->printer->printFile($f);
        $this->assertHasStr('namespace line', 'namespace App;', $out);
    }

    public function testOpeningTagPresent(): void
    {
        $out = $this->printer->printFile(new PHPFile());
        $this->assertHasStr('<?php tag', '<?php', $out);
    }
}
