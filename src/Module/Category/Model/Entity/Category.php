<?php

namespace App\Module\Category\Model\Entity;

use App\Core\Utils\Collection;
use App\Module\CAtegory\Model\Repository\CategoryDAO;
use App\Module\Image\Model\Entity\Image;

/**
 * no lazy initialization for collections, because it'll need to add a field for the dao in the class, i prefered keeping it in constructor
 * Class Category
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
    private $images;

    public function __construct(CategoryDAO $categoryDAO, int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->images = new Collection($categoryDAO->getImages($this));
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
        return $this->images;
    }

    /**
     * @param Image $from
     * @param int $nb
     * @return array
     */
    public function getImagesList(Image $from, int $nb): array
    {
        $offset = $this->images->indexOf($from);

        if($offset < 0){
            $offset = 0;
        }

        if($offset > $this->images->length()){
            $offset = $this->images->length() - 1;
        }

        if($offset + $nb > $this->images->length()){
            $nb = $this->images->length() - $offset;
        }

        return array_slice($this->images->asArray(), $offset, $nb);
    }

    /**
     * saute en avant ou en arrière de $nb images
     * @param Image $img
     * @param int|null $nb
     * @return Image
     */
    function jumpToImage(Image $img, ?int $nb = 1): Image
    {
        $index = $this->images->indexOf($img);
        $offset = $index + $nb;

        // oob negatif
        if($nb < 0 && $offset < 0){
            $to = $this->images->first();
        } else if($nb >= 0 && $offset > $this->images->length()){ // oob positif
            $to = $this->images->last();
        } else { // nominal
            $to = $this->images->get($offset);
        }

        return $to;
    }

    /**
     * Retourne une image au hazard
     * @return Image
     */
    public function getRandomImage(): Image
    {
        return $this->images->get(rand(0, $this->images->length()));
    }
}