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

use OLIUP\CG\PHPUseTrait;

/**
 * Trait UseTraitsAwareTrait.
 */
trait UseTraitsAwareTrait
{
	/**
	 * @var PHPUseTrait[]
	 */
	protected array $used_traits = [];

	/**
	 * @return PHPUseTrait[]
	 */
	public function getUsedTraits(): array
	{
		return $this->used_traits;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isUsingTrait(string $name): bool
	{
		return isset($this->used_traits[$name]);
	}

	/**
	 * @param string $name
	 *
	 * @return ?PHPUseTrait
	 */
	public function getUsedTrait(string $name): ?PHPUseTrait
	{
		return $this->used_traits[$name] ?? null;
	}

	/**
	 * @param PHPUseTrait $use_trait
	 *
	 * @return $this
	 */
	public function useTrait(PHPUseTrait $use_trait): static
	{
		$this->used_traits[$use_trait->getName()] = $this->validateUseTrait($use_trait);

		return $this;
	}

	/**
	 * @param PHPUseTrait $use_trait
	 *
	 * @return PHPUseTrait
	 */
	abstract protected function validateUseTrait(PHPUseTrait $use_trait): PHPUseTrait;
}
