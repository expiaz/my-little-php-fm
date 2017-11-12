<?php

namespace App\Module\User\Model\Repository;

use App\Core\BaseDAO;
use App\Module\Category\Model\Repository\CategoryDAO;
use App\Module\Image\Model\Entity\Image;
use App\Module\Image\Model\Repository\DbImageDAO;
use App\Module\User\Model\Entity\User;
use stdClass;

class UserDAO extends BaseDAO {

    protected $table = 'users';

    /**
     * @param stdClass $upplet
     * @return User
     */
    public function build(stdClass $upplet): User
    {
        if($this->resolved->has($upplet->id)){
            return $this->resolved->get($upplet->id);
        }
        $user = new User($this->container->get(DbImageDAO::class), $upplet->id, $upplet->name);
        $this->resolved->set($upplet->id, $user);
        return $user;
    }

    /**
     * authenticate an user
     * @param string $login
     * @param string $pwd
     * @return User|null
     */
    public function auth(string $login, string $pwd): ?User
    {
        $encrypted = @password_hash(
            $pwd,
            PASSWORD_BCRYPT,
            [
                'salt' => 'thisisachainof22characters'
            ]
        );

        $query = $this->query("SELECT * FROM users WHERE login = :login AND password = :password", [
            'login' => $login,
            'password' => $encrypted
        ]);
        if(! $query->haveResult()){
            return null;
        }
        $upplet = $query->getResult();
        return $this->getUser($upplet->id);
    }

    public function getUser($id): User
    {
        $query = $this->getById($id);
        if(! $query->haveResult()){
            throw new \Exception("UserDAO::getUser $id does not exists");
        }
        return $this->build($query->getResult());
    }


}