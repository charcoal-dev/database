<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Exceptions;

use Charcoal\Database\Queries\ExecutedQuery;

/**
 * Class QueryFetchException
 * @package Charcoal\Database\Exception
 */
class QueryFetchException extends DbQueryException
{
    public function __construct(
        public readonly ExecutedQuery $query,
        string                        $message = "",
        int                           $code = 0,
        ?\Throwable                   $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
