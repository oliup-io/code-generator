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

use InvalidArgumentException;
use OLIUP\CG\PHPPrinter;
use UnitEnum;

/**
 * Trait CommonTrait.
 */
trait CommonTrait
{
	public function __toString(): string
	{
		return new PHPPrinter()->print($this);
	}

	/**
	 * How deep one `clone` may go before the graph is called cyclic.
	 */
	private const int MAX_CLONE_DEPTH = 64;

	/**
	 * How deep the current `clone` is: a copy cannot be mapped back to its original from inside
	 * `__clone()`, so what is bounded is the depth.
	 */
	private static int $clone_depth = 0;

	public function __clone()
	{
		if (self::$clone_depth >= self::MAX_CLONE_DEPTH) {
			throw new InvalidArgumentException(\sprintf(
				'Cloning "%s" went deeper than %d levels: the graph holds itself.',
				static::class,
				self::MAX_CLONE_DEPTH
			));
		}

		++self::$clone_depth;

		try {
			$this->cloneChildren();
		} finally {
			--self::$clone_depth;
		}
	}

	/**
	 * Clones what this node holds, one level.
	 */
	private function cloneChildren(): void
	{
		// Enum are not cloneable
		$cloneable = static fn ($v) => \is_object($v) && !($v instanceof UnitEnum);

		$vars = \get_object_vars($this);
		foreach ($vars as $key => $_) {
			if (isset($this->{$key})) {
				if (\is_array($this->{$key})) {
					foreach ($this->{$key} as &$value) {
						if ($cloneable($value)) {
							$value = clone $value;
						}
					}
				} elseif ($cloneable($this->{$key})) {
					$this->{$key} = clone $this->{$key};
				}
			}
		}
	}
}
