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
 * Class PdoError
 * @package Charcoal\Database
 */
class PdoError
{
    /** @var string|null */
    public readonly ?string $sqlState;
    /** @var int|string|null */
    public readonly int|string|null $code;
    /** @var string|null */
    public readonly ?string $info;

    /**
     * @param \PDO|\PDOStatement|\PDOException $errorObj
     */
    public function __construct(\PDO|\PDOStatement|\PDOException $errorObj)
    {
        $errorInfo = $errorObj instanceof \PDOException ? $errorObj->errorInfo : $errorObj->errorInfo();
        $this->sqlState = $errorInfo[0] ?? null;
        $this->code = $errorInfo[1] ?? null;
        $this->info = $errorInfo[2] ?? null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('[%s][%s] %s', $this->sqlState, $this->code, $this->info);
    }
}
