<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Config;

use Charcoal\Base\Traits\NoDumpTrait;
use Charcoal\Database\Enums\DbConnectionStrategy;
use Charcoal\Database\Enums\DbDriver;

/**
 * Class DbCredentials
 * @package Charcoal\Database\Config
 */
class DbCredentials
{
    use NoDumpTrait;

    public function __construct(
        public readonly DbDriver             $driver,
        public readonly string               $dbName,
        #[\SensitiveParameter]
        public readonly string               $host = "localhost",
        public readonly ?int                 $port = null,
        #[\SensitiveParameter]
        public readonly ?string              $username = null,
        #[\SensitiveParameter]
        public readonly ?string              $password = null,
        public readonly DbConnectionStrategy $strategy = DbConnectionStrategy::Lazy,
    )
    {
        if (!in_array($this->driver->value, \PDO::getAvailableDrivers())) {
            throw new \OutOfBoundsException("Database driver is not supported in PDO build");
        }
    }

    /**
     * @return string
     */
    public function dsn(): string
    {
        if (!$this->dbName) {
            throw new \UnexpectedValueException("Cannot get DSN; Database name is not set");
        }

        switch ($this->driver) {
            case DbDriver::SQLITE:
                return sprintf("sqlite:%s", $this->dbName);
            default:
                $port = $this->port ? "port=" . $this->port . ";" : "";
                return sprintf("%s:host=%s;%sdbname=%s;charset=utf8mb4",
                    $this->driver->value,
                    $this->host,
                    $port,
                    $this->dbName);
        }
    }
}
