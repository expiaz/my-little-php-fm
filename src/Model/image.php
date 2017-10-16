<?php

namespace App\Model;

# Notion d'image
class Image
{
    private $url = "";
    private $id = 0;

    function __construct($url, $id)
    {
        $this->url = $url;
        $this->id = $id;
    }

    # Retourne l'URL de cette image
    function getURL(): string
    {
        return $this->url;
    }

    function getId(): int
    {
        return $this->id;
    }
}


?>