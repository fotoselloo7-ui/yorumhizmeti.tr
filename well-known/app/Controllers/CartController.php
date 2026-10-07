<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Csrf;

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;

        if (!empty($cart)) {
            $db = Database::getInstance();
            foreach ($cart as $key => $item) {
                $pkg = $db->fetch("SELECT * FROM packages WHERE id = ? AND status = 'active'", [$item['id']]);
                if ($pkg) {
                    $price = $pkg['discount_price'] && $pkg['discount_price'] < $pkg['price'] ? $pkg['discount_price'] : $pkg['price'];
                    $qty = $item['quantity'] ?? 1;
                    $cartItems[] = array_merge($pkg, ['quantity' => $qty, 'line_total' => $price * $qty, 'cart_key' => $key]);
                    $total += $price * $qty;
                }
            }
        }

        $this->render('frontend/cart', [
            'pageTitle' => 'Sepet - ' . setting('site_name'),
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function add(): void
    {
        Csrf::check();
        $packageId = (int) ($_POST['package_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        $pkg = Database::getInstance()->fetch("SELECT * FROM packages WHERE id = ? AND status = 'active'", [$packageId]);
        if (!$pkg) {
            flash('error', 'Paket bulunamadı.');
            redirect('/');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] == $packageId) {
                $item['quantity'] = min($item['quantity'] + $quantity, $pkg['max_quantity']);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = ['id' => $packageId, 'quantity' => min($quantity, $pkg['max_quantity'])];
        }

        $_SESSION['cart'] = $cart;
        flash('success', 'Paket sepete eklendi.');
        redirect('/sepet');
    }

    public function update(): void
    {
        Csrf::check();
        $key = (int) ($_POST['key'] ?? -1);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = $quantity;
        }
        redirect('/sepet');
    }

    public function remove(): void
    {
        Csrf::check();
        $key = (int) ($_POST['key'] ?? -1);
        if (isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        flash('success', 'Paket sepetten çıkarıldı.');
        redirect('/sepet');
    }
}
