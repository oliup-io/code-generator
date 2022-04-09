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

use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\NamespaceAwareTrait;
use OLIUP\CG\Utils\Utils;
use PHPUtils\ClassUtils;

/**
 * Class PHPNamespace.
 */
class PHPNamespace
{
	use ChildrenAwareTrait;
	use CommonTrait;
	use NameAwareTrait;

	public function __construct(string $namespace)
	{
		$this->setName($namespace);
	}

	public function newClass(string $name): PHPClass
	{
		$class = new PHPClass($name);
		$this->addChild($class);

		return $class;
	}

	/**
	 * @return bool
	 */
	public function isGlobal(): bool
	{
		return '' === $this->name;
	}

	/**
	 * @param \OLIUP\CG\PHPNamespace $namespace
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
		return empty($name) ? '' : \trim(Utils::validateNamespace($name), '\\');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		if (ClassUtils::hasTrait($child, NamespaceAwareTrait::class)) {
			/** @var PHPClass $child */
			$child->setNamespace($this);
		}

		return $child;
	}
}
