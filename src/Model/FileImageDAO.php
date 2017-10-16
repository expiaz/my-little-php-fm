<?php

namespace App\Model;

# Le 'Data Access Object' d'un ensemble images
class FileImageDAO extends AbstractDAO
{

    # Tableau pour stocker tous les chemins des images
    private $imgEntry = [];


    function __construct()
    {
        $this->imgEntry = static::scanDir(self::absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function size(): int
    {
        return count($this->imgEntry);
    }

    /**
     * @inheritdoc
     */
    public function getImage(int $id): Image
    {
        $id = $this->restrictId($id);
        return new Image(static::urlPath . $this->imgEntry[$id - 1], $id);
    }

}


?>