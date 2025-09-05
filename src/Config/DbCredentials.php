<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Config;

use Charcoal\Base\Objects\Traits\NoDumpTrait;
use Charcoal\Database\Enums\DbConnectionStrategy;
use Charcoal\Database\Enums\DbDriver;

/**
 * Represents database credentials and configuration,
 * required for establishing a database connection.
 */
readonly class DbCredentials
{
    use NoDumpTrait;

    public function __construct(
        public DbDriver             $driver,
        public string               $dbName,
        #[\SensitiveParameter]
        public string               $host = "localhost",
        public ?int                 $port = null,
        #[\SensitiveParameter]
        public ?string              $username = null,
        #[\SensitiveParameter]
        public ?string              $password = null,
        public DbConnectionStrategy $strategy = DbConnectionStrategy::Lazy,
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
