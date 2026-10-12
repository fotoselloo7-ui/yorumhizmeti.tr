<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Csrf;
use App\Services\DiscountService;

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;

        if (!empty($cart)) {
            $db = Database::getInstance();
            $validCart = [];
            foreach ($cart as $item) {
                $pkg = $db->fetch("SELECT p.* FROM packages p
                 INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                 LEFT JOIN categories parent ON parent.id = c.parent_id
                 WHERE p.id = ? AND p.status = 'active'
                   AND (c.parent_id IS NULL OR parent.status = 'active')", [(int)($item['id'] ?? 0)]);
                if ($pkg) {
                    $price = $pkg['discount_price'] && $pkg['discount_price'] < $pkg['price'] ? $pkg['discount_price'] : $pkg['price'];
                    $min = max(1, (int)$pkg['min_quantity']);
                    $max = max($min, (int)$pkg['max_quantity']);
                    $qty = max($min, min($max, (int)($item['quantity'] ?? $min)));
                    $cartKey = count($validCart);
                    $validCart[] = ['id' => (int)$pkg['id'], 'quantity' => $qty, 'fields' => $item['fields'] ?? []];
                    $cartItems[] = array_merge($pkg, ['quantity' => $qty, 'line_total' => $price * $qty, 'cart_key' => $cartKey, 'fields' => $item['fields'] ?? []]);
                    $total += $price * $qty;
                }
            }
            // Discontinued packages must not remain as invisible, unremovable cart entries.
            $_SESSION['cart'] = $validCart;
        }

        $couponCode=(string)($_SESSION['discount_code']??'');
        $pricing=DiscountService::quotePackages(
            $cartItems,
            Auth::check()?Auth::user():null,
            $couponCode
        );
        $payableTotal=(float)($pricing['final_total']??$total);

        $this->render('frontend/cart', [
            'pageTitle' => 'Sepet - ' . setting('site_name'),
            'paymentOptions' => (new \App\Services\PaymentGatewayManager())->getCheckoutOptions(),
            'cartItems' => $cartItems,
            'total' => $payableTotal,
            'pricing' => $pricing,
            'couponCode' => $couponCode,
        ]);
    }

    public function add(): void
    {
        Csrf::check();
        $packageId = (int) ($_POST['package_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        $pkg = Database::getInstance()->fetch("SELECT p.* FROM packages p
                 INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                 LEFT JOIN categories parent ON parent.id = c.parent_id
                 WHERE p.id = ? AND p.status = 'active'
                   AND (c.parent_id IS NULL OR parent.status = 'active')", [$packageId]);
        if (!$pkg) {
            flash('error', 'Paket bulunamadı.');
            redirect('/');
            return;
        }

        $smmLink = \App\Services\SmmCatalogService::mapping($packageId);
        if ($smmLink && (!$smmLink['enabled'] || !$smmLink['is_available'] || !$smmLink['provider_active'])) {
            flash('error', 'Bu hizmet şu anda siparişe kapalı.');
            redirect('/paket/' . $pkg['slug']);
            return;
        }
        $min = max(1, (int)$pkg['min_quantity']);
        $max = max($min, (int)$pkg['max_quantity']);
        $quantity = max($min, min($max, $quantity));
        $cart = $_SESSION['cart'] ?? [];
        $captured = [];
        foreach (\App\Services\SmmOrderFields::fields($pkg) as $field) {
            $key=(string)$field['field_key'];
            if (isset($_POST['field_'.$packageId.'_'.$key])) {
                $captured[$key]=mb_substr(trim((string)$_POST['field_'.$packageId.'_'.$key]),0,2048);
            }
        }
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] == $packageId) {
                $item['quantity'] = max($min, min((int)$item['quantity'] + $quantity, $max));
                if ($captured) $item['fields']=$captured;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = ['id' => $packageId, 'quantity' => $quantity, 'fields'=>$captured];
        }

        $_SESSION['cart'] = $cart;
        flash('success', 'Paket sepete eklendi.');
        redirect('/sepet');
    }

    public function update(): void
    {
        Csrf::check();
        $key = (int) ($_POST['key'] ?? -1);
        if (!isset($_SESSION['cart'][$key])) {
            redirect('/sepet');
        }

        $item = $_SESSION['cart'][$key];
        $pkg = Database::getInstance()->fetch(
            "SELECT p.min_quantity, p.max_quantity, p.status FROM packages p
             INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
             LEFT JOIN categories parent ON parent.id = c.parent_id
             WHERE p.id = ? AND p.status = 'active'
               AND (c.parent_id IS NULL OR parent.status = 'active')",
            [(int)($item['id'] ?? 0)]
        );
        if (!$pkg) {
            flash('error', 'Bu paket artık satışta değil.');
            redirect('/sepet');
        }

        $min = max(1, (int)$pkg['min_quantity']);
        $max = max($min, (int)$pkg['max_quantity']);
        $quantity = max($min, min($max, (int)($_POST['quantity'] ?? $min)));
        $_SESSION['cart'][$key]['quantity'] = $quantity;
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

    public function applyCoupon(): void
    {
        Csrf::check();
        $code=strtoupper(trim((string)($_POST['coupon_code']??'')));
        if($code===''){
            flash('error','Kupon kodunu girin.');
            redirect('/sepet');
            return;
        }
        $items=$this->discountItems();
        if(!$items){
            flash('error','Kupon uygulamak için sepetinizde ürün olmalı.');
            redirect('/sepet');
            return;
        }
        $quote=DiscountService::quotePackages(
            $items,
            Auth::check()?Auth::user():null,
            $code
        );
        if(empty($quote['valid']) || (float)$quote['discount_amount']<=0){
            unset($_SESSION['discount_code']);
            flash('error',$quote['error']?:'Bu kupon sepetinize uygulanamıyor.');
            redirect('/sepet');
            return;
        }
        $_SESSION['discount_code']=$code;
        flash('success',$code.' kuponu uygulandı. '.money((float)$quote['discount_amount']).' indirim kazandınız.');
        redirect('/sepet');
    }

    public function removeCoupon(): void
    {
        Csrf::check();
        unset($_SESSION['discount_code']);
        flash('success','Kupon sepetten kaldırıldı.');
        redirect('/sepet');
    }

    private function discountItems(): array
    {
        $cart=$_SESSION['cart']??[];
        if(!$cart) return [];
        $db=Database::getInstance();
        $items=[];
        foreach($cart as $item){
            $pkg=$db->fetch(
                "SELECT p.* FROM packages p
                 INNER JOIN categories c ON p.category_id=c.id AND c.status='active'
                 LEFT JOIN categories parent ON parent.id=c.parent_id
                 WHERE p.id=? AND p.status='active'
                   AND (c.parent_id IS NULL OR parent.status='active')",
                [(int)($item['id']??0)]
            );
            if(!$pkg) continue;
            $price=($pkg['discount_price']&&$pkg['discount_price']<$pkg['price'])
                ?(float)$pkg['discount_price']:(float)$pkg['price'];
            $min=max(1,(int)$pkg['min_quantity']);
            $max=max($min,(int)$pkg['max_quantity']);
            $qty=max($min,min($max,(int)($item['quantity']??$min)));
            $items[]=array_merge($pkg,[
                'quantity'=>$qty,
                'line_total'=>round($price*$qty,2),
            ]);
        }
        return $items;
    }
}
