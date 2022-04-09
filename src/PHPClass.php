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

use OLIUP\CG\Traits\AbstractAwareTrait;
use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\ConstantsAwareTrait;
use OLIUP\CG\Traits\FinalAwareTrait;
use OLIUP\CG\Traits\ImplementsAwareTrait;
use OLIUP\CG\Traits\MethodsAwareTrait;
use OLIUP\CG\Traits\PropertiesAwareTrait;
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\UseTraitsAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use RuntimeException;

/**
 * Class PHPClass.
 */
class PHPClass
{
	use AbstractAwareTrait;
	use ChildrenAwareTrait {
		ChildrenAwareTrait::addChild as private addChildReal;
	}
	use CommentAwareTrait;
	use CommonTrait;
	use ConstantsAwareTrait;
	use FinalAwareTrait;
	use ImplementsAwareTrait;
	use MethodsAwareTrait;
	use PropertiesAwareTrait;
	use QualifiedNameAwareTrait;
	use UseTraitsAwareTrait;
	use ValidateAwareTrait;

	protected ?PHPClass $parent_class = null;

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
		if ($child instanceof PHPProperty) {
			return $this->addProperty($child);
		}
		if ($child instanceof PHPMethod) {
			return $this->addMethod($child);
		}

		return $this->addChildReal($child);
	}

	/**
	 * @param null|PHPClass|string $class
	 *
	 * @return $this
	 */
	public function extends(null|string|PHPClass $class): static
	{
		$this->parent_class = \is_string($class) ? new self($class) : $class;

		return $this;
	}

	/**
	 * @return null|PHPClass
	 */
	public function getExtends(): ?self
	{
		return $this->parent_class;
	}

	/**
	 * @return bool
	 */
	public function isAnonymous(): bool
	{
		return empty($this->name);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void
	{
		if ($this->isFinal() && $this->isAbstract()) {
			throw new RuntimeException('a class cannot be abstract and final.');
		}
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
	protected function validateNamespace(?PHPNamespace $namespace): ?PHPNamespace
	{
		return $namespace;
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
	protected function validateUseTrait(PHPUseTrait $use_trait): PHPUseTrait
	{
		return $use_trait;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateImplements(PHPInterface $interface): PHPInterface
	{
		return $interface;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		return $child;
	}
}
