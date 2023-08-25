<?php
/*
 * This file is a part of "charcoal-dev/database" package.
 * https://github.com/charcoal-dev/database
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/database/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Database;

/**
 * Class DbDriver
 * @package Charcoal\Database
 */
enum DbDriver: string
{
    case MYSQL = "mysql";
    case SQLITE = "sqlite";
    case PGSQL = "pgsql";
}
