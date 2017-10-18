<?php

namespace App\Model\Entity;

# Notion d'image
class Image
{
    private $url;
    private $id = 0;

    function __construct(string $url, int $id)
    {
        $this->url = $url;
        $this->id = $id;
    }

    function getURL(): string
    {
        return $this->url;
    }

    function getId(): int
    {
        return $this->id;
    }
}