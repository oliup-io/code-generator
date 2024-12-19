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
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\StaticAwareTrait;
use OLIUP\CG\Traits\TypeAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;

/**
 * Class PHPConstant.
 */
class PHPProperty
{
	use CommentAwareTrait;
	use CommonTrait;
	use NameAwareTrait;
	use StaticAwareTrait;
	use TypeAwareTrait;
	use ValueAwareTrait;
	use VisibilityAwareTrait;

	public const PROPERTY_NAME_PATTERN = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';

	public function __construct(string $name, ?PHPValue $value = null) {}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(self::PROPERTY_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid property name: ' . $name);
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
	protected function validateType(?PHPType $type): ?PHPType
	{
		return $type;
	}
}
