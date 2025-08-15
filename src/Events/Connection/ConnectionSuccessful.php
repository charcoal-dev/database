<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Database\DatabaseClient;
use Charcoal\Database\DbCredentials;

/**
 * Class ConnectionSuccessful
 * @package Charcoal\Database\Events\Connection
 */
readonly class ConnectionSuccessful implements ConnectionStateContext
{
    public function __construct(
        #[\SensitiveParameter]
        public DbCredentials  $credentials,
        public DatabaseClient $db
    )
    {
    }
}