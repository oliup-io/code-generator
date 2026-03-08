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
use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\NamespaceAwareTrait;
use PHPUtils\ClassUtils;

/**
 * Class PHPNamespace.
 */
class PHPNamespace
{
	use ChildrenAwareTrait;
	use CommonTrait;
	use NameAwareTrait;

	public const NAMESPACE_PATTERN     = '#^[a-zA-Z_][a-zA-Z0-9_]*(\\\[a-zA-Z_][a-zA-Z0-9_]*)*$#';

	protected array $uses = [];

	public function __construct(string $namespace)
	{
		$this->setName($namespace);
	}

	public function use(PHPClass|PHPConstant|PHPEnum|PHPFunction|PHPInterface|string $fqn_name, ?string $as = null): self
	{
		if (!\is_string($fqn_name)) {
			$fqn_name = $fqn_name->getFullyQualifiedName(true);
		}

		$this->uses[$fqn_name] = $as;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getUses(): array
	{
		return $this->uses;
	}

	public function newClass(string $name): PHPClass
	{
		$class = new PHPClass($name);
		$this->addChild($class);

		return $class;
	}

	public function newInterface(string $name): PHPInterface
	{
		$interface = new PHPInterface($name);
		$this->addChild($interface);

		return $interface;
	}

	/**
	 * @return bool
	 */
	public function isGlobal(): bool
	{
		return '' === $this->name;
	}

	/**
	 * @param PHPNamespace $namespace
	 *
	 * @return bool
	 */
	public function isChildOf(self $namespace): bool
	{
		return $this->name !== $namespace->name && \str_starts_with($this->name, $namespace->name);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (empty($name)) {
			return '';
		}

		if (!\preg_match(self::NAMESPACE_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid namespace name: ' . $name);
		}

		return \trim($name, '\\');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		if ($child instanceof PHPFunction && $child->isAnonymous()) {
			throw new InvalidArgumentException(
				'Anonymous functions cannot be added directly to a namespace; assign the closure to a variable or return it from a named function.'
			);
		}

		if (ClassUtils::hasTrait($child, NamespaceAwareTrait::class)) {
			/** @var PHPClass $child */
			$child->setNamespace($this);
		}

		return $child;
	}
}
