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

use OLIUP\CG\Utils\Utils;

/**
 * Trait QualifiedNameAwareTrait.
 */
trait QualifiedNameAwareTrait
{
	use NameAwareTrait;
	use NamespaceAwareTrait;

	public function setName(string $name): static
	{
		Utils::parseQualifiedName($name, $namespace, $short_name);

		$this->name = $this->validateName($short_name);

		$namespace && $this->setNamespace($namespace);

		return $this;
	}

	/**
	 * @param bool $use
	 * @param bool $trait
	 *
	 * @return string
	 */
	public function getFullyQualifiedName(bool $use = false, bool $trait = false): string
	{
		$ns   = $this->getNamespace();
		$name = $this->getName();

		if ($ns) {
			$name = ($ns->isGlobal() ? '\\' : '\\' . $ns->getName() . '\\') . $name;
		}
		if ($use && !$trait) {
			return \ltrim($name, '\\');
		}

		return $name;
	}
}
