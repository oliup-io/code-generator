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

use OLIUP\CG\Traits\CommonTrait;

/**
 * Class PHPValue.
 */
class PHPValue
{
	use CommonTrait;

	public function __construct(protected mixed $value)
	{
	}

	/**
	 * @return mixed
	 */
	public function getValue(): mixed
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue(mixed $value): void
	{
		$this->value = $value;
	}
}
