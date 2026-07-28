<?php

namespace App\Http\Middleware;

use App\Models\CustomerSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('customer_session_token');

        if ($token) {
            $session = CustomerSession::where('session_token', $token)
                ->whereNull('ended_at')
                ->first();

            if ($session && $session->isValid()) {
                session([
                    'customer_session_id' => $session->id,
                    'table_id' => $session->restaurant_table_id,
                    'table_number' => $session->restaurantTable->code ?? null,
                ]);
                return $next($request);
            }
        }

        return redirect()->route('home')->with('error', 'Silakan scan QR meja terlebih dahulu.');
    }
}
