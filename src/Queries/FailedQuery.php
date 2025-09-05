<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

namespace Charcoal\Database\Queries;

use Charcoal\Database\Exceptions\QueryExecuteException;
use Charcoal\Database\Pdo\PdoError;

/**
 * Represents a failed database query and captures information
 * about the failed execution for debugging purposes.
 */
readonly class FailedQuery
{
    public string $queryStr;
    public array $boundData;
    public ?PdoError $error;

    /**
     * @param \Charcoal\Database\Exceptions\QueryExecuteException $e
     */
    public function __construct(QueryExecuteException $e)
    {
        $this->queryStr = $e->queryStr;
        $this->boundData = $e->boundData;
        $this->error = $e->error;
    }
}
