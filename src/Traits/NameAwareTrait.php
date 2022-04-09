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
 * Trait NameAwareTrait.
 */
trait NameAwareTrait
{
	protected string $name = '';

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName(string $name): static
	{
		$this->name = $this->validateName($name);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	abstract protected function validateName(string $name): string;
}
