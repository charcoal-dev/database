<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Enums;

/**
 * Class DbConnectionStrategy
 * @package Charcoal\Database\Enums
 */
enum DbConnectionStrategy
{
    case Normal;
    case Lazy;
    case Persistent;
}