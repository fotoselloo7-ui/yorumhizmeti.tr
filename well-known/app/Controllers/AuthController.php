<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) { redirect('/hesabim'); }
        $this->render('frontend/auth/login', ['pageTitle' => 'Giriş Yap'], 'auth');
    }

    public function loginPost(): void
    {
        Csrf::check();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($email, $password)) {
            flash('success', 'Başarıyla giriş yaptınız.');
            redirect('/hesabim');
        }

        flash('error', 'E-posta veya şifre hatalı.');
        flash('old', ['email' => $email]);
        redirect('/giris');
    }

    public function register(): void
    {
        if (Auth::check()) { redirect('/hesabim'); }
        $this->render('frontend/auth/register', ['pageTitle' => 'Kayıt Ol'], 'auth');
    }

    public function registerPost(): void
    {
        Csrf::check();
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $userId = $this->db->insert('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => 'active',
        ]);

        Auth::login($this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]));

        \App\Services\MailService::sendTemplate('user_registered', $data['email'], [
            'user_name' => $data['name'],
            'user_email' => $data['email'],
        ]);

        flash('success', 'Hesabınız oluşturuldu. Hoş geldiniz!');
        redirect('/hesabim');
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'Çıkış yaptınız.');
        redirect('/');
    }

    public function forgot(): void
    {
        $this->render('frontend/auth/forgot', ['pageTitle' => 'Şifremi Unuttum'], 'auth');
    }

    public function forgotPost(): void
    {
        Csrf::check();
        $email = trim($_POST['email'] ?? '');
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->db->insert('password_resets', ['email' => $email, 'token' => $token, 'type' => 'user']);

            \App\Services\MailService::sendTemplate('password_reset', $email, [
                'user_name' => $user['name'],
                'reset_link' => url('/sifre-sifirla/' . $token),
            ]);
        }

        flash('success', 'Şifre sıfırlama linki e-posta adresinize gönderildi.');
        redirect('/sifremi-unuttum');
    }

    public function reset(string $token): void
    {
        $reset = $this->db->fetch("SELECT * FROM password_resets WHERE token = ? AND type = 'user' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", [$token]);
        if (!$reset) {
            flash('error', 'Geçersiz veya süresi dolmuş link.');
            redirect('/sifremi-unuttum');
        }
        $this->render('frontend/auth/reset', ['pageTitle' => 'Şifre Sıfırla', 'token' => $token], 'auth');
    }

    public function resetPost(): void
    {
        Csrf::check();
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        $reset = $this->db->fetch("SELECT * FROM password_resets WHERE token = ? AND type = 'user' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", [$token]);
        if (!$reset) {
            flash('error', 'Geçersiz link.');
            redirect('/sifremi-unuttum');
        }

        $this->db->update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)], 'email = ?', [$reset['email']]);
        $this->db->delete('password_resets', 'email = ? AND type = ?', [$reset['email'], 'user']);

        flash('success', 'Şifreniz güncellendi. Giriş yapabilirsiniz.');
        redirect('/giris');
    }
}
