<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('customer_id')) {
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}
