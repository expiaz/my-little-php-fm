<?php

namespace App\Module\Image\Model\Repository;

use App\Core\Container;
use App\Module\Category\Model\Entity\Category;
use App\Module\Category\Model\Repository\CategoryDAO;
use App\Module\Image\Controller\ImageController;
use App\Module\Image\Model\Entity\Image;
use PDO;

class DbImageDAO extends AbstractDAO
{

    /**
     * @var PDO
     */
    private $pdo;

    public const DB_PATH = ImageController::MODULE_PATH . 'ressources/images.db';

    /**
     * @var CategoryDAO
     */
    private $categoryDao;

    /**
     * register every picture found in AbstractDAO::absolutePath
     */
    public function register(): void
    {
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO images VALUES (:id, :name);";
        $query = $this->pdo->prepare($sql);
        foreach (static::scanDir(self::absolutePath) as $image) {
            $passed = $query->execute([
                'id' => NULL,
                'name' => $image
            ]);
            if (!$passed) {
                $this->pdo->rollBack();
                return;
            }
        }

        $this->pdo->commit();
    }

    public function __construct(Container $container)
    {
        $this->pdo = $container->get(PDO::class);
        $this->categoryDao = $container->get(CategoryDAO::class);
    }

    /**
     * @inheritdoc
     */
    public function size(): int
    {
        $sql = "SELECT COUNT(id) AS nb FROM images";
        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetch()->nb;
    }

    /**
     * @inheritdoc
     */
    public function getImage(int $id): Image
    {
        $sql = "SELECT * FROM images WHERE id = :id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            "id" => $this->restrictId($id)
        ]);

        $upplet = $query->fetch();

        $sql = "SELECT * FROM category WHERE id = :id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            "id" => $upplet->category
        ]);

        $r = $query->fetchAll();

        if(count($r)){
            $category = $r[0];
            return new Image(
                static::URL_PATH . $upplet->name,
                $upplet->id,
                new Category($this->categoryDao, $category->id, $category->name)
            );
        }

        return new Image(
            static::URL_PATH . $upplet->name,
            $upplet->id,
            null
        );
    }
}