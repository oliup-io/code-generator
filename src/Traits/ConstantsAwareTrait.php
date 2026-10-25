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

	public function hasConstant(PHPConstant|string $constant): bool
	{
		return isset($this->constants[\is_string($constant) ? $constant : $constant->getName()]);
	}

	public function getConstant(string $name): ?PHPConstant
	{
		return $this->constants[$name] ?? null;
	}

	/**
	 * @return $this
	 */
	public function addConstant(PHPConstant $constant): static
	{
		$this->constants[$constant->getName()] = $this->validateConstant($constant);

		return $this;
	}

	public function newConstant(string $name, mixed $value): PHPConstant
	{
		$this->addConstant($c = new PHPConstant($name, $value));

		return $c;
	}

	abstract protected function validateConstant(PHPConstant $constant): PHPConstant;
}
