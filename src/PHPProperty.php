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
use OLIUP\CG\Enums\PropertyHookKindEnum;
use OLIUP\CG\Enums\VisibilityEnum;
use OLIUP\CG\Traits\AttributeAwareTrait;
use OLIUP\CG\Traits\CommentAwareTrait;
use OLIUP\CG\Traits\CommonTrait;
use OLIUP\CG\Traits\NameAwareTrait;
use OLIUP\CG\Traits\StaticAwareTrait;
use OLIUP\CG\Traits\TypeAwareTrait;
use OLIUP\CG\Traits\ValidateAwareTrait;
use OLIUP\CG\Traits\ValueAwareTrait;
use OLIUP\CG\Traits\VisibilityAwareTrait;
use Override;
use RuntimeException;

/**
 * Class PHPConstant.
 */
class PHPProperty
{
	use AttributeAwareTrait;
	use CommentAwareTrait;
	use CommonTrait;
	use NameAwareTrait;
	use StaticAwareTrait;
	use TypeAwareTrait;
	use ValidateAwareTrait;
	use ValueAwareTrait;
	use VisibilityAwareTrait;

	public const string PROPERTY_NAME_PATTERN = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';

	/**
	 * Keyed by hook kind, so setting one kind twice replaces it rather than printing it twice.
	 *
	 * @var array<string, PHPPropertyHook>
	 */
	protected array $hooks = [];

	public function __construct(string $name, ?PHPValue $value = null)
	{
		$this->setName($name);
		if (null !== $value) {
			$this->setValue($value);
		}
	}

	/**
	 * @return PHPPropertyHook[]
	 */
	public function getHooks(): array
	{
		return \array_values($this->hooks);
	}

	public function hasHooks(): bool
	{
		return !empty($this->hooks);
	}

	public function getHook(PropertyHookKindEnum $kind): ?PHPPropertyHook
	{
		return $this->hooks[$kind->value] ?? null;
	}

	/**
	 * @return $this
	 */
	public function addHook(PHPPropertyHook $hook): static
	{
		$this->hooks[$hook->getKind()->value] = $hook;

		return $this;
	}

	public function newHook(PropertyHookKindEnum $kind): PHPPropertyHook
	{
		$this->addHook($hook = new PHPPropertyHook($kind));

		return $hook;
	}

	public function newGetHook(): PHPPropertyHook
	{
		return $this->newHook(PropertyHookKindEnum::GET);
	}

	public function newSetHook(): PHPPropertyHook
	{
		return $this->newHook(PropertyHookKindEnum::SET);
	}

	#[Override]
	public function validate(): void
	{
		if ($this->isStatic() && $this->hasHooks()) {
			throw new RuntimeException('a static property cannot have hooks.');
		}
	}

	#[Override]
	protected function validateName(string $name): string
	{
		if (!\preg_match(self::PROPERTY_NAME_PATTERN, $name)) {
			throw new InvalidArgumentException('Invalid property name: ' . $name);
		}

		return $name;
	}

	#[Override]
	protected function validateVisibility(?VisibilityEnum $visibility): ?VisibilityEnum
	{
		return $visibility;
	}

	#[Override]
	protected function validateValue(?PHPValue $value): ?PHPValue
	{
		return $value;
	}

	#[Override]
	protected function validateType(?PHPType $type): ?PHPType
	{
		return $type;
	}
}
