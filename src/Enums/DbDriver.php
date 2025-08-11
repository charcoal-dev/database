<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Enums;

/**
 * Class DbDriver
 * @package Charcoal\Database\Enums
 */
enum DbDriver: string
{
    case MYSQL = "mysql";
    case SQLITE = "sqlite";
    case PGSQL = "pgsql";
    case UNKNOWN = "unknown";
}
