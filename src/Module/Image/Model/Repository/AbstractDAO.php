<?php

namespace App\Module\Image\Model\Repository;

use App\Module\Image\Model\Entity\Image;

abstract class AbstractDAO {

    // Chemin LOCAL où se trouvent les images
    protected const absolutePath = PUBLIK . 'assets/img';

    // Chemin URL où se trouvent les images
    public const URL_PATH = WEBROOT . "/assets/img";

    /**
     * @param string $path
     * @param int|null $prefixLen
     * @param null|string $filter
     * @return array
     */
    public static function scanDir(string $path, ?int $prefixLen = -1, ?string $filter = "|^[^\.].*$|"): array
    {
        if ($prefixLen < 0) {
            $prefixLen = $realFrom =  strlen($path);
        } elseif($prefixLen === 0){
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
     * restrict the id between the min & max
     * @param int $id
     * @return int
     */
    protected function restrictId(int $id): int{
        if ($id < 1) {
            return 1;
        } else if ($id > $this->size()) {
            return $this->size();
        }

        return $id;
    }

    /**
     * Retourne une image au hazard
     * @return Image
     */
    function getRandomImage(): Image
    {
        return $this->getImage(rand($this->getFirstImage()->getId(), $this->getLastImage()->getId()));
    }

    /**
     * Retourne l'objet de la premiere image
     * @return Image
     */
    function getFirstImage(): Image
    {
        return $this->getImage(1);
    }

    /**
     * Retourne l'objet de la dernière image
     * @return Image
     */
    function getLastImage(): Image
    {
        return $this->getImage($this->size());
    }

    /**
     * Retourne l'image suivante d'une image
     * @param Image $img
     * @return Image
     */
    function getNextImage(Image $img): Image
    {
        return $this->jumpToImage($img, 1);
    }

    /**
     * Retourne l'image précédente d'une image
     * @param Image $img
     * @return Image
     */
    function getPrevImage(Image $img): Image
    {
        return $this->jumpToImage($img, -1);
    }

    /**
     * saute en avant ou en arrière de $nb images
     * @param Image $img
     * @param int|null $nb
     * @return Image
     */
    function jumpToImage(Image $img, ?int $nb = 1): Image
    {
        if($nb < 0 && $img->getId() + $nb < $this->getFirstImage()->getId()){
            $to = $this->getFirstImage();
        } else if($nb >= 0 && $img->getId() + $nb > $this->getLastImage()->getId()){
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
    function getImageList(Image $img, int $nb): array
    {
        $id = $img->getId();
        $max = $this->jumpToImage($img, $nb)->getId();

        $res = [$img];
        while (++$id <= $max) {
            $res[] = $this->getImage($id);
        }
        return $res;
    }


    /**
     * Retourne le nombre d'images référencées dans le DAO
     * @return int
     */
    public abstract function size(): int;

    /**
     * Retourne un objet image correspondant à l'identifiant
     * @param int $id
     * @return Image
     */
    public abstract function getImage(int $id): Image;

}