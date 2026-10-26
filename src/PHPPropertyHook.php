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

use OLIUP\CG\Enums\PropertyHookKindEnum;
use OLIUP\CG\Traits\ArgumentsAwareTrait;
use OLIUP\CG\Traits\ChildrenAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\FinalAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use Override;
use RuntimeException;

/**
 * Class PHPPropertyHook.
 *
 * A `get` or `set` hook of a property, in one of the two body forms PHP accepts: an expression
 * (`get => $this->x;`) or a block of statements. A `set` hook may name its own parameter, whose
 * type PHP requires to be a supertype of the property type; with none it receives `$value`.
 */
class PHPPropertyHook
{
	use ArgumentsAwareTrait;
	use ChildrenAwareTrait;
	use CommonTrait;
	use FinalAwareTrait;
	use ValidateAwareTrait;

	protected ?string $expression = null;

	public function __construct(protected PropertyHookKindEnum $kind) {}

	public function getKind(): PropertyHookKindEnum
	{
		return $this->kind;
	}

	/**
	 * Sets the body to a single expression, printed as `=> $expression;`.
	 *
	 * @return $this
	 */
	public function setExpression(?string $expression): static
	{
		$this->expression = $expression;

		return $this;
	}

	public function getExpression(): ?string
	{
		return $this->expression;
	}

	#[Override]
	public function validate(): void
	{
		if (PropertyHookKindEnum::GET === $this->kind && !empty($this->arguments)) {
			throw new RuntimeException('a get hook takes no parameter.');
		}

		if (\count($this->arguments) > 1) {
			throw new RuntimeException('a set hook takes a single parameter.');
		}

		$has_expression = null !== $this->expression;
		$has_block      = !empty($this->children);

		if ($has_expression === $has_block) {
			throw new RuntimeException(
				\sprintf(
					'the %s hook needs exactly one body: an expression or a block, not %s.',
					$this->kind->value,
					$has_expression ? 'both' : 'neither'
				)
			);
		}
	}

	#[Override]
	protected function validateArgument(PHPArgument $argument): PHPArgument
	{
		return $argument;
	}

	#[Override]
	protected function validateChild(object $child): object
	{
		return $child;
	}
}
