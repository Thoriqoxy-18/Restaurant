@extends('admin.layouts.app')
@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← Kembali</a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Order #{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                @elseif($order->status == 'completed') bg-green-100 text-green-700
                @else bg-red-100 text-red-700 @endif
            ">{{ $order->status }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div><span class="text-gray-400">Meja:</span> <span class="font-medium text-gray-800">{{ $order->restaurantTable?->table_number ?? '-' }}</span></div>
            <div><span class="text-gray-400">Customer:</span> <span class="font-medium text-gray-800">{{ $order->customer_name ?? '-' }}</span></div>
            <div><span class="text-gray-400">Pembayaran:</span> <span class="font-medium text-gray-800 capitalize">{{ $order->payment_method ?? '-' }}</span></div>
            <div><span class="text-gray-400">Status Bayar:</span> <span class="font-medium {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ $order->payment_status }}</span></div>
        </div>

        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center gap-3 pt-4 border-t border-gray-100">
            @csrf @method('PUT')
            <select name="status" class="h-10 px-4 rounded-xl border border-gray-200 text-sm">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button class="h-10 px-5 bg-primary text-white rounded-xl text-sm font-medium">Update Status</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Order Items</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b border-gray-100">
                    <th class="pb-2 font-medium">Menu</th>
                    <th class="pb-2 font-medium">Harga</th>
                    <th class="pb-2 font-medium">Qty</th>
                    <th class="pb-2 font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 text-gray-800">{{ $item->menuItem?->name ?? 'Menu #'.$item->menu_item_id }}</td>
                        <td class="py-2.5 text-gray-500">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="py-2.5 text-gray-500">{{ $item->quantity }}</td>
                        <td class="py-2.5 text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="pt-3 text-right text-sm text-gray-500">Subtotal</td><td class="pt-3 text-sm text-gray-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
                <tr><td colspan="3" class="text-right text-sm text-gray-500">Service Fee</td><td class="text-sm text-gray-800">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</td></tr>
                <tr><td colspan="3" class="text-right text-sm text-gray-500">Tax</td><td class="text-sm text-gray-800">Rp {{ number_format($order->tax, 0, ',', '.') }}</td></tr>
                <tr class="font-bold"><td colspan="3" class="text-right text-sm text-gray-800">Total</td><td class="text-sm text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
