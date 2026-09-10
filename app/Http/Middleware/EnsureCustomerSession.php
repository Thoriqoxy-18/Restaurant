<?php
namespace App\Http\Middleware;
use App\Models\CustomerSession;
use App\Models\RestaurantTable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $table = $request->route('table');
        if (!$table || !$table instanceof RestaurantTable) {
            abort(404, 'Meja tidak ditemukan');
        }

        $token = $request->cookie('customer_session_token');
        $session = null;

        if ($token) {
            // Session hanya boleh dipakai ulang untuk MEJA yang sama.
            $session = CustomerSession::where('session_token', $token)
                ->where('restaurant_table_id', $table->id)
                ->whereNull('ended_at')
                ->first();
        }

        if (!$session || !$session->isValid()) {
            $session = CustomerSession::create([
                'restaurant_table_id' => $table->id,
                'started_at' => now(),
            ]);
        }

        $request->attributes->set('customer_session', $session);
        $request->attributes->set('restaurant_table', $table);

        return $next($request)->withCookie(cookie('customer_session_token', $session->session_token, 720));
    }
}
