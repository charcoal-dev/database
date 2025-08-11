<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

namespace Charcoal\Database\Queries;

use Charcoal\Database\Exception\QueryExecuteException;
use Charcoal\Database\PdoError;

/**
 * Class FailedQuery
 * @package Charcoal\Database\Queries
 */
readonly class FailedQuery
{
    public string $queryStr;
    public array $boundData;
    public PdoError $error;

    /**
     * @param \Charcoal\Database\Exception\QueryExecuteException $e
     */
    public function __construct(QueryExecuteException $e)
    {
        $this->queryStr = $e->queryStr;
        $this->boundData = $e->boundData;
        $this->error = $e->error;
    }
}
