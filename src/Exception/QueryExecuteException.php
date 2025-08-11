<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Exception;

use Charcoal\Database\PdoError;

/**
 * Class QueryExecuteException
 * @package Charcoal\Database\Exception
 */
class QueryExecuteException extends DbQueryException
{
    public function __construct(
        public readonly string   $queryStr,
        public readonly array    $boundData,
        public readonly PdoError $error,
        string                   $message = "",
        int|string               $code = 0,
        ?\Throwable              $previous = null)
    {
        parent::__construct($message, intval($code), $previous);
    }
}
