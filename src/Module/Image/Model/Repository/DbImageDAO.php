<?php

namespace App\Module\Image\Model\Repository;

use App\Core\BaseDAO;
use App\Core\Container;
use App\Core\Utils\Query;
use App\Module\Category\Model\Entity\Category;
use App\Module\Category\Model\Repository\CategoryDAO;
use App\Module\Image\Model\Entity\Image;
use App\Module\User\Model\Entity\User;
use App\Module\User\Model\Repository\UserDAO;
use Exception;
use stdClass;

class DbImageDAO extends BaseDAO
{

    protected $table = 'images';

    /**
     * @var CategoryDAO
     */
    private $categoryDao;
    private $userDao;

    /**
     * @param string $path
     * @param int|null $prefixLen
     * @param null|string $filter
     * @return array
     */
    public static function scanDir(string $path, ?int $prefixLen = -1, ?string $filter = "|^[^\.].*$|"): array
    {
        if ($prefixLen < 0) {
            $prefixLen = $realFrom = strlen($path);
        } elseif ($prefixLen === 0) {
            $realFrom = strrpos($path, DIRECTORY_SEPARATOR) + 1;
        } else {
            $realFrom = $prefixLen;
        }

        $pathName = substr($path, $realFrom);

        if (is_dir($path)) {

            $files = array_slice(
                scandir($path),
                2
            );

            $tree = [];

            foreach ($files as $filename) {
                if (preg_match($filter, $filename)) {

                    $childs = self::scanDir("$path/$filename", $prefixLen, $filter);

                    $tree = array_merge(
                        $tree,
                        $childs
                    );

                }
            }

            return $tree;
        }

        return [$pathName];

    }

    /**
     * register every picture found in AbstractDAO::absolutePath
     */
    public function register(): void
    {
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO images VALUES (:id, :name, 0, '', 0);";
        $query = $this->pdo->prepare($sql);
        foreach (static::scanDir($this->imagesPath) as $image) {
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

    /**
     * DbImageDAO constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->categoryDao = $container->get(CategoryDAO::class);
        $this->userDao = $container->get(UserDAO::class);
    }

    /**
     * @param stdClass $upplet
     * @return Image
     */
    public function build(stdClass $upplet): Image
    {
        if($this->resolved->has($upplet->id)){
            return $this->resolved->get($upplet->id);
        }

        /**
         * @var $category Query
         * @var $author Query
         */
        $category = $this->categoryDao->getById($upplet->category);
        $author = $this->userDao->getById($upplet->author);

        if(! $category->haveResult()) {
            $category = null;
        } else {
            $category = $this->categoryDao->build($category->getResult());
        }

        if(! $author->haveResult()) {
            $author = null;
        } else {
            $author = $this->userDao->build($author->getResult());
        }

        $image = new Image(
            strpos($upplet->name, 'http') !== false ? $upplet->name : $this->imagesUrl . $upplet->name,
            $upplet->id,
            $category,
            $upplet->description,
            $author
        );

        $this->resolved->set($image->getId(), $image);
        return $image;
    }

    /**
     * restrict the id between the min & max
     * @param int $id
     * @return int
     */
    protected function restrictId(int $id): int
    {
        if ($id < 1) {
            return 1;
        } else if ($id > $this->size()) {
            return $this->size();
        }

        return $id;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->query("SELECT COUNT(id) AS nb FROM images")->getResult()->nb;
    }

    /**
     * @param string $url
     * @param string $name
     * @param Category|null $cat
     * @param string $desc
     * @param User|null $auth
     * @return Image
     */
    public function addImage(string $url, string $name, Category $cat, string $desc, User $auth): Image
    {
        $this->query("INSERT INTO images VALUES (NULL, :name, :category, :description, :author)", [
            'name' => $name,
            'category' => $cat->getId(),
            'description' => $desc,
            'author' => $auth->getId()
        ]);

        $i = new Image(
            $url,
            $this->pdo->lastInsertId(),
            $cat,
            $desc,
            $auth
        );
        $this->resolved[$i->getId()] = $i;
        return $i;
    }

    /**
     * @param int $id
     * @return Image
     * @throws Exception
     */
    public function getImage(int $id): Image
    {
        $image = $this->getById($this->restrictId($id))->getResult();

        if ($image === null) {
            throw new Exception("Image $id not found");
        }

        return $this->build($image);
    }

    /**
     * @param Category $category
     * @return Image[]
     */
    public function getByCategory(Category $category): array {
        return array_map(function ($upplet): Image {
            return $this->build($upplet);
        }, $this->query("SELECT * FROM images WHERE category = :id", [
            'id' => $category->getId()
        ])->getResults());
    }

    /**
     * @param User $author
     * @return array
     */
    public function getByAuthor(User $author): array {
        return array_map(function ($upplet): Image {
            return $this->build($upplet);
        }, $this->query("SELECT * FROM images WHERE author = :id", [
            'id' => $author->getId()
        ])->getResults());
    }

    /**
     * Retourne une image au hazard
     * @return Image
     */
    public function getRandomImage(): Image
    {
        return $this->getImage(rand($this->getFirstImage()->getId(), $this->getLastImage()->getId()));
    }

    /**
     * Retourne l'objet de la premiere image
     * @return Image
     */
    public function getFirstImage(): Image
    {
        return $this->getImage(1);
    }

    /**
     * Retourne l'objet de la dernière image
     * @return Image
     */
    public function getLastImage(): Image
    {
        return $this->getImage($this->size());
    }

    /**
     * Retourne l'image suivante d'une image
     * @param Image $img
     * @return Image
     */
    public function getNextImage(Image $img): Image
    {
        return $this->jumpToImage($img, 1);
    }

    /**
     * Retourne l'image précédente d'une image
     * @param Image $img
     * @return Image
     */
    public function getPrevImage(Image $img): Image
    {
        return $this->jumpToImage($img, -1);
    }

    /**
     * saute en avant ou en arrière de $nb images
     * @param Image $img
     * @param int|null $nb
     * @return Image
     */
    public function jumpToImage(Image $img, ?int $nb = 1): Image
    {
        if ($nb < 0 && $img->getId() + $nb < $this->getFirstImage()->getId()) {
            $to = $this->getFirstImage();
        } else if ($nb >= 0 && $img->getId() + $nb > $this->getLastImage()->getId()) {
            $to = $this->getLastImage();
        } else {
            $to = $this->getImage($img->getId() + $nb);
        }

        return $to;
    }

    /**
     * Retourne la liste des images consécutives à partir d'une image
     * @param Image $img
     * @param $nb
     * @return Image[]
     */
    public function getImageList(Image $img, int $nb): array
    {
        $id = $img->getId();
        $max = $this->jumpToImage($img, $nb)->getId();

        $res = [$img];
        while (++$id <= $max) {
            $res[] = $this->getImage($id);
        }
        return $res;
    }

}