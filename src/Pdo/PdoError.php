<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Pdo;

/**
 * Represents an error extracted from a PDO, PDOStatement, or PDOException instance.
 */
final readonly class PdoError
{
    public ?string $sqlState;
    public int|string|null $code;
    public ?string $info;

    public function __construct(\PDO|\PDOStatement|\PDOException $errorObj)
    {
        $errorInfo = $errorObj instanceof \PDOException ? $errorObj->errorInfo : $errorObj->errorInfo();
        $this->sqlState = $errorInfo[0] ?? null;
        $this->code = $errorInfo[1] ?? null;
        $this->info = $errorInfo[2] ?? null;
    }
}
