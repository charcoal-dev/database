<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Queries;

use Charcoal\Base\Traits\NoDumpTrait;
use Charcoal\Base\Traits\NotCloneableTrait;
use Charcoal\Base\Traits\NotSerializableTrait;
use Charcoal\Database\Exception\QueryFetchException;

/**
 * Class FetchQuery
 * @package Charcoal\Database\Queries
 */
class FetchQuery
{
    use NoDumpTrait;
    use NotCloneableTrait;
    use NotSerializableTrait;

    /**
     * @param \Charcoal\Database\Queries\ExecutedQuery $query
     * @param \PDOStatement $stmt
     */
    public function __construct(
        public readonly ExecutedQuery  $query,
        private readonly \PDOStatement $stmt,
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