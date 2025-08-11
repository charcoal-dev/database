<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Queries;

use Charcoal\Base\Concerns\InstancedObjectsRegistry;
use Charcoal\Base\Concerns\RegistryKeysLowercaseTrimmed;

/**
 * Class QueryArchive
 * @package Charcoal\Database\Queries
 * @property array<ExecutedQuery|FailedQuery> $queries
 */
class QueryLog implements \IteratorAggregate
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
