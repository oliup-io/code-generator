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
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\ReferenceAwareTrait;
use OLIUP\CG\Traits\TypeAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;
use RuntimeException;

/**
 * Class PHPArgument.
 */
class PHPArgument
{
	use NameAwareTrait;
	use ReferenceAwareTrait;
	use TypeAwareTrait;
	use ValueAwareTrait;
	use VisibilityAwareTrait;

	protected bool $promoted      = false;
	protected bool $variadic      = false;

	public function __construct(string $name, null|PHPType|string $type = null)
	{
		$this->setName($name)
			->setType($type);
	}

	/**
	 * @param bool $promoted
	 */
	public function setPromoted(bool $promoted): void
	{
		if ($promoted && $this->isVariadic()) {
			throw new RuntimeException('Cannot declare variadic promoted property');
		}

		$this->promoted = $promoted;
	}

	/**
	 * @param bool $variadic
	 */
	public function setVariadic(bool $variadic): void
	{
		if ($variadic && $this->isPromoted()) {
			throw new RuntimeException('Cannot declare variadic promoted property.');
		}
		$this->variadic = $variadic;
	}

	/**
	 * @return bool
	 */
	public function isVariadic(): bool
	{
		return $this->variadic;
	}

	/**
	 * @return bool
	 */
	public function isPromoted(): bool
	{
		return $this->promoted;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(PHPVar::VAR_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid argument name: ' . $name);
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateType(?PHPType $type): ?PHPType
	{
		return $type;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		$this->setPromoted(null !== $visibility);

		return $visibility;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateValue(?PHPValue $value): ?PHPValue
	{
		return $value;
	}
}
