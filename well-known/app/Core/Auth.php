<?php
namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        static $user = null;
        if ($user === null) {
            $db = Database::getInstance();
            $user = $db->fetch("SELECT * FROM users WHERE id = ? AND status = 'active'", [$_SESSION['user_id']]);
            if (!$user) {
                self::logout();
                return null;
            }
        }
        return $user;
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Son giriş güncelle
        $db = Database::getInstance();
        $db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
    }

    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
        if ($user && password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }
        return false;
    }
}
