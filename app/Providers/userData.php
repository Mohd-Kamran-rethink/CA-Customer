<?php

namespace App\Providers;

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
            $view->with([
              'user' => $user??null,
               ]);
        });
        
    }
}
