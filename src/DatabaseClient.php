<?php
/**
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database;

use Charcoal\Base\Objects\Traits\NoDumpTrait;
use Charcoal\Base\Objects\Traits\NotCloneableTrait;
use Charcoal\Contracts\Storage\Enums\StorageType;
use Charcoal\Contracts\Storage\StorageProviderInterface;
use Charcoal\Database\Config\DbCredentials;
use Charcoal\Database\Enums\DbDriver;
use Charcoal\Database\Exceptions\DbConnectionException;
use Charcoal\Database\Exceptions\QueryExecuteException;
use Charcoal\Database\Pdo\PdoAdapter;
use Charcoal\Database\Pdo\PdoError;
use Charcoal\Database\Queries\ExecutedQuery;
use Charcoal\Database\Queries\FailedQuery;
use Charcoal\Database\Queries\FetchQuery;
use Charcoal\Database\Queries\QueryBuilder;
use Charcoal\Database\Queries\QueryLog;
use Charcoal\Events\Contracts\EventStoreOwnerInterface;

/**
 * Represents a database client that integrates with a database storage system.
 * It provides abstraction over the PDO interface and includes functionality for
 * query execution, event handling, query logging, and serialization.
 */
class DatabaseClient extends PdoAdapter implements
    StorageProviderInterface,
    EventStoreOwnerInterface
{
    public readonly string $storeContextId;
    public readonly QueryLog $queries;

    use NoDumpTrait;
    use NotCloneableTrait;

    /**
     * @param DbCredentials $credentials
     * @param string|null $password
     * @param int $errorMode
     * @param bool $serializeEvents
     * @param bool $serializeQueries
     * @throws DbConnectionException
     */
    public function __construct(
        #[\SensitiveParameter]
        public readonly DbCredentials $credentials,
        #[\SensitiveParameter]
        public readonly ?string       $password = null,
        int                           $errorMode = \PDO::ERRMODE_EXCEPTION,
        bool                          $serializeEvents = true,
        public bool                   $serializeQueries = false,
    )
    {
        $this->storeContextId = $this->createStoreContextId();
        parent::__construct($errorMode, $serializeEvents);
        $this->queries = new QueryLog();
    }

    /**
     * @return array
     */
    protected function collectSerializableData(): array
    {
        $data = parent::collectSerializableData();
        $data["credentials"] = $this->credentials;
        $data["storeContextId"] = $this->storeContextId;
        $data["serializeEvents"] = $this->serializeEvents;
        $data["serializeQueries"] = $this->serializeQueries;
        $data["events"] = isset($this->events) && $this->serializeEvents ? $this->events : null;
        $data["queries"] = isset($this->queries) && $this->serializeQueries ? $this->queries : null;
        return $data;
    }

    /**
     * @param array $data
     * @return void
     * @throws DbConnectionException
     */
    public function __unserialize(array $data): void
    {
        $this->credentials = $data["credentials"];
        $this->storeContextId = $data["storeContextId"];
        $this->serializeQueries = $data["serializeQueries"];
        if ($this->serializeQueries && $data["queries"]) {
            $this->queries = $data["queries"];
        }

        if (!isset($this->queries)) {
            $this->queries = new QueryLog();
        }

        parent::__unserialize($data);
    }

    /**
     * @return QueryBuilder
     */
    public function queryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    /**
     * @throws QueryExecuteException
     */
    public function exec(string $query, array $data = []): ExecutedQuery
    {
        try {
            return $this->ensureConnection()->queryExec($query, $data, false);
        } catch (DbConnectionException $e) {
            throw new QueryExecuteException($query, $data, null, "Database connection failed", previous: $e);
        }
    }

    /**
     * @throws QueryExecuteException
     */
    public function fetch(string $query, array $data = []): FetchQuery
    {
        try {
            return $this->ensureConnection()->queryExec($query, $data, true);
        } catch (DbConnectionException $e) {
            throw new QueryExecuteException($query, $data, null, "Database connection failed", previous: $e);
        }
    }

    /**
     * @throws QueryExecuteException
     */
    private function queryExec(string $queryStr, array $data, bool $fetchQuery): ExecutedQuery|FetchQuery
    {
        try {
            try {
                $stmt = $this->queryPrepareStatement($queryStr);
                $this->queryBindParams($stmt, $queryStr, $data);
                $query = new ExecutedQuery($stmt, $queryStr, $data);
            } catch (\PDOException $e) {
                throw new QueryExecuteException($queryStr, $data, new PdoError($e), $e->getMessage(), $e->getCode(), $e);
            }
        } catch (QueryExecuteException $e) {
            $this->queries->append(new FailedQuery($e)); // Append failed query to log
            throw $e;
        }

        $this->queries->append($query); // Append successful query to log
        return $fetchQuery ? new FetchQuery($query, $stmt) : $query;
    }

    /**
     * @throws QueryExecuteException
     * @throws \PDOException
     */
    private function queryPrepareStatement(string $queryStr): \PDOStatement
    {
        $stmt = $this->pdo->prepare($queryStr);
        if (!$stmt) {
            throw new QueryExecuteException($queryStr, [], new PdoError($stmt), "Failed to prepare PDO statement");
        }

        return $stmt;
    }

    /**
     * @throws QueryExecuteException
     * @throws \PDOException
     */
    private function queryBindParams(\PDOStatement $stmt, string $queryStr, array $data): void
    {
        try {
            // Bind params
            foreach ($data as $key => $value) {
                $type = match (gettype($value)) {
                    "boolean" => \PDO::PARAM_BOOL,
                    "integer" => \PDO::PARAM_INT,
                    "NULL" => \PDO::PARAM_NULL,
                    "string", "double" => \PDO::PARAM_STR,
                    default => throw new \LogicException("Cannot bind value of type " . gettype($value))
                };

                if (is_int($key)) {
                    $key++; // Indexed arrays get +1 to numeric keys so that they don't start with 0
                }

                if (!$stmt->bindValue($key, $value, $type)) {
                    throw new \LogicException(
                        sprintf('Failed to bind value of type "%s" to key "%s"', gettype($value), $key)
                    );
                }
            }
        } catch (\LogicException $e) {
            throw new QueryExecuteException($queryStr, $data, new PdoError($stmt), $e->getMessage());
        }
    }

    /**
     * @return StorageType
     */
    public function storageType(): StorageType
    {
        return StorageType::Database;
    }

    /**
     * @return string
     */
    protected function createStoreContextId(): string
    {
        return strtolower(sprintf("[%s][@%s]:%s",
            $this->credentials->driver->value,
            $this->credentials->host,
            match ($this->credentials->driver) {
                DbDriver::SQLITE => basename($this->credentials->dbName),
                default => $this->credentials->dbName,
            } ?? throw new \LogicException("Invalid database name"),
        ));
    }

    /**
     * @return string
     */
    public function storageProviderId(): string
    {
        return $this->storeContextId;
    }

    /**
     * @return string
     */
    public function eventsUniqueContextKey(): string
    {
        return $this->storeContextId;
    }
}
