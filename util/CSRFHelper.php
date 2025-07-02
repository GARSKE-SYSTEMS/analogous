<?php
namespace Analogous\Util;

class CSRFHelper
{
    const TOKEN_SESSION_KEY = 'csrf_token';

    /**
     * Returns the CSRF token, generating and storing it in session if necessary.
     *
     * @return string
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[self::TOKEN_SESSION_KEY])) {
            $_SESSION[self::TOKEN_SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_SESSION_KEY];
    }

    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_SESSION_KEY] = $token;
        return $token;
    }

    /**
     * Returns an HTML hidden input field with the CSRF token.
     *
     * @return string
     */
    public static function insertHiddenInput(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validates a provided CSRF token against the session-stored token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function validateToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[self::TOKEN_SESSION_KEY]) || $token === null) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_SESSION_KEY], $token);
    }

    /**
     * Checks the CSRF token from POST request and returns validity.
     *
     * @return bool
     */
    public static function requireRequestToken(): bool
    {
        $token = $_POST['csrf_token'] ?? null;
        return self::validateToken($token);
    }
}