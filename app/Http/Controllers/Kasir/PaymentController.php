<?php
namespace App\Http\Controllers\Kasir;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function process(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'method' => 'required|in:cash,qris,debit',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pesanan ini sudah dibayar.');
        }

        DB::beginTransaction();
        try {
            Payment::create([
                'order_id' => $order->id,
                'kasir_id' => Auth::id(),
                'amount' => $data['amount'],
                'method' => $data['method'],
                'status' => 'success',
                'paid_at' => now(),
            ]);
            $order->update(['payment_status' => 'paid', 'payment_method' => $data['method'], 'status' => 'completed']);
            DB::commit();
            return redirect()->route('kasir.orders.show', $order)->with('success', 'Pembayaran berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran.');
        }
    }
}
