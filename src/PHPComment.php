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

use OLIUP\CG\Enums\CommentKindEnum;
use OLIUP\CG\Traits\CommonTrait;

/**
 * Class PHPComment.
 */
class PHPComment
{
	use CommonTrait;

	protected CommentKindEnum $kind = CommentKindEnum::DOC;

	public function __construct(protected string $content)
	{
	}

	/**
	 * @param \OLIUP\CG\Enums\CommentKindEnum $kind
	 *
	 * @return $this
	 */
	public function setKind(CommentKindEnum $kind): static
	{
		$this->kind = $kind;

		return $this;
	}

	/**
	 * @return \OLIUP\CG\Enums\CommentKindEnum
	 */
	public function getKind(): CommentKindEnum
	{
		return $this->kind;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 *
	 * @return $this
	 */
	public function setContent(string $content): static
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * @param string $lines
	 *
	 * @return $this
	 */
	public function addLines(string $lines): static
	{
		$this->content .= \PHP_EOL . $lines;

		return $this;
	}
}
