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

use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;

/**
 * Class PHPUseTrait.
 */
class PHPUseTrait
{
	use CommentAwareTrait;
	use CommonTrait;

	/**
	 * @var PHPUseTraitMethodRule[]
	 */
	protected array $rules = [];

	public function __construct(protected PHPTrait $trait)
	{
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->trait->getName();
	}

	/**
	 * @return PHPTrait
	 */
	public function getTrait(): PHPTrait
	{
		return $this->trait;
	}

	/**
	 * @param PHPUseTraitMethodRule $rule
	 *
	 * @return $this
	 */
	public function addRule(PHPUseTraitMethodRule $rule): static
	{
		$this->rules[] = $rule;

		return $this;
	}

	/**
	 * @return PHPUseTraitMethodRule[]
	 */
	public function getRules(): array
	{
		return $this->rules;
	}
}
