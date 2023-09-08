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

namespace Charcoal\Database\Queries;

use Charcoal\Database\Exception\QueryExecuteException;
use Charcoal\Database\PdoError;

/**
 * Class DbFailedQuery
 * @package Charcoal\Database\Queries
 */
class DbFailedQuery
{
    public readonly string $queryStr;
    public readonly array $boundData;
    public readonly PdoError $error;

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
