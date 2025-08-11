<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database;

/**
 * Class PdoError
 * @package Charcoal\Database
 */
readonly class PdoError implements \Stringable
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

    public function __toString(): string
    {
        return sprintf("[%s][%s] %s", $this->sqlState, $this->code, $this->info);
    }
}
