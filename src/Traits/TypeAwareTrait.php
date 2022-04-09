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

use OLIUP\CG\PHPClass;
use OLIUP\CG\PHPInterface;
use OLIUP\CG\PHPType;

/**
 * Trait TypeAwareTrait.
 */
trait TypeAwareTrait
{
	protected ?PHPType $type = null;

	/**
	 * @param null|PHPClass|PHPInterface|PHPType|string $type
	 *
	 * @return $this
	 */
	public function setType(null|string|PHPType|PHPClass|PHPInterface $type): static
	{
		$this->type = (null === $type || $type instanceof PHPType) ? $type : new PHPType($type);

		return $this;
	}

	/**
	 * @return null|PHPType
	 */
	public function getType(): ?PHPType
	{
		return $this->type;
	}

	/**
	 * @param null|PHPType $type
	 *
	 * @return null|PHPType
	 */
	abstract protected function validateType(?PHPType $type): ?PHPType;
}
