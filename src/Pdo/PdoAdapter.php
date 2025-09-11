<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Pdo;

use Charcoal\Base\Objects\Traits\ControlledSerializableTrait;
use Charcoal\Database\Enums\DbConnectionStrategy;
use Charcoal\Database\Events\Connection\ConnectionError;
use Charcoal\Database\Events\Connection\ConnectionSuccess;
use Charcoal\Database\Events\Connection\ConnectionWaiting;
use Charcoal\Database\Events\DbEvents;
use Charcoal\Database\Exceptions\DbConnectionException;
use Charcoal\Database\Exceptions\DbQueryException;
use Charcoal\Database\Exceptions\DbTransactionException;

/**
 * Abstract class representing a PDO adapter for database interactions.
 * Provides methods to manage database connections, transactions, and related tasks.
 */
abstract class PdoAdapter
{
    use ControlledSerializableTrait;

    protected ?\PDO $pdo = null;
    public readonly DbEvents $events;

    /**
     * @param int $errorMode
     * @param bool $serializeEvents
     * @throws DbConnectionException
     */
    public function __construct(
        protected readonly int $errorMode,
        public bool            $serializeEvents = true,
    )
    {
        $this->events = new DbEvents($this);
        $this->initialize();
    }

    /**
     * @return void
     * @throws DbConnectionException
     */
    private function initialize(): void
    {
        if ($this->credentials->strategy === DbConnectionStrategy::Lazy) {
            $this->events->dispatch(new ConnectionWaiting($this->credentials));
            return;
        }

        // Establish connection if not "Lazy" strategy
        $this->ensureConnection();
    }

    /**
     * @return array
     */
    protected function collectSerializableData(): array
    {
        return [
            "pdo" => null,
            "events" => isset($this->events) && $this->serializeEvents ? $this->events : null,
            "serializeEvents" => $this->serializeEvents,
            "errorMode" => $this->errorMode,
        ];
    }

    /**
     * @param array $data
     * @return void
     * @throws DbConnectionException
     */
    public function __unserialize(array $data): void
    {
        $this->pdo = null;
        $this->errorMode = $data["errorMode"];
        $this->serializeEvents = $data["serializeEvents"];
        if ($this->serializeEvents && $data["events"]) {
            $this->events = $data["events"];
        }

        if (!isset($this->events)) {
            $this->events = new DbEvents($this);
        }

        $this->initialize();
    }

    /**
     * @return $this
     * @throws DbConnectionException
     */
    protected function ensureConnection(): static
    {
        if (isset($this->pdo)) {
            return $this;
        }

        $options = [\PDO::ATTR_ERRMODE => $this->errorMode];
        if ($this->credentials->strategy === DbConnectionStrategy::Persistent) {
            $options[\PDO::ATTR_PERSISTENT] = true;
        }

        try {
            $this->pdo = new \PDO($this->credentials->dsn(), $this->credentials->username,
                $this->password ?? $this->credentials->password, $options);
        } catch (\Throwable $t) {
            $this->events->dispatch(new ConnectionError($t));
            throw new DbConnectionException("Failed to establish DB connection", previous: $t);
        }

        $this->events->dispatch(new ConnectionSuccess($this->credentials, $this));
        return $this;
    }

    /**
     * @return \PDO
     * @throws DbConnectionException
     */
    public function pdoAdapter(): \PDO
    {
        return $this->ensureConnection()->pdo;
    }

    /**
     * @return PdoError
     */
    public function error(): PdoError
    {
        return new PdoError($this->pdo);
    }

    /**
     * @return int
     * @throws DbQueryException
     */
    public function lastInsertId(): int
    {
        return intval($this->lastInsertSeq());
    }

    /**
     * @param string|null $seq
     * @return string
     * @throws DbQueryException
     */
    public function lastInsertSeq(?string $seq = null): string
    {
        try {
            $lastInsertId = $this->pdo?->lastInsertId($seq) ?: null;
            if (!$lastInsertId) {
                throw new DbQueryException("Failed to retrieve last inserted id");
            }

            return $lastInsertId;
        } catch (\PDOException $e) {
            throw DbQueryException::fromPdoException($e);
        }
    }

    /**
     * @return bool
     * @throws DbTransactionException
     */
    public function inTransaction(): bool
    {
        try {
            return $this->pdo?->inTransaction() ?: false;
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws DbTransactionException
     */
    public function beginTransaction(): void
    {
        try {
            if (!$this->ensureConnection()->pdo->beginTransaction()) {
                throw new DbTransactionException("Failed to begin database transaction");
            }
        } catch (DbConnectionException $e) {
            throw new DbTransactionException("Database connection failed", previous: $e);
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws DbTransactionException
     */
    public function rollBack(): void
    {
        try {
            if (!$this->pdo?->rollBack()) {
                throw new DbTransactionException("Failed to roll back transaction");
            }
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws DbTransactionException
     */
    public function commit(): void
    {
        try {
            if (!$this->pdo?->commit()) {
                throw new DbTransactionException("Failed to commit transaction");
            }
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }
}