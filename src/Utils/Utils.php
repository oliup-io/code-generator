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

use RuntimeException;

/**
 * Class Utils.
 */
class Utils
{
	public static function validateArgName(string $name): string
	{
		return $name;
	}

	public static function validateConstantName(string $name): string
	{
		return $name;
	}

	public static function validatePropertyName(string $name): string
	{
		return $name;
	}

	public static function validateMethodName(string $name): string
	{
		if (!empty($name)) {
			return $name;
		}

		throw new RuntimeException('Method name should not be empty.');
	}

	public static function validateNamespace(string $name): string
	{
		return $name;
	}

	public static function validateFunctionName(string $name): string
	{
		return $name;
	}

	/**
	 * @param string      $name
	 * @param null|string $namespace
	 * @param null|string $short_name
	 */
	public static function parseQualifiedName(string $name, ?string &$namespace = null, ?string &$short_name = null): void
	{
		$pos        = \strrpos($name, '\\');
		$namespace  = $pos ? \substr($name, 0, $pos) : '';
		$short_name = false === $pos ? $name : \substr($name, $pos + 1);
	}
}
