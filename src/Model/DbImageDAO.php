<?php

namespace App\Model;

use PDO;
use PDOException;

class DbImageDAO extends AbstractDAO
{

    /**
     * @var PDO
     */
    private $pdo;

    private $dbPath = __DIR__ . DIRECTORY_SEPARATOR . "Database/sqlite/images.db";

    /**
     * register every picture found in
     */
    public function register(): void
    {
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO images VALUES (:id, :name);";
        $query = $this->pdo->prepare($sql);
        foreach (static::scanDir(self::absolutePath) as $image) {
            $passed = $query->execute([
                'id' => NULL,
                'name' => $image
            ]);
            if(! $passed){
                $this->pdo->rollBack();
                return;
            }
        }

        $this->pdo->commit();
    }

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "sqlite:$this->dbPath",
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function size(): int
    {
        $sql = "SELECT COUNT(id) as nb FROM images";
        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetch()->nb;
    }

    public function getImage(int $id): Image
    {
        $sql = "SELECT * FROM images WHERE id = :id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            "id" => $this->restrictId($id)
        ]);

        $upplet = $query->fetch();

        return new Image(static::urlPath . $upplet->name, $upplet->id);
    }
}