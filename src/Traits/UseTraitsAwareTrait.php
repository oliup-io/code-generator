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

use OLIUP\CG\PHPTrait;
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
	 * @param \OLIUP\CG\PHPTrait|\OLIUP\CG\PHPUseTrait|string $trait
	 *
	 * @return $this
	 */
	public function useTrait(string|PHPTrait|PHPUseTrait $trait): static
	{
		if (\is_string($trait)) {
			$trait = new PHPUseTrait(new PHPTrait($trait));
		} elseif ($trait instanceof PHPTrait) {
			$trait = new PHPUseTrait($trait);
		}

		$this->used_traits[$trait->getName()] = $this->validateUseTrait($trait);

		return $this;
	}

	/**
	 * @param PHPUseTrait $use_trait
	 *
	 * @return PHPUseTrait
	 */
	abstract protected function validateUseTrait(PHPUseTrait $use_trait): PHPUseTrait;
}
