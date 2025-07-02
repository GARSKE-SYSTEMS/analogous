<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../util/Database.php';

use Analogous\Model\User;
use Analogous\Util\Database;

class UserRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = User::fromArray($row);
        }
        return $users;
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return User::fromArray($row);
        }
        return null;
    }

    public function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return User::fromArray($row);
        }
        return null;
    }

    public function createUser(User $user)
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $user->getUsername());
        $stmt->bindParam(':password', password_hash($user->getPassword(), PASSWORD_BCRYPT));

        if ($stmt->execute()) {
            $user->setId($this->db->lastInsertId());
            return $user;
        }
        return null;
    }

    public function updateUser(User $user): ?User
    {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, password = :password WHERE id = :id");
        $stmt->bindParam(':id', $user->getId(), \PDO::PARAM_INT);
        $stmt->bindParam(':username', $user->getUsername());
        $stmt->bindParam(':password', $user->getPassword());
        $stmt->execute();
        return $user;
    }

    public function deleteUser(User $user)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user->getId(), \PDO::PARAM_INT);
        return $stmt->execute();
    }
}