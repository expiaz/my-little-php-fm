<?php

namespace App\Core\Utils;

use PDO;
use PDOStatement;

class Query
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PDOStatement
     */
    private $query;

    /**
     * @var array
     */
    private $results;

    /**
     * Query constructor.
     * @param PDO $pdo
     * @param string $statement
     */
    public function __construct(PDO $pdo, string $statement)
    {
        $this->pdo = $pdo;

        $this->query = $this->pdo->prepare($statement);
    }

    /**
     * @param array $bindings
     * @param int|null $fetchStyle
     * @return Query
     */
    public function execute(?array $bindings = [], ?int $fetchStyle = null): Query
    {
        $this->query->execute($bindings);
        $this->results = $this->query->fetchAll($fetchStyle);
        return $this;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return mixed|null
     */
    public function getResult()
    {
        return $this->haveResult()
            ? $this->results[0]
            : null;
    }

    /**
     * @return bool
     */
    public function haveResult(): bool
    {
        return count($this->results) > 0;
    }

    /**
     * @return int
     */
    public function resultLenght(): int
    {
        return count($this->results);
    }

}