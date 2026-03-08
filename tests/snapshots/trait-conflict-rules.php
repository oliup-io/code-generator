<?php
declare(strict_types=1);

namespace App\Traits;

trait TimestampableTrait
{
	public function getCreatedAt(): string
	{
		return $this->created_at;
	}
}trait LoggableTrait
{
	public function log(): void
	{
		error_log($this->getName());
	}
}class AuditableEntity
{
	use \App\Traits\TimestampableTrait;
	use \App\Traits\LoggableTrait {
		\App\Traits\LoggableTrait::log as public writeLog;
	}
}
