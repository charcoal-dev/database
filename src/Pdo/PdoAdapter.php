<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Pdo;

use Charcoal\Database\DbCredentials;
use Charcoal\Database\Exception\DbConnectionException;
use Charcoal\Database\Exception\DbQueryException;
use Charcoal\Database\Exception\DbTransactionException;

/**
 * Class PdoAdapter
 * @package Charcoal\Database\Pdo
 */
abstract class PdoAdapter
{
    /** @var \PDO */
    protected \PDO $pdo;

    /**
     * @param \Charcoal\Database\DbCredentials $credentials
     * @param int $errorMode
     * @throws \Charcoal\Database\Exception\DbConnectionException
     */
    public function __construct(public readonly DbCredentials $credentials, int $errorMode)
    {
        $options = [\PDO::ATTR_ERRMODE => $errorMode];
        if ($credentials->persistent === true) {
            $options[\PDO::ATTR_PERSISTENT] = true;
        }

        try {
            $this->pdo = new \PDO($credentials->dsn(), $credentials->username,
                $credentials->password, $options);
        } catch (\PDOException $e) {
            // Connection is not established unless PERSISTENT mode is set
            throw new DbConnectionException("Failed to establish DB connection", previous: $e);
        }
    }

    /**
     * @return \PDO
     */
    public function pdoAdapter(): \PDO
    {
        return $this->pdo;
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
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function lastInsertId(): int
    {
        return intval($this->lastInsertSeq());
    }

    /**
     * @param string|null $seq
     * @return string
     * @throws \Charcoal\Database\Exception\DbQueryException
     */
    public function lastInsertSeq(?string $seq = null): string
    {
        try {
            $lastInsertId = $this->pdo->lastInsertId($seq);
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
     * @throws \Charcoal\Database\Exception\DbTransactionException
     */
    public function inTransaction(): bool
    {
        try {
            return $this->pdo->inTransaction();
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws \Charcoal\Database\Exception\DbTransactionException
     */
    public function beginTransaction(): void
    {
        try {
            if (!$this->pdo->beginTransaction()) {
                throw new DbTransactionException("Failed to begin database transaction");
            }
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws \Charcoal\Database\Exception\DbTransactionException
     */
    public function rollBack(): void
    {
        try {
            if (!$this->pdo->rollBack()) {
                throw new DbTransactionException("Failed to roll back transaction");
            }
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }

    /**
     * @return void
     * @throws \Charcoal\Database\Exception\DbTransactionException
     */
    public function commit(): void
    {
        try {
            if (!$this->pdo->commit()) {
                throw new DbTransactionException("Failed to commit transaction");
            }
        } catch (\PDOException $e) {
            throw DbTransactionException::fromPdoException($e);
        }
    }
}