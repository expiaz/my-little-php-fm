<?php

namespace App\Module\Category\Model\Repository;

use App\Core\BaseDAO;
use App\Core\Utils\Collection;
use App\Module\Category\Model\Entity\Category;
use App\Module\Image\Model\Repository\DbImageDAO;
use Exception;
use stdClass;

class CategoryDAO extends BaseDAO
{

    protected $table = 'category';

    /**
     * @param stdClass $upplet
     * @return Category
     */
    public function build(stdClass $upplet): Category
    {
        if ($this->resolved->has($upplet->id)) {
            return $this->resolved->get($upplet->id);
        }
        $cat = new Category($this->container->get(DbImageDAO::class), $upplet->id, $upplet->name);
        $this->resolved->set($upplet->id, $cat);
        return $cat;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function addCategory(string $name): Category
    {
        $this->query("INSERT INTO category VALUES (NULL, :name)", [
            'name' => $name
        ]);

        $c = new Category(
            $this->container->get(DbImageDAO::class),
            $this->pdo->lastInsertId(),
            $name
        );
        $this->resolved[$c->getId()] = $c;

        return $c;
    }

    /**
     * @param int $id
     * @return Category
     * @throws Exception
     */
    public function getCategory(int $id)
    {
        $cat = $this->getById($id)->getResult();

        if ($cat === null) {
            throw new Exception("Category $id not found");
        }

        return $this->build($cat);
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return new Collection(array_map(function ($upplet): Category {
            return $this->build($upplet);
        }, $this->query('SELECT * FROM category')->getResults()));
    }

    /**
     * @param string $name
     * @return array
     * @internal param string $search
     */
    public function searchCategories(string $name): array
    {
        return array_map(function ($upplet): Category {
            return $this->build($upplet);
        }, $this->query("SELECT * FROM category WHERE name LIKE ?", [
            "%$name%"
        ])->getResults());
    }

}