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

use Closure;
use OLIUP\CG\PHPComment;
use OLIUP\CG\PHPFile;
use OLIUP\CG\PHPFunction;
use OLIUP\CG\PHPInterface;
use OLIUP\CG\PHPNamespace;
use OLIUP\CG\PHPTrait;
use OLIUP\CG\PHPType;
use OLIUP\CG\PHPUseTrait;
use OLIUP\CG\PHPUseTraitMethodRule;
use OLIUP\CG\PHPValue;
use OLIUP\CG\PHPVar;

/**
 * @internal
 *
 * @coversNothing
 */
final class SnapshotTest extends TestCase
{
	private string $dir;

	protected function setUp(): void
	{
		$this->dir = __DIR__ . '/snapshots';
	}

	public function testClassWithConstructorPromotion(): void
	{
		$file = new PHPFile();
		$file->setComment("Copyright (c) OLIUP <dev@oliup.com>.\n\nThis file is part of the Oliup CodeGenerator package.");

		$ns = new PHPNamespace('App\Models');
		$ns->use('App\Contracts\Identifiable');
		$file->addChild($ns);

		$class = $ns->newClass('User');
		$class->final()->implements('App\Contracts\Identifiable');
		$class->setComment("Class User.\n\n@package App\\Models");
		$class->newConstant('ROLE_ADMIN', 'admin');
		$class->newConstant('ROLE_USER', 'user');

		$nameProp = $class->newProperty('name');
		$nameProp->setType('string')->protected();

		$ctor = $class->newMethod('__construct');
		$ctor->public();
		$ctor->newArgument('id')->setType('int')->public();
		$ctor->newArgument('name')->setType('string');
		$ctor->addChild('$this->name = $name;');

		$getId = $class->newMethod('getId');
		$getId->public()->setReturnType('int')->addChild('return $this->id;');

		$getName = $class->newMethod('getName');
		$getName->public()->setReturnType('string')->addChild('return $this->name;');

		$this->assertSnapshot($this->dir . '/class-with-promotion.php', (string) $file);
	}

	public function testInterfaceWithMethodsAsDeclarations(): void
	{
		$file = new PHPFile();
		$ns   = new PHPNamespace('App\Contracts');
		$file->addChild($ns);

		$iface = new PHPInterface('Repository');
		$iface->extends('App\Contracts\Countable');
		$iface->setComment('Interface Repository.');
		$ns->addChild($iface);

		$iface->newConstant('DEFAULT_LIMIT', 20);

		$find = $iface->newMethod('findById');
		$find->public()->setReturnType('mixed');
		$find->newArgument('id')->setType('int');

		$findAll = $iface->newMethod('findAll');
		$findAll->public()->setReturnType('array');
		$findAll->newArgument('limit')->setType('int')->setValue(20);

		$this->assertSnapshot($this->dir . '/interface-with-methods.php', (string) $file);
	}

	public function testTraitWithMethodConflictRules(): void
	{
		$file = new PHPFile();
		$ns   = new PHPNamespace('App\Traits');
		$file->addChild($ns);

		$trA = new PHPTrait('TimestampableTrait');
		$ns->addChild($trA);
		$trA->newMethod('getCreatedAt')->public()->setReturnType('string')
			->addChild('return $this->created_at;');

		$trB = new PHPTrait('LoggableTrait');
		$ns->addChild($trB);
		$trB->newMethod('log')->public()->setReturnType('void')
			->addChild('error_log($this->getName());');

		$class = $ns->newClass('AuditableEntity');
		$ns->addChild($class);

		$utA  = new PHPUseTrait($trA);
		$utB  = new PHPUseTrait($trB);
		$rule = new PHPUseTraitMethodRule('log');
		$rule->setName('writeLog')->public();
		$utB->addRule($rule);
		$class->useTrait($utA);
		$class->useTrait($utB);

		$this->assertSnapshot($this->dir . '/trait-conflict-rules.php', (string) $file);
	}

	public function testClosureWithUseCaptures(): void
	{
		$file = new PHPFile();
		$ns   = new PHPNamespace('App\Utils');
		$file->addChild($ns);

		$class = $ns->newClass('Transformer');

		$fn = new PHPFunction();
		$fn->newArgument('input')->setType('string');
		$fn->setReturnType('string');

		$prefix = new PHPVar('prefix');
		$prefix->setValue('PREFIX_');
		$fn->use($prefix);
		$fn->addChild('return $prefix . strtoupper($input);');

		$method = $class->newMethod('createTransformer');
		$method->public()->newArgument('prefix')->setType('string')->setValue('PREFIX_');
		$method->public()->setReturnType('\\' . Closure::class);
		$method->addChild('return ' . $fn . ';');

		$this->assertSnapshot($this->dir . '/closure-with-use.php', (string) $file);
	}

	public function testAbstractClassWithUnionTypes(): void
	{
		$file = new PHPFile();
		$ns   = new PHPNamespace('App\Domain');
		$file->addChild($ns);

		$class = $ns->newClass('AbstractHandler');
		$class->abstract();
		$class->setComment('Base handler.');

		$handleMethod = $class->newMethod('handle');
		$handleMethod->abstract()->public();
		$handleMethod->newArgument('payload')->setType('mixed');
		$handleMethod->setReturnType((new PHPType('string', 'int'))->nullable());

		$logMethod = $class->newMethod('log');
		$logMethod->protected()->setReturnType('void');
		$logMethod->newArgument('message')->setType('string');
		$logMethod->addChild('echo $message;');

		$staticMethod = $class->newMethod('create');
		$staticMethod->public()->static()->setReturnType('static');
		$staticMethod->addChild('return new static();');

		$this->assertSnapshot($this->dir . '/abstract-class-union-types.php', (string) $file);
	}

	public function testClassWithMixedComments(): void
	{
		$file = new PHPFile();
		$ns   = new PHPNamespace('App\Config');
		$file->addChild($ns);

		$class = $ns->newClass('Config');
		$class->final();
		$class->setComment("Holds application config.\n\n@package App\\Config");

		$prop = $class->newProperty('data');
		$prop->private()->setType('array');
		$prop->setComment('Raw config array.');
		$prop->setValue(new PHPValue([]));

		$load = $class->newMethod('load');
		$load->public()->setReturnType('void');
		$load->newArgument('path')->setType('string');
		$load->setComment("Load config from file.\n\n@param string \$path path to PHP config file");
		$load->addChild(PHPComment::inline('validate before reading'));
		$load->addChild('if (!is_file($path)) { throw new \InvalidArgumentException("Not a file: $path"); }');
		$load->addChild('$this->data = require $path;');

		$get = $class->newMethod('get');
		$get->public()->setReturnType((new PHPType('mixed'))->nullable());
		$get->newArgument('key')->setType('string');
		$get->newArgument('default')->setType('mixed')->setValue(null);
		$get->addChild('return $this->data[$key] ?? $default;');

		$this->assertSnapshot($this->dir . '/class-with-comments.php', (string) $file);
	}
}
