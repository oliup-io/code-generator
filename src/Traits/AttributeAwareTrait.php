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

use OLIUP\CG\PHPAttribute;

/**
 * Trait AttributeAwareTrait.
 */
trait AttributeAwareTrait
{
	/** @var PHPAttribute[] */
	protected array $attributes = [];

	/**
	 * @param PHPAttribute|string $attribute
	 *
	 * @return static
	 */
	public function addAttribute(PHPAttribute|string $attribute): static
	{
		if (\is_string($attribute)) {
			$attribute = new PHPAttribute($attribute);
		}

		$this->attributes[] = $attribute;

		return $this;
	}

	/**
	 * @return PHPAttribute[]
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * Create, register and return a new attribute.
	 *
	 * @param string $name
	 * @param string ...$arguments
	 *
	 * @return PHPAttribute
	 */
	public function newAttribute(string $name, string ...$arguments): PHPAttribute
	{
		$this->addAttribute($attr = new PHPAttribute($name, ...$arguments));

		return $attr;
	}
}
