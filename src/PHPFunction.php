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

use OLIUP\CG\Traits\ArgumentsAwareTrait;
use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\StaticAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use OLIUP\CG\Utils\Utils;

/**
 * Class PHPFunction.
 */
class PHPFunction
{
	use ArgumentsAwareTrait;
	use ChildrenAwareTrait;
	use CommentAwareTrait;
	use CommonTrait;
	use QualifiedNameAwareTrait;
	use StaticAwareTrait;
	use ValidateAwareTrait;

	/** @var PHPVar[] */
	protected array    $uses        = [];
	protected ?PHPType $return_type = null;

	public function __construct(string $name = '')
	{
		$this->setName($name);
	}

	/**
	 * @return bool
	 */
	public function isAnonymous(): bool
	{
		return empty($this->name);
	}

	/**
	 * @param null|PHPClass|PHPInterface|PHPType|string $return_type
	 *
	 * @return $this
	 */
	public function setReturnType(null|string|PHPType|PHPClass|PHPInterface $return_type): static
	{
		$this->return_type = (null === $return_type || $return_type instanceof PHPType) ? $return_type : new PHPType($return_type);

		return $this;
	}

	/**
	 * @return null|PHPType
	 */
	public function getReturnType(): ?PHPType
	{
		return $this->return_type;
	}

	/**
	 * @return PHPVar[]
	 */
	public function getUses(): array
	{
		return $this->uses;
	}

	/**
	 * @param PHPVar|string $var
	 *
	 * @return bool
	 */
	public function isUsing(string|PHPVar $var): bool
	{
		return isset($this->uses[\is_string($var) ? $var : $var->getName()]);
	}

	/**
	 * @param PHPVar $var
	 *
	 * @return $this
	 */
	public function use(PHPVar $var): static
	{
		$this->uses[$var->getName()] = $var;

		return $this;
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
		if (empty($name)) {
			return '';
		}

		return Utils::validateFunctionName($name);
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
	protected function validateArgument(PHPArgument $argument): PHPArgument
	{
		return $argument;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		return $child;
	}
}
