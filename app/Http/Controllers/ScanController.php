<?php

namespace App\Http\Controllers;

use App\Models\CustomerSession;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function orderPage(RestaurantTable $table): RedirectResponse
    {
        $token = request()->cookie('customer_session_token');
        $session = $token ? CustomerSession::where('session_token', $token)->whereNull('ended_at')->first() : null;

        if (! $session || ! $session->isValid()) {
            $session = CustomerSession::create([
                'restaurant_table_id' => $table->id,
                'started_at' => now(),
            ]);
        }

        session([
            'customer_session_id' => $session->id,
            'table_id' => $table->id,
            'table_number' => $table->code,
        ]);

        return redirect()->route('home')->withCookie(cookie('customer_session_token', $session->session_token, 720));
    }
}
