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

use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;

/**
 * Class PHPFile.
 */
class PHPFile
{
	use ChildrenAwareTrait;
	use CommentAwareTrait;
	use CommonTrait;

	public function __construct(protected bool $strict = true) {}

	/**
	 * @return bool
	 */
	public function isStrict(): bool
	{
		return $this->strict;
	}

	/**
	 * @param bool $strict
	 */
	public function strict(bool $strict = true): void
	{
		$this->strict = $strict;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function validateChild(object $child): object
	{
		return $child;
	}
}
