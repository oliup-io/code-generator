<?php
declare(strict_types=1);

namespace App\Http;

#[Route\Controller]
#[Authorize]
class UserController
{
	#[Deprecated]
	const LIMIT = 100;
	#[Inject]
	public int $count = 0;
	#[Get('/users/{id}')]
	public function show(#[MapEntity] int $id, string $format = 'json'): mixed
	{
		return null;
	}
}
