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

	// according to "http://php.net/manual/en/language.oop5.basic.php" visited on 1st Sept. 2017
	// php class name in regexp should be : ^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$
	// but we constrain the class name to start with a letter
	// and to contain only letters, numbers and underscores and underscores
	public const CLASS_NAME_PATTERN = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';

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
	public function extends(null|self|string $class): static
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
		if (empty($name)) {
			return '';
		}
		if (!\preg_match(self::CLASS_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException(\sprintf('Invalid class name: %s', $name));
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
