<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Database\Config\DbCredentials;

/**
 * Class ConnectionWaiting
 * @package Charcoal\Database\Events\Connection
 */
readonly class ConnectionWaiting implements ConnectionStateContext
{
    public function __construct(
        #[\SensitiveParameter]
        public DbCredentials $credentials
    )
    {
    }
}