<?php
namespace Analogous\Service;

require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';

use Analogous\Model\User;
use Analogous\Repository\UserRepository;

class UserService
{

    public static function createUser($username, $password): ?User
    {
        $userRepo = new UserRepository();
        $user = new User(null, $username, $password, date('Y-m-d H:i:s'));
        return $userRepo->createUser($user);
    }

    /**
     * Updates the password of a user.
     * @param \Analogous\Model\User $user
     * @param mixed $newPassword Password in plain text to be hashed.
     * @return User|null
     */
    public static function updateUserPassword(User $user, $newPassword): ?User
    {
        if ($user) {
            $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->setPassword($newPassword);
            return (new UserRepository())->updateUser($user);
        }
        return null;
    }

}