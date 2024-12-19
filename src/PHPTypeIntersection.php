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

namespace OLIUP\CG;

use OLIUP\CG\Traits\CommonTrait;
use RuntimeException;

/**
 * Class PHPTypeIntersection.
 *
 * @internal
 */
final class PHPTypeIntersection
{
	use CommonTrait;

	/** @var array<string, PHPClass|PHPEnum|PHPInterface> */
	private array $types = [];
	private string $name;

	public function __construct(PHPClass|PHPEnum|PHPInterface ...$types)
	{
		foreach ($types as $type) {
			if (($type instanceof PHPClass) && $type->isAnonymous()) {
				throw new RuntimeException('an anonymous class cannot be used as type.');
			}

			$key = $type->getFullyQualifiedName();

			$this->types[$key] = $type;
		}

		$names = \array_keys($this->types);

		\sort($names);

		$this->name = \implode('&', $names);
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array<string, PHPClass|PHPEnum|PHPInterface>
	 */
	public function getTypes(): array
	{
		return $this->types;
	}
}
