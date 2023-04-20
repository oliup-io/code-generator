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
use OLIUP\CG\Enums\VisibilityEnum;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;

/**
 * Class PHPUseTraitMethodRule.
 */
class PHPUseTraitMethodRule
{
	use CommentAwareTrait;
	use CommonTrait;
	use NameAwareTrait;
	use VisibilityAwareTrait;

	protected ?PHPTrait $hidden_from_trait = null;
	protected PHPMethod $method;

	public function __construct(string|PHPMethod $method)
	{
		$this->method = \is_string($method) ? new PHPMethod($method) : $method;
	}

	/**
	 * @return PHPMethod
	 */
	public function getMethod(): PHPMethod
	{
		return $this->method;
	}

	/**
	 * @return null|PHPTrait
	 */
	public function getHiddenFromTrait(): ?PHPTrait
	{
		return $this->hidden_from_trait;
	}

	/**
	 * @param null|PHPTrait|string $trait
	 *
	 * @return $this
	 */
	public function hideFrom(null|string|PHPTrait $trait): static
	{
		if (\is_string($trait)) {
			$trait = new PHPTrait($trait);
		}

		$this->hidden_from_trait = $trait;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(PHPMethod::METHOD_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid use trait method name: ' . $name);
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		return $visibility;
	}
}
