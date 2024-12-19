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
 * Trait ImplementsAwareTrait.
 */
trait ImplementsAwareTrait
{
	/** @var PHPInterface[] */
	protected array $implements = [];

	/**
	 * @return PHPInterface[]
	 */
	public function getImplements(): array
	{
		return $this->implements;
	}

	/**
	 * @param PHPInterface|string $interface
	 *
	 * @return bool
	 */
	public function hasInterface(PHPInterface|string $interface): bool
	{
		return isset($this->implements[\is_string($interface) ? $interface : $interface->getFullyQualifiedName()]);
	}

	/**
	 * @param string $name
	 *
	 * @return ?PHPInterface
	 */
	public function getInterface(string $name): ?PHPInterface
	{
		return $this->implements[$name] ?? null;
	}

	/**
	 * @param PHPInterface|string $interface
	 *
	 * @return $this
	 */
	public function implements(PHPInterface|string $interface): static
	{
		if (\is_string($interface)) {
			$interface = new PHPInterface($interface);
		}

		$this->implements[$interface->getName()] = $this->validateImplements($interface);

		return $this;
	}

	/**
	 * @param PHPInterface $interface
	 *
	 * @return PHPInterface
	 */
	abstract protected function validateImplements(PHPInterface $interface): PHPInterface;
}
