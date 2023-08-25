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

use Charcoal\Database\Database;
use Charcoal\Database\Exception\QueryBuilderException;

/**
 * Class QueryBuilder
 * @package Charcoal\Database\Queries
 */
class QueryBuilder
{
    /** @var string */
    private string $tableName = "";
    /** @var string */
    private string $whereClause = "1";
    /** @var string */
    private string $selectColumns = "*";
    /** @var bool */
    private bool $selectLock = false;
    /** @var string */
    private string $selectOrder = "";
    /** @var int|null */
    private ?int $selectStart = null;
    /** @var int|null */
    private ?int $selectLimit = null;
    /** @var array */
    private array $queryData = [];

    /**
     * @param \Charcoal\Database\Database $db
     */
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * @param array $assoc
     * @return \Charcoal\Database\Queries\DbExecutedQuery
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function insert(array $assoc): DbExecutedQuery
    {
        $query = sprintf("INSERT INTO `%s`", $this->tableName);
        $cols = [];
        $params = [];

        // Process data
        foreach ($assoc as $key => $value) {
            if (!is_string($key)) {
                throw new QueryBuilderException('INSERT query cannot accept indexed array');
            }

            $cols[] = sprintf('`%s`', $key);
            $params[] = sprintf(':%s', $key);
        }

        // Complete Query
        $query .= sprintf(' (%s) VALUES (%s)', implode(",", $cols), implode(",", $params));

        // Execute
        return $this->db->exec($query, $assoc);
    }

    /**
     * @param array $assoc
     * @return \Charcoal\Database\Queries\DbExecutedQuery
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function update(array $assoc): DbExecutedQuery
    {
        $query = sprintf('UPDATE `%s`', $this->tableName);
        $queryData = $assoc;
        if ($this->whereClause === "1") {
            throw new QueryBuilderException('UPDATE query requires WHERE clause');
        }

        // SET clause
        $setClause = "";
        foreach ($assoc as $key => $value) {
            if (!is_string($key)) {
                throw new QueryBuilderException('UPDATE query cannot accept indexed array');
            }

            $setClause .= sprintf('`%1$s`=:%1$s, ', $key);
        }

        // Query Data
        foreach ($this->queryData as $key => $value) {
            if (!is_string($key)) {
                throw new QueryBuilderException('WHERE clause for UPDATE query requires named parameters');
            }

            // Prefix WHERE clause params with "__"
            $queryData["__" . $key] = $value;
        }

        // Compile Query
        $this->queryData = $queryData;
        $query .= sprintf(' SET %s WHERE %s', substr($setClause, 0, -2), str_replace(':', ':__', $this->whereClause));

        // Execute UPDATE query
        return $this->db->exec($query, $queryData);
    }

    /**
     * @return \Charcoal\Database\Queries\DbExecutedQuery
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function delete(): DbExecutedQuery
    {
        if ($this->whereClause === "1") {
            throw new QueryBuilderException('DELETE query requires WHERE clause');
        }

        return $this->db->exec(
            sprintf('DELETE FROM `%s` WHERE %s', $this->tableName, $this->whereClause),
            $this->queryData
        );
    }

    /**
     * @return \Charcoal\Database\Queries\DbFetchQuery
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function fetch(): DbFetchQuery
    {
        // Limit
        $limitClause = "";
        if ($this->selectStart && $this->selectLimit) {
            $limitClause = sprintf(' LIMIT %d,%d', $this->selectStart, $this->selectLimit);
        } elseif ($this->selectLimit) {
            $limitClause = sprintf(' LIMIT %d', $this->selectLimit);
        }

        // Query
        $query = sprintf(
            'SELECT' . ' %s FROM `%s` WHERE %s%s%s%s',
            $this->selectColumns,
            $this->tableName,
            $this->whereClause,
            $this->selectOrder,
            $limitClause,
            $this->selectLock ? " FOR UPDATE" : ""
        );

        // Fetch
        return $this->db->fetch($query, $this->queryData);
    }

    /**
     * Set table name
     * @param string $name
     * @return $this
     */
    public function table(string $name): static
    {
        $this->tableName = trim($name);
        return $this;
    }

    /**
     * Provide your own WHERE clause of query, does not start with "WHERE" word itself
     * @param string $clause
     * @param array $data
     * @return $this
     */
    public function where(string $clause, array $data): static
    {
        $this->whereClause = $clause;
        $this->queryData = $data;
        return $this;
    }

    /**
     * Creates a WHERE clause from input assoc array
     * @param array $cols
     * @return $this
     */
    public function find(array $cols): static
    {
        // Reset
        $this->whereClause = "";
        $this->queryData = [];

        // Process data
        foreach ($cols as $key => $val) {
            if (!is_string($key)) {
                continue; // skip
            }

            $this->whereClause = sprintf('`%1$s`=:%1$s, ', $key);
            $this->queryData[$key] = $val;
        }

        $this->whereClause = substr($this->whereClause, 0, -2);
        return $this;
    }

    /**
     * Select specified columns
     * @param string ...$cols
     * @return $this
     */
    public function cols(string ...$cols): static
    {
        $this->selectColumns = implode(",", array_map(function ($col) {
            return preg_match('/[(|)]/', $col) ? trim($col) : sprintf('`%1$s`', trim($col));
        }, $cols));
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function lock(): static
    {
        $this->selectLock = true;
        return $this;
    }

    /**
     * @param string ...$cols
     * @return $this
     */
    public function sortAsc(string ...$cols): static
    {
        $cols = array_map(function ($col) {
            return sprintf('`%1$s`', trim($col));
        }, $cols);

        $this->selectOrder = sprintf(" ORDER BY %s ASC", trim(implode(",", $cols), ", "));
        return $this;
    }

    /**
     * @param string ...$cols
     * @return $this
     */
    public function sortDesc(string ...$cols): static
    {
        $cols = array_map(function ($col) {
            return sprintf('`%1$s`', trim($col));
        }, $cols);

        $this->selectOrder = sprintf(" ORDER BY %s DESC", trim(implode(",", $cols), ", "));
        return $this;
    }

    /**
     * Define a offset value for SELECT query
     * @param int $from
     * @return $this
     */
    public function start(int $from): static
    {
        $this->selectStart = $from;
        return $this;
    }

    /**
     * Define limit value for SELECT query
     * @param int $to
     * @return $this
     */
    public function limit(int $to): static
    {
        $this->selectLimit = $to;
        return $this;
    }
}
