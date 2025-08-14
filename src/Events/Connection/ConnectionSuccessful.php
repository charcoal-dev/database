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
class ConnectionSuccessful extends ConnectionStateEventContext
{
    public function __construct(
        ConnectionStateEvent           $event,
        #[\SensitiveParameter]
        public readonly DbCredentials  $credentials,
        public readonly DatabaseClient $db
    )
    {
        parent::__construct($event);
    }
}