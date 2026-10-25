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

use OLIUP\CG\PHPPrinter;
use UnitEnum;

/**
 * Trait CommonTrait.
 */
trait CommonTrait
{
	public function __toString(): string
	{
		return (new PHPPrinter())->print($this);
	}

	public function __clone()
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
