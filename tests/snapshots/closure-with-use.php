<?php
declare(strict_types=1);

namespace App\Utils;

function(string $input) use ($prefix = 'PREFIX_';): string
{
	return $prefix . strtoupper($input);
}
