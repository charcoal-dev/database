<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Pdo;

use Charcoal\Database\DbCredentials;
use Charcoal\Database\Enums\DbConnectionStrategy;
use Charcoal\Database\Exception\DbConnectionException;
use Charcoal\Database\Exception\DbQueryException;
use Charcoal\Database\Exception\DbTransactionException;

/**
 * Class PdoAdapter
 * @package Charcoal\Database\Pdo
 */
abstract class PdoAdapter
{
    /** @var \PDO|null */
    protected ?\PDO $pdo = null;

    /**
     * @param DbCredentials $credentials
     * @param int $errorMode
     * @throws DbConnectionException
     */
    public function __construct(
        #[\SensitiveParameter]
        public readonly DbCredentials $credentials,
        protected readonly int        $errorMode
    )
    {
        // Establish connection is not Lazy strategy
        if ($this->credentials->strategy !== DbConnectionStrategy::Lazy) {
            $this->isConnected();
        }
    }

    /**
     * @return $this
     * @throws DbConnectionException
     */
    protected function isConnected(): static
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
                $this->credentials->password, $options);
        } catch (\PDOException $e) {
            // Connection is not established unless PERSISTENT mode is set
            throw new DbConnectionException("Failed to establish DB connection", previous: $e);
        }

        return $this;
    }

    /**
     * @return \PDO
     * @throws DbConnectionException
     */
    public function pdoAdapter(): \PDO
    {
        return $this->isConnected()->pdo;
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
            if (!$this->isConnected()->pdo->beginTransaction()) {
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