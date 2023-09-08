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

/**
 * Class QueryArchive
 * @package Charcoal\Database\Queries
 */
class QueryArchive implements \IteratorAggregate
{
    private array $queries = [];
    private int $count = 0;

    /**
     * Appends an executed query into archive
     * @param \Charcoal\Database\Queries\DbExecutedQuery $query
     * @return void
     */
    public function append(DbExecutedQuery $query): void
    {
        $this->queries[] = $query;
        $this->count++;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->queries = [];
        $this->count = 0;
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->queries);
    }
}
