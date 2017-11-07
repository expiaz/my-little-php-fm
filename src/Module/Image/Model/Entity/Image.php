<?php

namespace App\Module\Image\Model\Entity;

# Notion d'image
use App\Module\Category\Model\Entity\Category;

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
     * Image constructor.
     * @param string $url
     * @param int $id
     * @param Category|null $category
     */
    public function __construct(string $url, int $id, ?Category $category)
    {
        $this->url = $url;
        $this->id = $id;
        $this->category = $category;
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
}