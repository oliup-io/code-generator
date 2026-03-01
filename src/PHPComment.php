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

	public function __construct(
		protected string $content,
		protected CommentKindEnum $kind = CommentKindEnum::DOC
	) {}

	/**
	 * @param CommentKindEnum $kind
	 *
	 * @return $this
	 */
	public function setKind(CommentKindEnum $kind): static
	{
		$this->kind = $kind;

		return $this;
	}

	/**
	 * @return CommentKindEnum
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

	/**
	 * Create an inline comment.
	 *
	 * @param string $content
	 *
	 * @return self
	 */
	public static function inline(string $content): self
	{
		return new self($content, CommentKindEnum::SLASH);
	}

	/**
	 * Create a hash comment.
	 *
	 * @param string $content
	 *
	 * @return self
	 */
	public static function hash(string $content): self
	{
		return new self($content, CommentKindEnum::HASH);
	}

	/**
	 * Create a multiline comment.
	 *
	 * @param string $content
	 *
	 * @return self
	 */
	public static function multiline(string $content): self
	{
		return new self($content, CommentKindEnum::MULTILINE);
	}

	/**
	 * Create a doc comment.
	 *
	 * @param string $content
	 *
	 * @return self
	 */
	public static function doc(string $content): self
	{
		return new self($content, CommentKindEnum::DOC);
	}
}
