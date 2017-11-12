<?php

namespace App\Core;

use App\Core\Utils\Collection;
use App\Core\Utils\Query;
use PDO;
use stdClass;

abstract class BaseDAO
{
    protected $table = 'NO_OP';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Collection
     */
    protected $resolved;

    protected $imagesPath;
    protected $imagesUrl;

    /**
     * BaseDAO constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->pdo = $container->get(PDO::class);
        $this->imagesPath = $container->get('config')->get('image.path');
        $this->imagesUrl = $container->get('config')->get('image.url');
        $this->resolved = new Collection();
    }

    /**
     * @param stdClass $upplet
     * @return mixed
     */
    public abstract function build(stdClass $upplet);

    /**
     * @param string $statement
     * @param array|null $bindings
     * @return Query
     */
    protected function query(string $statement, ?array $bindings = [])
    {
        return (new Query($this->pdo, $statement))->execute($bindings);
    }

    /**
     * @param $id
     * @return Query
     */
    public function getById($id): Query
    {
        return (new Query(
            $this->pdo,
            "SELECT * FROM {$this->table} WHERE id = :id"
        ))->execute([
            'id' => $id
        ]);
    }

}