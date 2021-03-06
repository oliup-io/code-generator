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

use OLIUP\CG\Enums\VisibilityEnum;
use OLIUP\CG\Traits\AbstractAwareTrait;
use OLIUP\CG\Traits\ArgumentsAwareTrait;
use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\FinalAwareTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\StaticAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;
use OLIUP\CG\Utils\Utils;
use RuntimeException;

/**
 * Class PHPMethod.
 */
class PHPMethod
{
	use AbstractAwareTrait;
	use ArgumentsAwareTrait;
	use ChildrenAwareTrait;
	use CommentAwareTrait;
	use CommonTrait;
	use FinalAwareTrait;
	use NameAwareTrait;
	use StaticAwareTrait;
	use ValidateAwareTrait;
	use VisibilityAwareTrait;

	protected ?PHPType $return_type = null;

	public function __construct(string $name = '')
	{
		$this->setName($name);
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
	 * @return null|\OLIUP\CG\PHPType
	 */
	public function getReturnType(): ?PHPType
	{
		return $this->return_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void
	{
		if ($this->isFinal() && $this->isAbstract()) {
			throw new RuntimeException('a method cannot be abstract and final.');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		return Utils::validateMethodName($name);
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

	/**
	 * {@inheritDoc}
	 */
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		return $visibility;
	}
}
