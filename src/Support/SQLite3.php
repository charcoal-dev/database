<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Support;

use Charcoal\Database\DatabaseClient;
use Charcoal\Database\Enums\DbDriver;
use Charcoal\Database\Exceptions\DbConnectionException;

/**
 * Helpers for SQLite3
 */
abstract readonly class SQLite3
{
    /**
     * Enable REGEXP_LIKE function for SQLite3
     * @throws DbConnectionException
     */
    public static function enableRegExpLike(DatabaseClient $db): void
    {
        if ($db->credentials->driver !== DbDriver::SQLITE) {
            throw new \BadMethodCallException("SQLite3 only supported for SQLite driver");
        }

        $db->pdoAdapter()->sqliteCreateFunction("REGEXP_LIKE",
            function (string $input, string $pattern, string $flags = ""): int {
                return preg_match("/" . $pattern . "/u" . $flags, $input) ? 1 : 0;
            }, 3);
    }
}