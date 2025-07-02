<?php
namespace Analogous\Util;

require_once __DIR__ . '/ConfigHelper.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../service/UserService.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/TokenRepository.php';

use Analogous\Repository\UserRepository;
use Analogous\Service\UserService;

class AuthHelper
{

    public static function requireLogin()
    {
        if (!ConfigHelper::getConfigValue('auth.enabled', true, true)) {
            return true; // Authentication is disabled, allow access
        }
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            header('Location: /login');
            exit();
        }
    }

    public static function requireTokenAuth()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_GET["token"])) {
            $token = $_GET["token"];
            $tokenRepo = new \Analogous\Repository\TokenRepository();
            $validToken = $tokenRepo->getTokenByContent($token);
            if ($validToken) {
                return $validToken;
            } else {
                return false; // Invalid token
            }
        } else {
            return false; // No token provided
        }
    }

    public static function login($username, $password)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!ConfigHelper::getConfigValue('auth.enabled', false, true)) {
            return true; // Authentication is disabled, allow access
        }

        $userRepo = new UserRepository();
        $user = $userRepo->getUserByUsername($username);
        if (!$user) {
            return false; // User not found
        }

        if (!password_verify($password, $user->getPassword())) {
            return false; // Invalid password
        }

        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $user->getUsername();
        return true;
    }

}