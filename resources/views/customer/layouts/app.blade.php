<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet">
    <script>window.__INITIAL_CART__ = @json(session()->get('cart', []));</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen pb-16 sm:pb-0" x-data>
    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 h-14 flex items-center px-4">
        <div class="flex items-center justify-between w-full max-w-5xl mx-auto">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm">
                    <span class="material-symbols-outlined text-lg">restaurant</span>
                </div>
                <span class="font-bold text-base text-gray-800">{{ config('app.name') }}</span>
                @if(session('table_number'))
                <span class="text-xs font-medium text-primary bg-primary/5 px-2 py-0.5 rounded-full ml-1">{{ session('table_number') }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customer.orders') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-gray-600 text-lg">receipt_long</span>
                </a>
                <button @@click="$store.ui.toggleCart()" class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-gray-600 text-lg">shopping_cart</span>
                    <template x-if="$store.cart.count > 0">
                        <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full border-2 border-white" x-text="$store.cart.count"></span>
                    </template>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="pt-14 min-h-screen">
        @yield('content')
    </main>

    {{-- Bottom Navigation (mobile) --}}
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-gray-200 lg:hidden" style="padding-bottom:env(safe-area-inset-bottom,0px)">
        <div class="flex justify-around items-center h-14 max-w-lg mx-auto">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-0.5 min-w-[64px] py-1 {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-400' }}">
                <span class="material-symbols-outlined text-xl" style="{{ request()->routeIs('home') ? 'font-variation-settings:\'FILL\'1;' : '' }}">menu_book</span>
                <span class="text-[10px] font-medium">Menu</span>
            </a>
            <a href="{{ route('customer.orders') }}" class="flex flex-col items-center justify-center gap-0.5 min-w-[64px] py-1 {{ request()->routeIs('customer.orders*') ? 'text-primary' : 'text-gray-400' }}">
                <span class="material-symbols-outlined text-xl" style="{{ request()->routeIs('customer.orders*') ? 'font-variation-settings:\'FILL\'1;' : '' }}">receipt_long</span>
                <span class="text-[10px] font-medium">Orders</span>
            </a>
            <button @@click="$store.ui.toggleCart()" class="flex flex-col items-center justify-center gap-0.5 min-w-[64px] py-1 text-gray-400 relative">
                <span class="material-symbols-outlined text-xl">shopping_cart</span>
                <span class="text-[10px] font-medium">Cart</span>
                <template x-if="$store.cart.count > 0">
                    <span class="absolute -top-0.5 right-1 bg-red-500 text-white text-[9px] font-bold min-w-[16px] h-[16px] flex items-center justify-center rounded-full border-2 border-white" x-text="$store.cart.count"></span>
                </template>
            </button>
        </div>
    </nav>

    {{-- Cart Bottom Sheet --}}
    <div x-show="$store.ui.cartOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/40" @@click="$store.ui.cartOpen = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl flex flex-col max-h-[85vh] bottom-sheet"
             x-transition:enter="transition-transform duration-300 ease-out" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition-transform duration-200 ease-in" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            <div class="shrink-0 flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-200 rounded-full"></div></div>
            <div class="shrink-0 px-5 pb-3 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Pesanan Saya</h2>
                <span class="text-xs text-gray-400" x-text="$store.cart.count + ' item'"></span>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar px-5 py-4 space-y-4">
                <template x-if="$store.cart.items.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL'1;">shopping_basket</span>
                        <p class="text-sm mt-3">Keranjang masih kosong</p>
                    </div>
                </template>
                <template x-for="(item, idx) in $store.cart.items" :key="idx">
                    <div class="flex gap-3.5 pb-4 border-b border-gray-100 last:border-0">
                        <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                            <img :src="item.image || '{{ asset('assets/images/default/no-image.svg') }}'" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-800" x-text="item.name"></p>
                                <button @@click="$store.cart.remove(item.id, item._selectedOptionIds, item.notes)" class="text-gray-300 hover:text-red-400 shrink-0"><span class="material-symbols-outlined text-lg">close</span></button>
                            </div>
                            <template x-for="og in (item._options || [])" :key="og.id">
                                <template x-for="ch in (og.choices || []).filter(c => (item._selectedOptionIds || []).includes(c.id))" :key="ch.id">
                                    <p class="text-xs text-gray-500 mt-0.5"><span class="text-gray-400" x-text="og.group_name + ':'"></span> <span x-text="ch.name"></span></p>
                                </template>
                            </template>
                            <template x-if="item.notes"><p class="text-xs text-gray-500 mt-0.5"><span class="text-gray-400">Catatan:</span> <span class="italic" x-text="item.notes"></span></p></template>
                            <p class="text-xs text-gray-400 mt-1" x-text="'Rp ' + (item.finalPrice || item.price).toLocaleString('id-ID') + ' / porsi'"></p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center bg-gray-100 rounded-xl p-0.5">
                                    <button @@click="$store.cart.updateQuantity(item.id, item.quantity - 1, item._selectedOptionIds, item.notes)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-500 active:scale-90 text-base font-semibold">−</button>
                                    <span class="w-10 text-center text-sm font-bold text-gray-800" x-text="item.quantity"></span>
                                    <button @@click="$store.cart.updateQuantity(item.id, item.quantity + 1, item._selectedOptionIds, item.notes)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-500 active:scale-90 text-base font-semibold">+</button>
                                </div>
                                <span class="text-sm font-bold text-primary" x-text="'Rp ' + ((item.finalPrice || item.price) * item.quantity).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="$store.cart.items.length > 0" class="shrink-0 border-t border-gray-100 px-5 py-4 space-y-2.5 bg-white">
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-500"><span>Subtotal</span><span x-text="'Rp ' + $store.cart.subtotal.toLocaleString('id-ID')"></span></div>
                    <div class="flex justify-between text-gray-500"><span>Pajak 10%</span><span x-text="'Rp ' + $store.cart.tax.toLocaleString('id-ID')"></span></div>
                    <div class="flex justify-between text-gray-500"><span>Service 5%</span><span x-text="'Rp ' + $store.cart.serviceCharge.toLocaleString('id-ID')"></span></div>
                </div>
                <div class="flex justify-between font-bold text-gray-800 pt-2 border-t border-gray-100">
                    <span>Total</span>
                    <span class="text-primary text-lg" x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span>
                </div>
                <a href="{{ route('checkout') }}" class="block w-full h-12 rounded-xl gradient-button text-white font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/25 mt-1">
                    Pesan Sekarang <span class="material-symbols-outlined text-lg">send</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Desktop Cart Panel --}}
    <div x-show="$store.ui.cartOpen" x-cloak class="fixed inset-0 z-50 hidden lg:flex" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/40" @@click="$store.ui.cartOpen = false"></div>
        <div class="absolute right-0 top-0 bottom-0 w-96 bg-white shadow-xl flex flex-col" x-transition:enter="transition-transform duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="shrink-0 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Pesanan Saya</h2>
                <button @@click="$store.ui.cartOpen = false" class="text-gray-400 hover:text-gray-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar px-5 py-4 space-y-4">
                <template x-if="$store.cart.items.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400"><span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL'1;">shopping_basket</span><p class="text-sm mt-3">Keranjang masih kosong</p></div>
                </template>
                <template x-for="(item, idx) in $store.cart.items" :key="idx">
                    <div class="flex gap-3 pb-4 border-b border-gray-100 last:border-0">
                        <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-100"><img :src="item.image || '{{ asset('assets/images/default/no-image.svg') }}'" alt="" class="w-full h-full object-cover"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between gap-1"><p class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></p><button @@click="$store.cart.remove(item.id, item._selectedOptionIds, item.notes)" class="text-gray-300 hover:text-red-400 shrink-0"><span class="material-symbols-outlined text-lg">close</span></button></div>
                            <template x-for="og in (item._options || [])" :key="og.id"><template x-for="ch in (og.choices || []).filter(c => (item._selectedOptionIds || []).includes(c.id))" :key="ch.id"><p class="text-xs text-gray-500 mt-0.5"><span class="text-gray-400" x-text="og.group_name+':'"></span> <span x-text="ch.name"></span></p></template></template>
                            <template x-if="item.notes"><p class="text-xs text-gray-500 mt-0.5"><span class="text-gray-400">Catatan:</span> <span class="italic" x-text="item.notes"></span></p></template>
                            <p class="text-xs text-gray-400 mt-1" x-text="'Rp ' + (item.finalPrice || item.price).toLocaleString('id-ID') + ' / porsi'"></p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center bg-gray-100 rounded-xl p-0.5"><button @@click="$store.cart.updateQuantity(item.id, item.quantity - 1, item._selectedOptionIds, item.notes)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-500 active:scale-90 text-base font-semibold">−</button><span class="w-10 text-center text-sm font-bold text-gray-800" x-text="item.quantity"></span><button @@click="$store.cart.updateQuantity(item.id, item.quantity + 1, item._selectedOptionIds, item.notes)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-500 active:scale-90 text-base font-semibold">+</button></div>
                                <span class="text-sm font-bold text-primary" x-text="'Rp ' + ((item.finalPrice || item.price) * item.quantity).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="$store.cart.items.length > 0" class="shrink-0 border-t border-gray-100 px-5 py-4 space-y-2.5 bg-white">
                <div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span x-text="'Rp ' + $store.cart.subtotal.toLocaleString('id-ID')"></span></div>
                <div class="flex justify-between text-sm text-gray-500"><span>Pajak 10%</span><span x-text="'Rp ' + $store.cart.tax.toLocaleString('id-ID')"></span></div>
                <div class="flex justify-between text-sm text-gray-500"><span>Service 5%</span><span x-text="'Rp ' + $store.cart.serviceCharge.toLocaleString('id-ID')"></span></div>
                <div class="flex justify-between font-bold text-gray-800 pt-2 border-t border-gray-100"><span>Total</span><span class="text-primary text-lg" x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span></div>
                <a href="{{ route('checkout') }}" class="block w-full h-12 rounded-xl gradient-button text-white font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/25 mt-1">Pesan Sekarang <span class="material-symbols-outlined text-lg">send</span></a>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
