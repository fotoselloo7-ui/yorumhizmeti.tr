<?php
namespace App\Core;

class AdminAuth
{
    public static function check(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public static function admin(): ?array
    {
        if (!self::check()) return null;
        static $admin = null;
        if ($admin === null) {
            $db = Database::getInstance();
            $admin = $db->fetch("SELECT * FROM admins WHERE id = ? AND status = 'active'", [$_SESSION['admin_id']]);
            if (!$admin) {
                self::logout();
                return null;
            }
        }
        return $admin;
    }

    public static function id(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }

    public static function login(array $admin): void
    {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email'], $_SESSION['admin_role']);
    }

    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $admin = $db->fetch("SELECT * FROM admins WHERE email = ? AND status = 'active'", [$email]);
        if ($admin && password_verify($password, $admin['password'])) {
            self::login($admin);
            return true;
        }
        return false;
    }

    public static function mustChangePassword(): bool
    {
        $admin = self::admin();
        return $admin && ($admin['must_change_password'] ?? false);
    }
}
