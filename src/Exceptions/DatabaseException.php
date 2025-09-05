<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Exceptions;

/**
 * Class DatabaseException
 * @package Charcoal\Database\Exception
 */
class DatabaseException extends \Exception
{
    public static function fromPdoException(\PDOException $e): static
    {
        return new static($e->getMessage(), intval($e->getCode()), previous: $e);
    }
}
