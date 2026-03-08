<?php
declare(strict_types=1);

namespace App\Utils;

class Transformer
{
	public function createTransformer(string $prefix = 'PREFIX_'): \Closure
	{
		return function(string $input) use ($prefix): string
		{
			return $prefix . strtoupper($input);
		};
	}
}
