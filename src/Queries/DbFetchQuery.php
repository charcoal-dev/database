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
     * Alias of next() method
     * @return array|null
     */
    public function row(): ?array
    {
        return $this->getNext();
    }

    /**
     * Returns next row
     * @return array|null
     */
    public function getNext(): ?array
    {
        $rows = $this->stmt->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * @return array
     * @throws \Charcoal\Database\Exception\QueryFetchException
     */
    public function getAll(): array
    {
        $rows = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (!is_array($rows)) {
            throw new QueryFetchException($this->query, 'Failed to fetch rows from executed query');
        }

        return $rows;
    }
}