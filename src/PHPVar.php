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
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\ReferenceAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;

/**
 * Class PHPVarName.
 */
class PHPVar
{
	use CommentAwareTrait;
	use CommonTrait;
	use NameAwareTrait;
	use ReferenceAwareTrait;
	use ValueAwareTrait;

	public const VAR_NAME_PATTERN = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';

	public function __construct(string $name = '')
	{
		$this->setName($name);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(self::VAR_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid var name: ' . $name);
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateValue(?PHPValue $value): ?PHPValue
	{
		return $value;
	}
}
