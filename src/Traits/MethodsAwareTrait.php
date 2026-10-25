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

use OLIUP\CG\PHPMethod;

/**
 * Trait MethodsAwareTrait.
 */
trait MethodsAwareTrait
{
	/**
	 * @var PHPMethod[]
	 */
	protected array $methods = [];

	/**
	 * @return PHPMethod[]
	 */
	public function getMethods(): array
	{
		return $this->methods;
	}

	public function hasMethod(PHPMethod|string $method): bool
	{
		return isset($this->methods[\is_string($method) ? $method : $method->getName()]);
	}

	public function getMethod(string $name): ?PHPMethod
	{
		return $this->methods[$name] ?? null;
	}

	/**
	 * @return $this
	 */
	public function addMethod(PHPMethod $method): static
	{
		$this->methods[$method->getName()] = $this->validateMethod($method);

		return $this;
	}

	public function newMethod(string $name): PHPMethod
	{
		$this->addMethod($c = new PHPMethod($name));

		return $c;
	}

	abstract protected function validateMethod(PHPMethod $method): PHPMethod;
}
