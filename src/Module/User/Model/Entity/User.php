<?php

namespace App\Module\User\Model\Entity;

use App\Core\Utils\Collection;
use App\Module\Image\Model\Repository\DbImageDAO;
use App\Module\User\Model\Repository\UserDAO;

class User {

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Collection<Image>
     */
    private $images;

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
            $this->images = new Collection($this->imageDao->getByAuthor($this));
        }
        return $this->images;
    }

}