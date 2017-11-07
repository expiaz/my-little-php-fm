<?php

namespace App\Core;

use App\Core\Utils\Query;
use PDO;

class BaseDAO
{

    protected $table = 'NO_OP';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * BaseDAO constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $statement
     * @param array|null $bindings
     * @return Query
     */
    protected function query(string $statement, ?array $bindings = [])
    {
        return (new Query($this->pdo, $statement))->execute($bindings);
    }

    protected function getById($id)
    {
        return (new Query(
            $this->pdo,
            "SELECT * FROM {$this->table} WHERE id = :id"
        ))->execute([
            'id' => $id
        ]);
    }

}