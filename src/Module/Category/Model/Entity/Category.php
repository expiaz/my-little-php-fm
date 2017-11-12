<?php

namespace App\Module\Category\Model\Entity;

use App\Core\Utils\Collection;
use App\Module\Image\Model\Entity\Image;
use App\Module\Image\Model\Repository\DbImageDAO;
use Exception;

/**
 * @package App\Module\Category\Model\Entity
 */
class Category
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;

    /**
     * @var Collection<Image> the images of the category
     */
    private $images = null;

    /**
     * @var DbImageDAO
     */
    private $imageDao;

    public function __construct(DbImageDAO $dao, int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->imageDao = $dao;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<Image>
     */
    public function getImages(): Collection
    {
        if($this->images === null) {
            $this->images = new Collection($this->imageDao->getByCategory($this));
        }
        return $this->images;
    }

    /**
     * @param Image $from
     * @param int $nb
     * @return array
     * @throws Exception
     */
    public function getImagesList(Image $from, int $nb): array
    {
        $needle = null;
        foreach ($this->getImages()->asArray() as $image){
            if($from->getId() === $image->getId()){
                $needle = $image;
            }
        }

        if($needle === null){
            throw new Exception("Category::getImageList {$from->getURL()} does not exists in {$this->getName()}");
        }

        $offset = $this->getImages()->indexOf($needle);

        if($offset < 0){
            $offset = 0;
        }

        if($offset > $this->getImages()->length()){
            $offset = $this->getImages()->length() - 1;
        }

        if($offset + $nb > $this->getImages()->length()){
            $nb = $this->getImages()->length() - $offset;
        }

        return array_slice($this->getImages()->asArray(), $offset, $nb);
    }

    /**
     * saute en avant ou en arrière de $nb images
     * @param Image $img
     * @param int|null $nb
     * @return Image
     * @throws Exception
     */
    function jumpToImage(Image $img, ?int $nb = 1): Image
    {
        $needle = null;
        foreach ($this->getImages()->asArray() as $image){
            if($img->getId() === $image->getId()){
                $needle = $image;
            }
        }

        if( $needle === null){
            throw new Exception("Category::getImageList {$img->getURL()} does not exists in {$this->getName()}");
        }

        $index = $this->getImages()->indexOf($needle);
        $offset = $index + $nb;

        // oob negatif
        if($nb < 0 && $offset < 0){
            $to = $this->getImages()->first();
        } else if($nb >= 0 && $offset >= $this->getImages()->length()){ // oob positif
            $to = $this->getImages()->last();
        } else { // nominal
            $to = $this->getImages()->get($offset);
        }

        return $to;
    }

    /**
     * Retourne une image au hazard
     * @return Image
     */
    public function getRandomImage(): Image
    {
        return $this->getImages()->get(rand(0, $this->getImages()->length() - 1));
    }
}