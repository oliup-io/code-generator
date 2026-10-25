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
use OLIUP\CG\PHPEnum;
use OLIUP\CG\PHPInterface;
use OLIUP\CG\PHPType;

/**
 * Trait TypeAwareTrait.
 */
trait TypeAwareTrait
{
	protected ?PHPType $type = null;

	/**
	 * @return $this
	 */
	public function setType(PHPClass|PHPEnum|PHPInterface|PHPType|string|null $type): static
	{
		$this->type = (null === $type || $type instanceof PHPType) ? $type : new PHPType($type);

		return $this;
	}

	public function getType(): ?PHPType
	{
		return $this->type;
	}

	abstract protected function validateType(?PHPType $type): ?PHPType;
}
