<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Enums;

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
     * @param DbDriver $driver
     * @return string
     * @throws DbQueryException
     */
    public function getQueryPart(DbDriver $driver): string
    {
        if ($driver !== DbDriver::MYSQL) {
            throw new DbQueryException("LockFlag not implemented/support for " . $driver->name . " driver");
        }

        return match ($this) {
            LockFlag::FOR_UPDATE => "FOR UPDATE",
            LockFlag::IN_SHARE_MODE => "LOCK IN SHARE MODE",
        };
    }
}

