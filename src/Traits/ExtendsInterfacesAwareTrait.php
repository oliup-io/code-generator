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

use OLIUP\CG\PHPInterface;

/**
 * Trait ExtendsInterfacesAwareTrait.
 */
trait ExtendsInterfacesAwareTrait
{
	/** @var PHPInterface[] */
	protected array $extends = [];

	/**
	 * @return PHPInterface[]
	 */
	public function getExtends(): array
	{
		return $this->extends;
	}

	/**
	 * @param PHPInterface|string $interface
	 *
	 * @return bool
	 */
	public function hasInterface(PHPInterface|string $interface): bool
	{
		return isset($this->extends[\is_string($interface) ? $interface : $interface->getFullyQualifiedName()]);
	}

	/**
	 * @param string $name
	 *
	 * @return ?PHPInterface
	 */
	public function getInterface(string $name): ?PHPInterface
	{
		return $this->extends[$name] ?? null;
	}

	/**
	 * @param PHPInterface|string $interface
	 *
	 * @return $this
	 */
	public function extends(PHPInterface|string $interface): static
	{
		if (\is_string($interface)) {
			$interface = new PHPInterface($interface);
		}

		$this->extends[$interface->getName()] = $this->validateExtendsInterface($interface);

		return $this;
	}

	/**
	 * @param PHPInterface $interface
	 *
	 * @return PHPInterface
	 */
	abstract protected function validateExtendsInterface(PHPInterface $interface): PHPInterface;
}
