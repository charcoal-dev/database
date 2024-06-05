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

namespace Charcoal\Database\Queries;

use Charcoal\Database\DbDriver;
use Charcoal\Database\Exception\DbQueryException;

/**
 * Class LockFlag
 * @package Charcoal\Database\Queries
 */
enum LockFlag
{
    case FOR_UPDATE;
    case IN_SHARE_MODE;

    /**
     * @param \Charcoal\Database\DbDriver $driver
     * @return string
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function getQueryPart(DbDriver $driver): string
    {
        if ($driver !== DbDriver::MYSQL) {
            throw new  DbQueryException("LockFlag not implemented/support for " . $driver->name . " driver");
        }

        return match ($this) {
            LockFlag::FOR_UPDATE => "FOR UPDATE",
            LockFlag::IN_SHARE_MODE => "LOCK IN SHARE MODE",
        };
    }
}

