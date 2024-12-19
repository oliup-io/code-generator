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

use OLIUP\CG\PHPConstant;

/**
 * Trait ConstantsAwareTrait.
 */
trait ConstantsAwareTrait
{
	/**
	 * @var PHPConstant[]
	 */
	protected array $constants = [];

	/**
	 * @return PHPConstant[]
	 */
	public function getConstants(): array
	{
		return $this->constants;
	}

	/**
	 * @param PHPConstant|string $constant
	 *
	 * @return bool
	 */
	public function hasConstant(PHPConstant|string $constant): bool
	{
		return isset($this->constants[\is_string($constant) ? $constant : $constant->getName()]);
	}

	/**
	 * @param string $name
	 *
	 * @return ?PHPConstant
	 */
	public function getConstant(string $name): ?PHPConstant
	{
		return $this->constants[$name] ?? null;
	}

	/**
	 * @param PHPConstant $constant
	 *
	 * @return $this
	 */
	public function addConstant(PHPConstant $constant): static
	{
		$this->constants[$constant->getName()] = $this->validateConstant($constant);

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return PHPConstant
	 */
	public function newConstant(string $name, mixed $value): PHPConstant
	{
		$this->addConstant($c = new PHPConstant($name, $value));

		return $c;
	}

	/**
	 * @param PHPConstant $constant
	 *
	 * @return PHPConstant
	 */
	abstract protected function validateConstant(PHPConstant $constant): PHPConstant;
}
