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

/**
 * Trait StaticAwareTrait.
 */
trait StaticAwareTrait
{
	protected bool $static = false;

	/**
	 * @return bool
	 */
	public function isStatic(): bool
	{
		return $this->static;
	}

	/**
	 * @param bool $static
	 *
	 * @return $this
	 */
	public function static(bool $static =  true): static
	{
		$this->static = $static;

		return $this;
	}
}
