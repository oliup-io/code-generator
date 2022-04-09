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
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\MethodsAwareTrait;
use OLIUP\CG\Traits\PropertiesAwareTrait;
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\UseTraitsAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;

/**
 * Class PHPTrait.
 */
class PHPTrait
{
	use ChildrenAwareTrait {
		ChildrenAwareTrait::addChild as private addChildReal;
	}
	use CommentAwareTrait;
	use CommonTrait;
	use MethodsAwareTrait;
	use PropertiesAwareTrait;
	use QualifiedNameAwareTrait;
	use UseTraitsAwareTrait;
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
		if ($child instanceof PHPProperty) {
			return $this->addProperty($child);
		}
		if ($child instanceof PHPMethod) {
			return $this->addMethod($child);
		}

		return $this->addChildReal($child);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void
	{
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateMethod(PHPMethod $method): PHPMethod
	{
		return $method;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateProperty(PHPProperty $property): PHPProperty
	{
		return $property;
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
	protected function validateUseTrait(PHPUseTrait $use_trait): PHPUseTrait
	{
		return $use_trait;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		return $child;
	}
}
