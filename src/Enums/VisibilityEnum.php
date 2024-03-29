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

namespace OLIUP\CG\Enums;

/**
 * Enum VisibilityEnum.
 */
enum VisibilityEnum: string
{
	case PUBLIC =  'public';

	case PRIVATE =  'private';

	case PROTECTED =  'protected';
}
