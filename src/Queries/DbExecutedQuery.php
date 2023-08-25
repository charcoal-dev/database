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

use Charcoal\Database\Exception\QueryExecuteException;
use Charcoal\Database\PdoError;

/**
 * Class DbExecutedQuery
 * @package Charcoal\Database\Queries
 */
class DbExecutedQuery
{
    public readonly int $rowsCount;

    /**
     * @param \PDOStatement $stmt
     * @param string $queryStr
     * @param array $boundData
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     */
    public function __construct(
        \PDOStatement          $stmt,
        public readonly string $queryStr,
        public readonly array  $boundData = []
    )
    {
        $exec = $stmt->execute();
        if (!$exec || $stmt->errorCode() !== "00000") {
            throw new QueryExecuteException(
                $this->queryStr,
                $this->boundData,
                new PdoError($stmt),
                'Failed to execute prepared PDO statement'
            );
        }

        $this->rowsCount = $stmt->rowCount();
    }
}

