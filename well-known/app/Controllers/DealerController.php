<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\DealerProgramService;
use App\Services\LegacyCustomerService;

final class DealerController extends Controller
{
    public function index(): void
    {
        $user=Auth::user();
        if(!$user){redirect('/giris');return;}
        $account=DealerProgramService::account((int)$user['id']);
        $legacy=LegacyCustomerService::overview((int)$user['id']);
        $this->render('frontend/account/dealer',[
            'pageTitle'=>'Bayilik ve İş Ortaklığı',
            'user'=>$user,
            'dealerReady'=>DealerProgramService::ready(),
            'dealer'=>$account,
            'legacy'=>$legacy,
            'visits'=>$account?DealerProgramService::referrals((int)$account['id']):0
        ]);
    }

    public function apply(): void
    {
        Csrf::check();
        $user=Auth::user();
        if(!$user){redirect('/giris');return;}
        try {
            flash('success',DealerProgramService::apply((int)$user['id']));
        } catch(\Throwable $error){
            flash('error',$error->getMessage());
        }
        redirect('/bayilik');
    }

    public function referral(string $code): void
    {
        $code=strtoupper(trim($code));
        try {
            if(DealerProgramService::track($code)){
                header('Cache-Control: no-store');
                header('Location: /hazir-scriptler',true,302);
                exit;
            }
        }catch(\Throwable $error) {
            error_log('Dealer referral route unavailable: '.get_class($error));
        }
        header('Location: /hazir-scriptler',true,302);
        exit;
    }
}
