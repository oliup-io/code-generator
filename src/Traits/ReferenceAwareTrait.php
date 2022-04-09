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
 * Trait ReferenceAwareTrait.
 */
trait ReferenceAwareTrait
{
	protected bool $by_reference = false;

	/**
	 * @return bool
	 */
	public function isByReference(): bool
	{
		return $this->by_reference;
	}

	/**
	 * @param bool $by_reference
	 */
	public function reference(bool $by_reference = true): void
	{
		$this->by_reference = $by_reference;
	}
}
