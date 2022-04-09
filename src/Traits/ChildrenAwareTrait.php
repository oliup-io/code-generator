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

use OLIUP\CG\PHPRaw;

/**
 * Trait ChildrenAwareTrait.
 */
trait ChildrenAwareTrait
{
	/** @var object[] */
	protected array $children = [];

	/**
	 * @return object[]
	 */
	public function getChildren(): array
	{
		return \array_unique($this->children);
	}

	/**
	 * @param object|string $child
	 *
	 * @return $this
	 */
	public function addChild(string|object $child): static
	{
		if (\is_string($child)) {
			$child = new PHPRaw($child);
		}

		$this->children[] = $this->validateChild($child);

		return $this;
	}

	/**
	 * @param object|string $child
	 *
	 * @return $this
	 */
	public function setContent(string|object $child): static
	{
		if (\is_string($child)) {
			$child = new PHPRaw($child);
		}

		$this->children = [$this->validateChild($child)];

		return $this;
	}

	/**
	 * @param object $child
	 *
	 * @return object
	 */
	abstract protected function validateChild(object $child): object;
}
