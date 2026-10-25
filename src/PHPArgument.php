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
use OLIUP\CG\Traits\AttributeAwareTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\ReferenceAwareTrait;
use OLIUP\CG\Traits\TypeAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;
use Override;
use RuntimeException;

/**
 * Class PHPArgument.
 */
class PHPArgument
{
	use AttributeAwareTrait;
	use NameAwareTrait;
	use ReferenceAwareTrait;
	use TypeAwareTrait;
	use ValueAwareTrait;
	use VisibilityAwareTrait;

	protected bool $promoted = false;
	protected bool $variadic = false;

	public function __construct(string $name, PHPType|string|null $type = null)
	{
		$this->setName($name)
			->setType($type);
	}

	public function setPromoted(bool $promoted): static
	{
		if ($promoted && $this->isVariadic()) {
			throw new RuntimeException('Cannot declare variadic promoted property');
		}

		$this->promoted = $promoted;

		return $this;
	}

	public function setVariadic(bool $variadic): static
	{
		if ($variadic && $this->isPromoted()) {
			throw new RuntimeException('Cannot declare variadic promoted property.');
		}
		$this->variadic = $variadic;

		return $this;
	}

	public function isVariadic(): bool
	{
		return $this->variadic;
	}

	public function isPromoted(): bool
	{
		return $this->promoted;
	}

	#[Override]
	protected function validateName(string $name): string
	{
		if (!\preg_match(PHPVar::VAR_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid argument name: ' . $name);
		}

		return $name;
	}

	#[Override]
	protected function validateType(?PHPType $type): ?PHPType
	{
		return $type;
	}

	#[Override]
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		$this->setPromoted(null !== $visibility);

		return $visibility;
	}

	#[Override]
	protected function validateValue(?PHPValue $value): ?PHPValue
	{
		return $value;
	}
}
