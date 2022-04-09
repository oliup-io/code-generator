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

namespace OLIUP\CG;

use InvalidArgumentException;
use OLIUP\CG\Enums\CommentKindEnum;
use OLIUP\CG\Enums\VisibilityEnum;
use OLIUP\CG\Traits\ValidateAwareTrait;
use PHPUtils\ClassUtils;
use PHPUtils\Str;

/**
 * Class PHPPrinter.
 */
class PHPPrinter
{
	public function __construct(protected string $indent = "\t")
	{
	}

	public function printArgument(PHPArgument $v, bool $allow_promoted = false, bool $allow_reference = true): string
	{
		$this->validate($v);

		$out = '';

		if ($allow_promoted && $v->isPromoted()) {
			$visibility = $v->getVisibility() ?? VisibilityEnum::PRIVATE;
			$out .= $visibility->value . ' ';
		}

		$out .= ($type = $v->getType()) ? $this->printType($type) . ' ' : '';
		$out .= $allow_reference && $v->isByReference() ? '&' : '';
		$out .= $v->isVariadic() ? '...' : '';
		$out .= '$' . $v->getName();
		$out .= ($value = $v->getValue()) ? ' = ' . $this->printValue($value) : '';

		return $out;
	}

	public function printClass(PHPClass $v): string
	{
		$this->validate($v);

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';

		if ($v->isAnonymous()) {
			$out .= 'new class';
		} else {
			if ($v->isAbstract()) {
				$out .= 'abstract ';
			} else {
				$out .= $v->isFinal() ? 'final ' : '';
			}

			$out .= 'class ' . $v->getName();
		}

		$out .= ($extends = $v->getExtends()) ? ' extends ' . $extends->getFullyQualifiedName() : '';
		$interfaces       = $v->getImplements();
		$use_traits       = $v->getUsedTraits();
		$constants        = $v->getConstants();
		$properties       = $v->getProperties();
		$methods          = $v->getMethods();

		if (!empty($interfaces)) {
			$out .= ' implements ' . \implode(' , ', \array_map(static fn ($it) => $it->getFullyQualifiedName(), $interfaces));
		}

		$out .= \PHP_EOL . '{';

		$body_parts = [];

		if (!empty($use_traits)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($ut) => $this->printUseTrait($ut), $use_traits));
		}

		if (!empty($constants)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($c) => $this->printConstant($c), $constants));
		}

		if (!empty($properties)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($c) => $this->printProperty($c), $properties));
		}
		if (!empty($methods)) {
			$body_parts[] = \implode(\PHP_EOL . \PHP_EOL, \array_map(fn ($c) => $this->printMethod($c), $methods));
		}
		foreach ($v->getChildren() as $child) {
			$body_parts[] = $this->print($child);
		}

		if (!empty($body_parts)) {
			$body = \implode(\PHP_EOL, $body_parts);
			$out .= \PHP_EOL . Str::indent($body, $this->indent);
		}

		$out .= \PHP_EOL . '}';

		return $out;
	}

	public function printInterface(PHPInterface $v): string
	{
		$this->validate($v);

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= 'interface ' . $v->getName();

		$extends   = $v->getExtends();
		$constants = $v->getConstants();
		$methods   = $v->getMethods();

		if (!empty($extends)) {
			$out .= ' extends ' . \implode(' , ', \array_map(static fn ($it) => $it->getFullyQualifiedName(), $extends));
		}

		$out .= \PHP_EOL . '{';

		$body_parts = [];

		if (!empty($constants)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($c) => $this->printConstant($c), $constants));
		}

		if (!empty($methods)) {
			$body_parts[] = \implode(\PHP_EOL . \PHP_EOL, \array_map(fn ($c) => $this->printMethod($c), $methods));
		}
		foreach ($v->getChildren() as $child) {
			$body_parts[] = $this->print($child);
		}

		if (!empty($body_parts)) {
			$body = \implode(\PHP_EOL, $body_parts);
			$out .= \PHP_EOL . Str::indent($body, $this->indent);
		}

		$out .= \PHP_EOL . '}';

		return $out;
	}

	public function printTrait(PHPTrait $v): string
	{
		$this->validate($v);

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= 'trait ' . $v->getName();

		$use_traits = $v->getUsedTraits();
		$properties = $v->getProperties();
		$methods    = $v->getMethods();

		$out .= \PHP_EOL . '{';

		$body_parts = [];

		if (!empty($use_traits)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($ut) => $this->printUseTrait($ut), $use_traits));
		}

		if (!empty($properties)) {
			$body_parts[] = \implode(\PHP_EOL, \array_map(fn ($c) => $this->printProperty($c), $properties));
		}
		if (!empty($methods)) {
			$body_parts[] = \implode(\PHP_EOL . \PHP_EOL, \array_map(fn ($c) => $this->printMethod($c), $methods));
		}
		foreach ($v->getChildren() as $child) {
			$body_parts[] = $this->print($child);
		}

		if (!empty($body_parts)) {
			$body = \implode(\PHP_EOL, $body_parts);
			$out .= \PHP_EOL . Str::indent($body, $this->indent);
		}

		$out .= \PHP_EOL . '}';

		return $out;
	}

	public function printEnum(PHPEnum $v): string
	{
		$this->validate($v);

		return '';
	}

	public function printUseTrait(PHPUseTrait $v): string
	{
		$this->validate($v);
		$t     = $v->getTrait();
		$out   = 'use ' . $t->getFullyQualifiedName(true);
		$rules = $v->getRules();

		if (!empty($rules)) {
			$body = '';
			foreach ($rules as $rule) {
				$ref        = $t->getFullyQualifiedName() . '::' . $rule->getMethod()
					->getName();
				$visibility = $rule->getVisibility();
				if ($dt = $rule->getHiddenFromTrait()) {
					$body .= $ref . ' insteadof ' . $dt->getFullyQualifiedName() . ';' . \PHP_EOL;
				}

				if (!empty($rule->getName())) {
					$body .= $ref . ' as ' . $visibility?->value . ' ' . $rule->getName() . ';' . \PHP_EOL;
				} elseif ($visibility) {
					$body .= $ref . ' as ' . $visibility->value . ';' . \PHP_EOL;
				}
			}

			$out .= '{' . \PHP_EOL . Str::indent($body, $this->indent) . '}';
		} else {
			$out .= ';';
		}

		return $out;
	}

	public function printMethod(PHPMethod $v, bool $declaration = false, bool $virtual = false): string
	{
		$this->validate($v);

		if ($virtual) {
			$out           = '@method ';
			$out .= ($type = $v->getReturnType()) ? $this->printType($type) . ' ' : '';
			$out .= $v->getName() . '(';
			$args = $v->getArguments();
			if (!empty($args)) {
				$out .= \implode(', ', \array_map(fn ($arg) => $this->printArgument($arg), $args));
			}
			$out .= ')';

			return $out;
		}

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';

		if (!$declaration) {
			if ($v->isAbstract()) {
				$out .= 'abstract ';
			} else {
				$out .= $v->isFinal() ? 'final ' : '';
			}
		}

		$out .= ($vb = $v->getVisibility()) ? $vb->value . ' ' : '';

		$out .= $v->isStatic() ? 'static ' : '';
		$method_name = $v->getName();
		$out .= 'function ' . $method_name . '(';

		$args                   = $v->getArguments();
		$arg_allow_promoted_arg = '__construct' === $method_name;

		if (!empty($args)) {
			$out .= \implode(', ', \array_map(fn ($arg) => $this->printArgument($arg, $arg_allow_promoted_arg), $args));
		}

		$out .= ')';

		$out .= ($type = $v->getReturnType()) ? ': ' . $this->printType($type) : '';

		if ($declaration || $v->isAbstract()) {
			$out .= ';';
		} else {
			$out .= \PHP_EOL . '{' . \PHP_EOL;

			$body = '';
			foreach ($v->getChildren() as $child) {
				$body .= $this->print($child);
			}

			$out .= Str::indent($body, $this->indent);

			$out .= \PHP_EOL . '}';
		}

		return $out;
	}

	public function printFunction(PHPFunction $v): string
	{
		$this->validate($v);

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';

		$out .= $v->isStatic() && $v->isAnonymous() ? 'static function' : 'function';
		$out .= ($v->isAnonymous() ? '' : ' ' . $v->getName()) . '(';

		$args = $v->getArguments();
		if (!empty($args)) {
			$out .= \implode(', ', \array_map(fn ($arg) => $this->printArgument($arg), $args));
		}

		$out .= ')';

		$uses = $v->getUses();
		if (!empty($uses)) {
			$out .= ' use (' . \implode(', ', \array_map(fn ($var) => $this->printVar($var), $uses)) . ')';
		}

		$out .= ($type = $v->getReturnType()) ? ': ' . $this->printType($type) : '';

		$out .= \PHP_EOL . '{' . \PHP_EOL;

		$body = '';
		foreach ($v->getChildren() as $child) {
			$body .= $this->print($child);
		}

		$out .= Str::indent($body, $this->indent);

		$out .= \PHP_EOL . '}';

		return $out;
	}

	public function printType(PHPType $v): string
	{
		$this->validate($v);
		$types = $v->getTypes();
		unset($types['null']);

		$temp  = [];
		$count = 0;

		foreach ($types as $t) {
			if ($t instanceof PHPTypeIntersection) {
				$temp[] = \implode('&', $t->getTypes());
			} elseif ($t instanceof PHPClass || $t instanceof PHPInterface) {
				$temp[] = $t->getFullyQualifiedName();
			} else {
				$temp[] = $t;
			}
			++$count;
		}

		if ($v->isNullable()) {
			return 1 === $count ? '?' . $temp[0] : 'null|' . \implode('|', $temp);
		}

		return \implode('|', $temp);
	}

	public function printConstant(PHPConstant $v): string
	{
		$this->validate($v);

		$out         = ($c         = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= ($vb = $v->getVisibility()) ? $vb->value . ' const ' : 'const ';
		$out .= $v->getName();
		$out .= ' = ' . $this->printValue($v->getValue());

		$out .= ';';

		return $out;
	}

	public function printProperty(PHPProperty $v): string
	{
		$this->validate($v);

		$out         = ($c         = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= ($vb = $v->getVisibility()) ? $vb->value . ' ' : '';
		$out .= $v->isStatic() ? 'static ' : '';
		$out .= ($type = $v->getType()) ? $this->printType($type) . ' ' : '';
		$out .= '$' . $v->getName();
		$out .= ($value = $v->getValue()) ? ' = ' . $this->printValue($value) : '';

		$out .= ';';

		return $out;
	}

	public function printVar(PHPVar $v): string
	{
		$this->validate($v);

		$out = ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= ($v->isByReference() ? '&$' : '$') . $v->getName();

		if (!$v->isByReference()) {
			$out .= ($value = $v->getValue()) ? ' = ' . $this->printValue($value) : '';
			$out .= ';';
		}

		return $out;
	}

	public function printNamespace(PHPNamespace $v, bool $scoped = false): string
	{
		$this->validate($v);

		$out           = 'namespace ' . $v->getName();
		[$start, $end] = $scoped ? ['{' . \PHP_EOL, '}' . \PHP_EOL] : [';' . \PHP_EOL . \PHP_EOL, ''];
		$out .= $start;

		$body = '';
		foreach ($v->getChildren() as $child) {
			$body .= $this->print($child);
		}

		$out .= $scoped ? Str::indent($body, $this->indent) : $body;

		$out .= $end;

		return $out;
	}

	public function printValue(PHPValue $v): string
	{
		$this->validate($v);

		$value = $v->getValue();

		if ($value instanceof PHPClass && $value->isAnonymous()) {
			return $this->printClass($value);
		}

		return \var_export($value, true);
	}

	public function printComment(PHPComment $v): string
	{
		$this->validate($v);

		return match ($v->getKind()) {
			CommentKindEnum::DOC       => '/**' . \PHP_EOL . Str::indent($v->getContent(), ' * ', 1, true) . \PHP_EOL . ' */',
			CommentKindEnum::MULTILINE => '/*' . \PHP_EOL . $v->getContent() . \PHP_EOL . '*/',
			CommentKindEnum::HASH      => Str::indent($v->getContent(), '# ', 1, true),
			CommentKindEnum::SLASH     => Str::indent($v->getContent(), '// ', 1, true),
		};
	}

	public function printFile(PHPFile $v): string
	{
		$this->validate($v);

		$out        = '<?php' . \PHP_EOL;
		$out .= ($c = $v->getComment()) ? $this->printComment($c) . \PHP_EOL : '';
		$out .= $v->isStrict() ? 'declare(strict_types=1);' . \PHP_EOL . \PHP_EOL : '';

		foreach ($v->getChildren() as $child) {
			$out .= $this->print($child);
			$out .= \PHP_EOL;
		}

		return $out;
	}

	public function printRaw(PHPRaw $v): string
	{
		$this->validate($v);

		return $v->getSource();
	}

	/**
	 * @param object $param
	 *
	 * @return string
	 */
	public function print(object $param): string
	{
		return match (true) {
			$param instanceof PHPFile      => $this->printFile($param),
			$param instanceof PHPRaw       => $this->printRaw($param),
			$param instanceof PHPClass     => $this->printClass($param),
			$param instanceof PHPInterface => $this->printInterface($param),
			$param instanceof PHPTrait     => $this->printTrait($param),
			$param instanceof PHPEnum      => $this->printEnum($param),
			$param instanceof PHPMethod    => $this->printMethod($param),
			$param instanceof PHPFunction  => $this->printFunction($param),
			$param instanceof PHPUseTrait  => $this->printUseTrait($param),
			$param instanceof PHPArgument  => $this->printArgument($param),
			$param instanceof PHPType      => $this->printType($param),
			$param instanceof PHPValue     => $this->printValue($param),
			$param instanceof PHPVar       => $this->printVar($param),
			$param instanceof PHPProperty  => $this->printProperty($param),
			$param instanceof PHPConstant  => $this->printConstant($param),
			$param instanceof PHPComment   => $this->printComment($param),
			$param instanceof PHPNamespace => $this->printNamespace($param),
			default                        => throw new InvalidArgumentException(\sprintf('object of type "%s" is not printable.', \get_debug_type($param)))
		};
	}

	private function validate(object $v): void
	{
		if (ClassUtils::hasTrait($v, ValidateAwareTrait::class)) {
			/** @var PHPClass $v */
			$v->validate();
		}
	}
}
