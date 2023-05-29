<?php

namespace App\Http\Middleware;

use Closure;

class ValidateManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check if the user is customer_care_manager i.e manger for this customer project
        if (session()->has('user') && session('user')->role=="customer_care_manager") {
        
            return $next($request);
        }
        else
        {
            return redirect('/');
        }
    }
}
