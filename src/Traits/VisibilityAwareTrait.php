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

	public function setVisibility(?VisibilityEnum $visibility): static
	{
		$this->visibility = $this->validateVisibility($visibility);

		return $this;
	}

	public function getVisibility(): ?VisibilityEnum
	{
		return $this->visibility;
	}

	public function private(): static
	{
		return $this->setVisibility(VisibilityEnum::PRIVATE);
	}

	public function protected(): static
	{
		return $this->setVisibility(VisibilityEnum::PROTECTED);
	}

	public function public(): static
	{
		return $this->setVisibility(VisibilityEnum::PUBLIC);
	}

	public function isPrivate(): bool
	{
		return VisibilityEnum::PRIVATE === $this->visibility;
	}

	public function isProtected(): bool
	{
		return VisibilityEnum::PROTECTED === $this->visibility;
	}

	public function isPublic(): bool
	{
		return VisibilityEnum::PUBLIC === $this->visibility;
	}

	abstract protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum;
}
