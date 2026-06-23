<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('customer_id')) {
            return redirect()->route('customer.auth.start')
                             ->with('error', 'Please sign in to continue.');
        }

        return $next($request);
    }
}
