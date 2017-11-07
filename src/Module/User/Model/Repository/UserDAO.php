<?php

namespace App\Module\User\Model\Repository;

use App\Core\BaseDAO;
use App\Module\User\Model\Entity\User;

class UserDAO extends BaseDAO {

    protected $table = 'users';

    /**
     * authenticate an user
     * @param string $login
     * @param string $pwd
     * @return User|null
     */
    public function auth(string $login, string $pwd): ?User
    {
        $encrypted = password_hash(
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

    public function getUser($id): ?User
    {
        $query = $this->getById($id);
        if(! $query->haveResult()){
            return null;
        }
        $upplet = $query->getResult();
        return new User($upplet->id, $upplet->name);
    }

    public function createUser(): bool
    {

    }

}