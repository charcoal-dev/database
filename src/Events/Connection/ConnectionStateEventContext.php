<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Events\AbstractEvent;
use Charcoal\Events\Contracts\EventContextInterface;

/**
 * Class DbConnectionStateContext
 * @package Charcoal\Database\Events
 */
abstract class ConnectionStateEventContext implements EventContextInterface
{
    public function __construct(
        protected ConnectionStateEvent $event,
    )
    {
    }

    public function getEvent(): AbstractEvent
    {
        return $this->event;
    }
}