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

namespace Charcoal\Database\Exception;

use Charcoal\Database\Queries\DbExecutedQuery;

/**
 * Class QueryFetchException
 * @package Charcoal\Database\Exception
 */
class QueryFetchException extends DbQueryException
{
    public function __construct(
        public readonly DbExecutedQuery $query,
        string                          $message = "",
        int                             $code = 0,
        ?\Throwable                     $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
