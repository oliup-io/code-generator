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
 * Class PHPType.
 */
class PHPType
{
	use CommonTrait;

	/** @var array<string, PHPClass|PHPEnum|PHPInterface|PHPTypeIntersection|string> */
	private array $types = [];

	public function __construct(string|PHPInterface|PHPClass|PHPEnum|PHPTypeIntersection ...$types)
	{
		$this->add(...$types);
	}

	/**
	 * @param \OLIUP\CG\PHPClass|\OLIUP\CG\PHPEnum|\OLIUP\CG\PHPInterface ...$types
	 *
	 * @return PHPTypeIntersection
	 */
	public static function intersection(PHPInterface|PHPEnum|PHPClass ...$types): PHPTypeIntersection
	{
		return new PHPTypeIntersection(...$types);
	}

	/**
	 * @return array<string, PHPClass|PHPEnum|PHPInterface|PHPTypeIntersection|string>
	 */
	public function getTypes(): array
	{
		return $this->types;
	}

	/**
	 * @param bool $nullable
	 *
	 * @return $this
	 */
	public function nullable(bool $nullable = true): static
	{
		if ($nullable) {
			$this->add('null');
		} else {
			unset($this->types['null']);
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return isset($this->types['null']);
	}

	/**
	 * @param \OLIUP\CG\PHPClass|\OLIUP\CG\PHPEnum|\OLIUP\CG\PHPInterface|\OLIUP\CG\PHPTypeIntersection|string ...$types
	 *
	 * @return $this
	 */
	public function add(string|PHPInterface|PHPEnum|PHPClass|PHPTypeIntersection ...$types): static
	{
		foreach ($types as $type) {
			if (!empty($type)) {
				$type = '?' === $type ? 'null' : $type;

				if (($type instanceof PHPClass) && $type->isAnonymous()) {
					throw new RuntimeException('an anonymous class cannot be used as type.');
				}

				if (\is_string($type)) {
					$key = $type;
				} elseif ($type instanceof PHPTypeIntersection) {
					$key = $type->getName();
				} else {
					$key = $type->getFullyQualifiedName();
				}

				$this->types[$key] = $type;
			}
		}

		return $this;
	}

	/**
	 * @param \OLIUP\CG\PHPClass|\OLIUP\CG\PHPEnum|\OLIUP\CG\PHPInterface|\OLIUP\CG\PHPTypeIntersection|string $type
	 *
	 * @return bool
	 */
	public function isAllowed(string|PHPInterface|PHPClass|PHPEnum|PHPTypeIntersection $type): bool
	{
		if (\is_string($type)) {
			$key = $type;
		} elseif ($type instanceof PHPTypeIntersection) {
			$key = $type->getName();
		} else {
			$key = $type->getFullyQualifiedName();
		}

		return isset($this->types[$key]);
	}
}
