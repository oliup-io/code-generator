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

use OLIUP\CG\PHPArgument;

/**
 * Trait ArgumentsAwareTrait.
 */
trait ArgumentsAwareTrait
{
	/** @var PHPArgument[] */
	protected array $arguments = [];

	/**
	 * @return PHPArgument[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @param string $name
	 *
	 * @return null|PHPArgument
	 */
	public function getArgument(string $name): ?PHPArgument
	{
		return $this->arguments[$name] ?? null;
	}

	/**
	 * @param PHPArgument $argument
	 *
	 * @return $this
	 */
	public function addArgument(PHPArgument $argument): static
	{
		$this->arguments[$argument->getName()] = $this->validateArgument($argument);

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return PHPArgument
	 */
	public function newArgument(string $name): PHPArgument
	{
		$this->addArgument($arg = new PHPArgument($name));

		return $arg;
	}

	/**
	 * @param \OLIUP\CG\PHPArgument $argument
	 *
	 * @return \OLIUP\CG\PHPArgument
	 */
	abstract protected function validateArgument(PHPArgument $argument): PHPArgument;
}
