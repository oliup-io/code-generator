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

use InvalidArgumentException;
use OLIUP\CG\Enums\VisibilityEnum;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;
use RuntimeException;

/**
 * Class PHPConstant.
 */
class PHPConstant
{
	use CommentAwareTrait;
	use CommonTrait;
	use QualifiedNameAwareTrait;
	use ValidateAwareTrait;
	use ValueAwareTrait;
	use VisibilityAwareTrait;

	public const CONSTANT_NAME_PATTERN = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';

	public function __construct(string $name, mixed $value = null)
	{
		$this->setName($name);
		$this->setValue($value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void
	{
		if (null === $this->getValue()) {
			throw new RuntimeException('a constant should have a value.');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(self::CONSTANT_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid constant name: ' . $name);
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		return $visibility;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateValue(?PHPValue $value): ?PHPValue
	{
		return $value;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateNamespace(?PHPNamespace $namespace): ?PHPNamespace
	{
		return $namespace;
	}
}
