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

namespace OLIUP\CG\Traits;

use OLIUP\CG\Enums\VisibilityEnum;

/**
 * Trait VisibilityAwareTrait.
 */
trait VisibilityAwareTrait
{
	protected ?VisibilityEnum $visibility = null;

	/**
	 * @param null|VisibilityEnum $visibility
	 *
	 * @return $this
	 */
	public function setVisibility(?VisibilityEnum $visibility): static
	{
		$this->visibility = $this->validateVisibility($visibility);

		return $this;
	}

	/**
	 * @return null|VisibilityEnum
	 */
	public function getVisibility(): ?VisibilityEnum
	{
		return $this->visibility;
	}

	/**
	 * @return $this
	 */
	public function private(): static
	{
		return $this->setVisibility(VisibilityEnum::PRIVATE);
	}

	/**
	 * @return $this
	 */
	public function protected(): static
	{
		return $this->setVisibility(VisibilityEnum::PROTECTED);
	}

	/**
	 * @return $this
	 */
	public function public(): static
	{
		return $this->setVisibility(VisibilityEnum::PUBLIC);
	}

	/**
	 * @return bool
	 */
	public function isPrivate(): bool
	{
		return VisibilityEnum::PRIVATE === $this->visibility;
	}

	/**
	 * @return bool
	 */
	public function isProtected(): bool
	{
		return VisibilityEnum::PROTECTED === $this->visibility;
	}

	/**
	 * @return bool
	 */
	public function isPublic(): bool
	{
		return VisibilityEnum::PUBLIC === $this->visibility;
	}

	/**
	 * @param null|VisibilityEnum $visibility
	 *
	 * @return null|VisibilityEnum
	 */
	abstract protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum;
}
