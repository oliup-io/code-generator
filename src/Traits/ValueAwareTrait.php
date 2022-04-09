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

use OLIUP\CG\PHPValue;

/**
 * Trait ValueAwareTrait.
 */
trait ValueAwareTrait
{
	protected ?PHPValue $value = null;

	/**
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setValue(mixed $value): static
	{
		if (!($value instanceof PHPValue)) {
			$value = new PHPValue($value);
		}

		$this->value = $this->validateValue($value);

		return $this;
	}

	/**
	 * @return null|PHPValue
	 */
	public function getValue(): ?PHPValue
	{
		return $this->value;
	}

	/**
	 * @param null|PHPValue $value
	 *
	 * @return null|PHPValue
	 */
	abstract protected function validateValue(?PHPValue $value): ?PHPValue;
}
