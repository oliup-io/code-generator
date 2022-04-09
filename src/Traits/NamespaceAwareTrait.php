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

use OLIUP\CG\PHPNamespace;

/**
 * Trait NamespaceAwareTrait.
 */
trait NamespaceAwareTrait
{
	protected ?PHPNamespace $namespace = null;

	/**
	 * @return null|PHPNamespace
	 */
	public function getNamespace(): ?PHPNamespace
	{
		return $this->namespace;
	}

	/**
	 * @param null|PHPNamespace|string $namespace
	 *
	 * @return $this
	 */
	public function setNamespace(null|string|PHPNamespace $namespace): static
	{
		if (\is_string($namespace)) {
			$namespace  = new PHPNamespace($namespace);
		}

		$this->namespace = $this->validateNamespace($namespace);

		return $this;
	}

	/**
	 * @param null|PHPNamespace $namespace
	 *
	 * @return null|PHPNamespace
	 */
	abstract protected function validateNamespace(?PHPNamespace $namespace): ?PHPNamespace;
}
