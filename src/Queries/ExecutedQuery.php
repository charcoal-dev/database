<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Queries;

use Charcoal\Database\Exception\QueryExecuteException;
use Charcoal\Database\PdoError;

/**
 * Class ExecutedQuery
 * @package Charcoal\Database\Queries
 */
readonly class ExecutedQuery
{
    public int $rowsCount;

    /**
     * @throws QueryExecuteException
     */
    public function __construct(
        \PDOStatement $stmt,
        public string $queryStr,
        public array  $boundData = []
    )
    {
        $exec = $stmt->execute();
        if (!$exec || $stmt->errorCode() !== "00000") {
            throw new QueryExecuteException(
                $this->queryStr,
                $this->boundData,
                new PdoError($stmt),
                "Failed to execute prepared PDO statement"
            );
        }

        $this->rowsCount = $stmt->rowCount();
    }
}

