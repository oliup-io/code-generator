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

use OLIUP\CG\PHPComment;

/**
 * Trait CommentAwareTrait.
 */
trait CommentAwareTrait
{
	protected ?PHPComment $comment = null;

	/**
	 * @return $this
	 */
	public function setComment(PHPComment|string|null $comment): static
	{
		$this->comment = \is_string($comment) ? new PHPComment($comment) : $comment;

		return $this;
	}

	public function getComment(): ?PHPComment
	{
		return $this->comment;
	}

	public function comment(string $comment): PHPComment
	{
		$this->setComment($c = new PHPComment($comment));

		return $c;
	}
}
