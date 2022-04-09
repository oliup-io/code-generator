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
 * Trait FinalAwareTrait.
 */
trait FinalAwareTrait
{
	protected bool $final = false;

	/**
	 * @return bool
	 */
	public function isFinal(): bool
	{
		return $this->final;
	}

	/**
	 * @param bool $final
	 *
	 * @return $this
	 */
	public function final(bool $final =  true): static
	{
		$this->final = $final;

		return $this;
	}
}
