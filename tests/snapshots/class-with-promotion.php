<?php
/**
 * Copyright (c) OLIUP <dev@oliup.com>.
 * 
 * This file is part of the Oliup CodeGenerator package.
 */
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Identifiable;

/**
 * Class User.
 * 
 * @package App\Models
 */
final class User implements \App\Contracts\Identifiable
{
	const ROLE_ADMIN = 'admin';
	const ROLE_USER = 'user';
	protected string $name;
	public function __construct(public int $id, string $name)
	{
		$this->name = $name;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}
}
