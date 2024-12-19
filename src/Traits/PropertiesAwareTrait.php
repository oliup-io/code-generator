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

use OLIUP\CG\PHPProperty;

/**
 * Trait PropertiesAwareTrait.
 */
trait PropertiesAwareTrait
{
	/**
	 * @var PHPProperty[]
	 */
	protected array $properties = [];

	/**
	 * @return PHPProperty[]
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}

	/**
	 * @param PHPProperty|string $property
	 *
	 * @return bool
	 */
	public function hasProperty(PHPProperty|string $property): bool
	{
		return isset($this->properties[\is_string($property) ? $property : $property->getName()]);
	}

	/**
	 * @param string $name
	 *
	 * @return ?PHPProperty
	 */
	public function getProperty(string $name): ?PHPProperty
	{
		return $this->properties[$name] ?? null;
	}

	/**
	 * @param PHPProperty $property
	 *
	 * @return $this
	 */
	public function addProperty(PHPProperty $property): static
	{
		$this->properties[$property->getName()] = $this->validateProperty($property);

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return PHPProperty
	 */
	public function newProperty(string $name): PHPProperty
	{
		$this->addProperty($c = new PHPProperty($name));

		return $c;
	}

	/**
	 * @param PHPProperty $property
	 *
	 * @return PHPProperty
	 */
	abstract protected function validateProperty(PHPProperty $property): PHPProperty;
}
