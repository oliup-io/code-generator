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

namespace OLIUP\CG\Utils;

/**
 * Class Utils.
 */
class Utils
{
	/**
	 * Parse a qualified name into namespace and short name.
	 *
	 * @param string      $fqn_name
	 * @param null|string $namespace
	 * @param null|string $short_name
	 */
	public static function parseQualifiedName(string $fqn_name, ?string &$namespace = null, ?string &$short_name = null): void
	{
		$pos        = \strrpos($fqn_name, '\\');
		$namespace  = $pos ? \substr($fqn_name, 0, $pos) : '';
		$short_name = false === $pos ? $fqn_name : \substr($fqn_name, $pos + 1);
	}
}
