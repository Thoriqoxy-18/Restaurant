@extends('customer.layouts.app')
@section('title', 'Notifications - Verdant Bistro')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-1">Notifications</h2>
            <p class="text-sm text-gray-500">Stay updated with your orders</p>
        </div>
        <button @click="$store.notifications.markAllRead()" class="text-xs font-medium text-primary hover:text-primary/80 transition-colors">Mark all as read</button>
    </div>

    <template x-if="$store.notifications.items.length === 0">
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">notifications_off</span>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Belum ada notifikasi</h3>
            <p class="text-sm text-gray-400">Notifikasi akan muncul saat ada pembaruan pesanan.</p>
        </div>
    </template>

    <div class="space-y-2">
        <template x-for="notif in $store.notifications.items" :key="notif.id">
            <div class="flex items-start gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm" :class="!notif.read ? 'border-l-4 border-l-primary' : ''">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg" x-text="notif.read ? 'notifications' : 'notifications_active'"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h4 class="text-sm font-semibold text-gray-800" x-text="notif.title" :class="!notif.read ? 'text-gray-900' : 'text-gray-600'"></h4>
                        <span class="text-[11px] text-gray-400 whitespace-nowrap shrink-0" x-text="notif.time"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="notif.message"></p>
                    <button x-show="!notif.read" @click="$store.notifications.markRead(notif.id)" class="text-[11px] font-medium text-primary hover:text-primary/80 mt-1.5 transition-colors">Mark as read</button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
