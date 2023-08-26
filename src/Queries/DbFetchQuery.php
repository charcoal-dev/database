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

use Charcoal\Database\Exception\QueryFetchException;
use Charcoal\OOP\Traits\NoDumpTrait;
use Charcoal\OOP\Traits\NotCloneableTrait;
use Charcoal\OOP\Traits\NotSerializableTrait;

/**
 * Class DbFetchQuery
 * @package Charcoal\Database\Queries
 */
class DbFetchQuery
{
    use NoDumpTrait;
    use NotCloneableTrait;
    use NotSerializableTrait;

    /**
     * @param \Charcoal\Database\Queries\DbExecutedQuery $query
     * @param \PDOStatement $stmt
     */
    public function __construct(
        public readonly DbExecutedQuery $query,
        private readonly \PDOStatement  $stmt,
    )
    {
    }

    /**
     * Alias of getNext() method
     * @return array|null
     * @throws \Charcoal\Database\Exception\QueryFetchException
     */
    public function row(): ?array
    {
        return $this->getNext();
    }

    /**
     * Returns next row
     * @return array|null
     * @throws \Charcoal\Database\Exception\QueryFetchException
     */
    public function getNext(): ?array
    {
        try {
            $row = $this->stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new QueryFetchException($this->query, $e->getMessage(), previous: $e);
        }

        return is_array($row) ? $row : null;
    }

    /**
     * Returns all rows from
     * @return array
     * @throws \Charcoal\Database\Exception\QueryFetchException
     */
    public function getAll(): array
    {
        try {
            $rows = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new QueryFetchException($this->query, $e->getMessage(), previous: $e);
        }

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        return is_array($rows) ? $rows : [];
    }
}