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

use Charcoal\Database\Exception\QueryExecuteException;
use Charcoal\Database\Queries\DbExecutedQuery;
use Charcoal\Database\Queries\DbFetchQuery;
use Charcoal\Database\Queries\QueryArchive;
use Charcoal\Database\Queries\QueryBuilder;
use Charcoal\OOP\Traits\NotCloneableTrait;
use Charcoal\OOP\Traits\NotSerializableTrait;

/**
 * Class Database
 * @package Charcoal\Database
 */
class Database extends PdoAdapter
{
    public readonly QueryArchive $queries;

    use NotSerializableTrait;
    use NotCloneableTrait;

    /**
     * @param \Charcoal\Database\DbCredentials $credentials
     * @param int $errorMode
     * @throws \Charcoal\Database\Exception\DbConnectionException
     */
    public function __construct(DbCredentials $credentials, int $errorMode = \PDO::ERRMODE_EXCEPTION)
    {
        parent::__construct($credentials, $errorMode);
        $this->queries = new QueryArchive();
    }

    /**
     * @return \Charcoal\Database\Queries\QueryBuilder
     */
    public function queryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    /**
     * @param string $query
     * @param array $data
     * @return \Charcoal\Database\Queries\DbExecutedQuery
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     */
    public function exec(string $query, array $data = []): DbExecutedQuery
    {
        return $this->queryExec($query, $data, false);
    }

    /**
     * @param string $query
     * @param array $data
     * @return \Charcoal\Database\Queries\DbFetchQuery
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     */
    public function fetch(string $query, array $data = []): DbFetchQuery
    {
        return $this->queryExec($query, $data, true);
    }

    /**
     * @param string $queryStr
     * @param array $data
     * @param bool $fetchQuery
     * @return \Charcoal\Database\Queries\DbExecutedQuery|\Charcoal\Database\Queries\DbFetchQuery
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     */
    private function queryExec(string $queryStr, array $data, bool $fetchQuery): DbExecutedQuery|DbFetchQuery
    {
        try {
            $stmt = $this->queryPrepareStatement($queryStr);
            $this->queryBindParams($stmt, $queryStr, $data);
            $query = new DbExecutedQuery($stmt, $queryStr, $data);
        } catch (\PDOException $e) {
            throw new QueryExecuteException($queryStr, $data, new PdoError($e), $e->getMessage(), $e->getCode(), $e);
        }

        $this->queries->append($query);
        return $fetchQuery ? new DbFetchQuery($query, $stmt) : $query;
    }

    /**
     * @param string $queryStr
     * @return \PDOStatement
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     * @throws \PDOException
     */
    private function queryPrepareStatement(string $queryStr): \PDOStatement
    {
        $stmt = $this->pdo->prepare($queryStr);
        if (!$stmt) {
            throw new QueryExecuteException($queryStr, [], new PdoError($stmt), 'Failed to prepare PDO statement');
        }

        return $stmt;
    }

    /**
     * @param \PDOStatement $stmt
     * @param string $queryStr
     * @param array $data
     * @return void
     * @throws \Charcoal\Database\Exception\QueryExecuteException
     * @throws \PDOException
     */
    private function queryBindParams(\PDOStatement $stmt, string $queryStr, array $data): void
    {
        try {
            // Bind params
            foreach ($data as $key => $value) {
                $type = match (gettype($value)) {
                    "boolean" => \PDO::PARAM_BOOL,
                    "integer" => \PDO::PARAM_INT,
                    "NULL" => \PDO::PARAM_NULL,
                    "string", "double" => \PDO::PARAM_STR,
                    default => throw new \LogicException('Cannot bind value of type ' . gettype($value))
                };

                if (is_int($key)) {
                    $key++; // Indexed arrays get +1 to numeric keys so that they don't start with 0
                }

                if (!$stmt->bindValue($key, $value, $type)) {
                    throw new \LogicException(
                        sprintf('Failed to bind value of type "%s" to key "%s"', gettype($value), $key)
                    );
                }
            }
        } catch (\LogicException $e) {
            throw new QueryExecuteException($queryStr, $data, new PdoError($stmt), $e->getMessage());
        }
    }
}

