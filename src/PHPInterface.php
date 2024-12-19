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
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\ConstantsAwareTrait;
use OLIUP\CG\Traits\ExtendsInterfacesAwareTrait;
use OLIUP\CG\Traits\MethodsAwareTrait;
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;

/**
 * Class PHPInterface.
 */
class PHPInterface
{
	use ChildrenAwareTrait {
		ChildrenAwareTrait::addChild as private addChildReal;
	}
	use CommentAwareTrait;
	use CommonTrait;
	use ConstantsAwareTrait;
	use ExtendsInterfacesAwareTrait;
	use MethodsAwareTrait;
	use QualifiedNameAwareTrait;
	use ValidateAwareTrait;

	public function __construct(string $name = '')
	{
		$this->setName($name);
	}

	/**
	 * {@inheritDoc}
	 */
	public function addChild(object|string $child): static
	{
		if ($child instanceof PHPConstant) {
			return $this->addConstant($child);
		}
		if ($child instanceof PHPMethod) {
			return $this->addMethod($child);
		}

		return $this->addChildReal($child);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void {}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(PHPClass::CLASS_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException(\sprintf('Invalid interface name: %s', $name));
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateNamespace(?PHPNamespace $namespace): ?PHPNamespace
	{
		return $namespace;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		return $child;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateExtendsInterface(self $interface): self
	{
		return $interface;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateConstant(PHPConstant $constant): PHPConstant
	{
		return $constant;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateMethod(PHPMethod $method): PHPMethod
	{
		return $method;
	}
}
