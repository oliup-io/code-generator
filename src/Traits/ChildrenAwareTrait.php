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
	 * The children, each once.
	 *
	 * Deduplicated by identity. It was `array_unique()`, whose default flag is `SORT_STRING`: every
	 * child was **cast to a string** to be compared, which for these objects means rendering its
	 * whole subtree through {@see CommonTrait::__toString()}, at every level, before the printer's
	 * own walk even starts. `SORT_REGULAR` would be no better -- `==` on objects recurses into their
	 * properties, so a graph holding itself ends in "Nesting level too deep".
	 *
	 * @return object[]
	 */
	public function getChildren(): array
	{
		$unique = [];

		foreach ($this->children as $child) {
			$unique[\spl_object_id($child)] = $child;
		}

		return \array_values($unique);
	}

	/**
	 * @return $this
	 */
	public function addChild(object|string $child): static
	{
		if (\is_string($child)) {
			$child = new PHPRaw($child);
		}

		$this->children[] = $this->validateChild($child);

		return $this;
	}

	public function setContent(object|string $child): static
	{
		if (\is_string($child)) {
			$child = new PHPRaw($child);
		}

		$this->children = [$this->validateChild($child)];

		return $this;
	}

	abstract protected function validateChild(object $child): object;
}
