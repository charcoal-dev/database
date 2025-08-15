<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events;

use Charcoal\Database\DatabaseClient;

/**
 * Class DbEvents
 * @package Charcoal\Database\Events
 */
readonly class DbEvents
{
    public ConnectionEvent $connectionState;

    public function __construct(DatabaseClient $db)
    {
        $this->connectionState = new ConnectionEvent($db);
    }
}