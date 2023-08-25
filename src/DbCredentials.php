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

namespace Charcoal\Database;

use Charcoal\OOP\Traits\NoDumpTrait;

/**
 * Class DbCredentials
 * @package Charcoal\Database
 */
class DbCredentials
{
    use NoDumpTrait;

    /**
     * @param \Charcoal\Database\DbDriver $driver
     * @param string $dbName
     * @param string $host
     * @param int|null $port
     * @param string|null $username
     * @param string|null $password
     * @param bool $persistent
     */
    public function __construct(
        public readonly DbDriver $driver,
        public readonly string   $dbName,
        public readonly string   $host = "localhost",
        public readonly ?int     $port = null,
        public readonly ?string  $username = null,
        public readonly ?string  $password = null,
        public bool              $persistent = false
    )
    {
        if (!in_array($this->driver, \PDO::getAvailableDrivers())) {
            throw new \OutOfBoundsException('Database driver is not support PDO');
        }
    }

    /**
     * @return string
     */
    public function dsn(): string
    {
        if (!$this->dbName) {
            throw new \UnexpectedValueException('Cannot get DSN; Database name is not set');
        }

        switch ($this->driver) {
            case "sqlite":
                return sprintf('sqlite:%s', $this->dbName);
            default:
                $port = $this->port ? "port=" . $this->port . ";" : "";
                return sprintf(
                    '%s:host=%s;%sdbname=%s;charset=utf8mb4',
                    $this->driver->value,
                    $this->host,
                    $port,
                    $this->dbName
                );
        }
    }
}
