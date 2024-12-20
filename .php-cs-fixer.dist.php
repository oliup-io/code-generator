<?php

use OLIUP\CS\PhpCS;
use PhpCsFixer\Finder;

$finder = Finder::create();

$finder->in([
	__DIR__ . '/src',
	__DIR__ . '/tests',
])
	->name('*.php')
	->notPath('otpl_done')
	->notPath('blate_cache')
	->notPath('vendor')
	->notPath('samples')
	->notPath('ignore')
	->ignoreDotFiles(true)
	->ignoreVCS(true);

$header = <<<'EOF'
Copyright (c) OLIUP <dev@oliup.com>.

This file is part of the Oliup CodeGenerator package.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$rules = [
	'header_comment' => [
		'header'       => $header,
		'comment_type' => 'PHPDoc',
		'separate'     => 'both',
		'location'     => 'after_open'
	],
];

return (new PhpCS())->mergeRules($finder, $rules)
					->setRiskyAllowed(true);
