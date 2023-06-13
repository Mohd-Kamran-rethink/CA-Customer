<?php

namespace App\Providers;

use App\Franchise;
use App\Transaction;
use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class userData extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      
        View::composer('Admin.index', function ($view) {
          
            $user = session('user')??null;
            if ($user) {
                $userData = User::find($user->id);
            }
            $depositBanker=1;
            $depositers=1;
            $depositers=User::where('role','=','depositer')->get()->count();
            $depositBanker =User::where('role','=','deposit_banker')->get()->count();
            $withdraweres=User::where('role','=','withdrawrer')->get()->count();
            $withdrawrerBanker =User::where('role','=','withdrawal_banker')->get()->count();
            $franchiese =Franchise::get()->count();

            $pendingWithdraw =Transaction::where('type', 'Withdraw')->where('status', 'Pending')->get()->count();
            $pendingDeposit =Transaction::where('type', 'Deposit')->where('status', 'Pending')->get()->count();
            $franchiese =Franchise::get()->count();

            $view->with([
              'user' => $userData??null,
              'Datas'=>"hello",
              'depositBanker'=>$depositBanker,
              'depositers'=>$depositers,
              'withdraweres'=>$withdraweres,
              'withdrawrerBanker'=>$withdrawrerBanker,
              'franchiese'=>$franchiese,
              'pendingDeposit'=>$pendingDeposit,
              'pendingWithdraw'=>$pendingWithdraw

]);
        });
        
    }
}
