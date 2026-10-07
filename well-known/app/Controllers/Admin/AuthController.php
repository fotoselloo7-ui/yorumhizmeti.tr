<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\AdminAuth;
use App\Core\Csrf;

class AuthController extends Controller
{
    public function login(): void
    {
        if (AdminAuth::check()) { redirect('/admin'); }
        $this->render('admin/login', ['pageTitle' => 'Admin Giriş'], 'auth');
    }

    public function loginPost(): void
    {
        Csrf::check();
        if (AdminAuth::attempt($_POST['email'] ?? '', $_POST['password'] ?? '')) {
            logActivity('admin_login', 'Admin giriş yaptı.');
            redirect('/admin');
        }
        flash('error', 'E-posta veya şifre hatalı.');
        redirect('/admin/giris');
    }

    public function logout(): void
    {
        logActivity('admin_logout', 'Admin çıkış yaptı.');
        AdminAuth::logout();
        redirect('/admin/giris');
    }
}
