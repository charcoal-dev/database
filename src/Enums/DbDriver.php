<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Enums;

/**
 * Enum representing database driver types.
 */
enum DbDriver: string
{
    case MYSQL = "mysql";
    case SQLITE = "sqlite";
    case PGSQL = "pgsql";
}
