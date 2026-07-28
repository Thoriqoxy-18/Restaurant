@extends('admin.layouts.app')
@section('title', 'Kasir Dashboard - ' . config('app.name'))
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Kasir Dashboard</h2>
        <p class="text-sm text-gray-500">Manage incoming orders and update their status.</p>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Processing</p>
            <p class="text-2xl font-bold text-blue-600">{{ $orders->where('status', 'processing')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Today's Revenue</p>
            <p class="text-2xl font-bold text-primary">Rp {{ number_format($orders->whereIn('status', ['completed'])->sum('total_amount'), 0, ',', '.') }}</p>
        </div>
    </div>

    @php $statuses = ['pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed']; @endphp
    @foreach ($statuses as $status => $label)
        @php $filtered = $orders->where('status', $status); @endphp
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700">{{ $label }}</h3>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">{{ $filtered->count() }}</span>
            </div>
            @if ($filtered->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6 bg-white rounded-xl border border-gray-100">No {{ strtolower($label) }} orders.</p>
            @else
                <div class="space-y-3">
                    @foreach ($filtered as $order)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">#{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d M H:i') }} &middot; Table {{ $order->restaurantTable->table_number ?? '-' }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-3">{{ $order->orderItems->count() }} item(s)</div>
                            <div class="flex gap-2">
                                <a href="{{ route('kasir.orders.show', $order->id) }}" class="flex-1 h-9 rounded-lg border border-gray-200 text-gray-600 text-xs font-medium flex items-center justify-center gap-1 hover:bg-gray-50 transition-colors">
                                    <span class="material-symbols-outlined text-sm">visibility</span> Detail
                                </a>
                                @if ($status == 'pending')
                                    <form method="POST" action="{{ route('kasir.orders.update-status', $order->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="h-9 px-4 rounded-lg bg-blue-500 text-white text-xs font-medium flex items-center gap-1 hover:bg-blue-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">cooking</span> Confirm
                                        </button>
                                    </form>
                                @elseif ($status == 'processing')
                                    <form method="POST" action="{{ route('kasir.orders.update-status', $order->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="h-9 px-4 rounded-lg bg-green-500 text-white text-xs font-medium flex items-center gap-1 hover:bg-green-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">check</span> Complete
                                        </button>
                                    </form>
                                @else
                                    <button onclick="document.getElementById('payment-{{ $order->id }}').showModal()" class="h-9 px-4 rounded-lg bg-primary text-white text-xs font-medium flex items-center gap-1 hover:bg-primary/90 transition-colors">
                                        <span class="material-symbols-outlined text-sm">payments</span> Pay
                                    </button>
                                    <dialog id="payment-{{ $order->id }}" class="rounded-2xl border border-gray-200 shadow-xl p-6 w-full max-w-sm">
                                        <form method="POST" action="{{ route('kasir.orders.payment', $order->id) }}" class="space-y-4">
                                            @csrf @method('PATCH')
                                            <h3 class="text-base font-semibold text-gray-800">Payment for #{{ $order->order_number }}</h3>
                                            <p class="text-sm text-gray-500">Total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                                <select name="payment_method" class="w-full h-10 rounded-xl border border-gray-200 text-sm px-3 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none">
                                                    <option value="cash">Cash</option>
                                                    <option value="qris">QRIS</option>
                                                    <option value="debit">Debit Card</option>
                                                </select>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="this.closest('dialog').close()" class="flex-1 h-10 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50">Cancel</button>
                                                <button type="submit" class="flex-1 h-10 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90">Confirm Payment</button>
                                            </div>
                                        </form>
                                    </dialog>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach
</div>
@endsection
