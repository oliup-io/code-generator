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

use OLIUP\CG\Traits\CommonTrait;

/**
 * Class PHPAttribute.
 *
 * Represents a PHP 8 attribute: #[Name(arg1, arg2, ...)]
 */
class PHPAttribute
{
	use CommonTrait;

	/** @var string[] */
	protected array $arguments = [];

	public function __construct(protected string $name, string ...$arguments)
	{
		\array_push($this->arguments, ...$arguments);
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return static
	 */
	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @param string $argument
	 *
	 * @return static
	 */
	public function addArgument(string $argument): static
	{
		$this->arguments[] = $argument;

		return $this;
	}
}
