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
use OLIUP\CG\Traits\QualifiedNameAwareTrait;
use OLIUP\CG\Traits\TypeAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use RuntimeException;

/**
 * Class PHPEnum.
 */
class PHPEnum
{
	use CommentAwareTrait;
	use CommonTrait;
	use QualifiedNameAwareTrait;
	use TypeAwareTrait;
	use ValidateAwareTrait;

	public function __construct(string $name = '')
	{
		$this->setName($name);
	}

	/**
	 * @return bool
	 */
	public function isBacked(): bool
	{
		return null !== $this->type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): void {}

	/**
	 * {@inheritDoc}
	 */
	protected function validateName(string $name): string
	{
		if (!\preg_match(PHPClass::CLASS_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException(\sprintf('Invalid enum name: %s', $name));
		}

		return $name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateType(?PHPType $type): ?PHPType
	{
		$t_str = $type ? (string) $type : '';

		if (!$type || 'string' === $t_str || 'int' === $t_str) {
			return $type;
		}

		throw new RuntimeException('backed enum type should be: string or int.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateNamespace(?PHPNamespace $namespace): ?PHPNamespace
	{
		return $namespace;
	}
}
