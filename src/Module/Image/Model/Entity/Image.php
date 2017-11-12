<?php

namespace App\Module\Image\Model\Entity;

# Notion d'image
use App\Module\Category\Model\Entity\Category;
use App\Module\User\Model\Entity\User;

class Image
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $id;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var User|null
     */
    private $author;

    /**
     * Image constructor.
     * @param string $url
     * @param int $id
     * @param Category|null $category
     * @param null|string $description
     * @param User|null $author
     */
    public function __construct(string $url, int $id, ?Category $category, ?string $description = '', ?User $author)
    {
        $this->url = $url;
        $this->id = $id;
        $this->category = $category;
        $this->description = $description;
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return User|null
     */
    public function getAuthor()
    {
        return $this->author;
    }
}