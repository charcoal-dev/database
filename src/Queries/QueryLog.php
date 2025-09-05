<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Queries;

use Charcoal\Base\Registry\Traits\InstancedObjectsRegistry;
use Charcoal\Base\Registry\Traits\RegistryKeysLowercaseTrimmed;

/**
 * A log management class that maintains a collection of executed or failed queries.
 * It supports appending queries, clearing the log, counting the stored queries,
 * and iterating over the collection.
 * @uses InstancedObjectsRegistry<ExecutedQuery|FailedQuery>
 * @property array<ExecutedQuery|FailedQuery> $queries
 */
final class QueryLog implements \IteratorAggregate
{
    use InstancedObjectsRegistry;
    use RegistryKeysLowercaseTrimmed;

    private array $queries = [];
    private int $count = 0;

    public function append(ExecutedQuery|FailedQuery $query): void
    {
        $this->queries[] = $query;
        $this->count++;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function flush(): void
    {
        $this->queries = [];
        $this->count = 0;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->queries);
    }
}
