<?php

namespace App\Module\Category\Model\Repository;

use App\Core\Container;
use App\Core\Utils\Collection;
use App\Module\Category\Model\Entity\Category;
use App\Module\Image\Model\Entity\Image;
use App\Module\Image\Model\Repository\AbstractDAO;
use PDO;

class CategoryDAO
{

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * CategoryDAO constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->pdo = $container->get(PDO::class);
    }

    /**
     * @param int $id
     * @return Category
     */
    public function getCategory(int $id)
    {
        $sql = "SELECT * FROM category WHERE id = :id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            'id' => $id
        ]);

        $upplet = $query->fetch();

        return new Category($this, $upplet->id, $upplet->name);
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        $sql = "SELECT * FROM category";
        $query = $this->pdo->prepare($sql);
        $query->execute();

        $categories = [];
        foreach ($query->fetchAll() as $category){
            $categories[] = $this->getCategory($category->id);
        }

        return new Collection($categories);
    }

    /**
     * @param Category $category
     * @return Image[]
     */
    public function getImages(Category $category)
    {
        $sql = "SELECT * FROM images WHERE category = :id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            'id' => $category->getId()
        ]);

        $images = [];
        foreach ($query->fetchAll() as $image) {
            $images[] = new Image(AbstractDAO::URL_PATH . $image->name, $image->id, $category);
        }

        return $images;
    }

    /**
     * @param string $name
     * @return array
     * @internal param string $search
     */
    public function searchCategories(string $name): array
    {
        $sql = "SELECT * FROM category WHERE name LIKE ?";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            "%$name%"
        ]);

        $results = $query->fetchAll();
        $categories = [];
        foreach ($results as $result){
            $categories[] = new Category($this, $result->id, $result->name);
        }

        return $categories;
    }

}