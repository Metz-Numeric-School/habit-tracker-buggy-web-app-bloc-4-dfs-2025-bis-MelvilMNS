<?php
namespace App\Repository;

use App\Entity\User;
use App\Utils\EntityMapper;
use Mns\Buggy\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function findAll()
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM mns_user";
        $STMT = $DB->prepare($SQL);
        $STMT->execute();
        $users = $STMT->fetchAll();
        return EntityMapper::mapCollection(User::class, $users);
    }

    public function find(int $id)
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM mns_user WHERE id = :id";
        $STMT = $DB->prepare($SQL);
        $STMT->bindValue(":id", $id);
        $STMT->execute();
        $user = $STMT->fetch();
        return EntityMapper::map(User::class, $user);
    }

    public function findByEmail(string $email)
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM mns_user WHERE email = :email";
        $STMT = $DB->prepare($SQL);
        $STMT->bindValue(":email", $email);
        $STMT->execute();
        $user = $STMT->fetch();
        return EntityMapper::map(User::class, $user);
    }

    public function insert(array $data = array())
    {
        $DB = $this->getConnection();
        $SQL = "INSERT INTO mns_user (lastname, firstname, email, password, isadmin) VALUES (:lastname, :firstname, :email, :password, :isadmin)";
        $STMT = $DB->prepare($SQL);
        $STMT->execute($data);
        return $this->getConnection()->lastInsertId();
    }
}