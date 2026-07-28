<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-t border-gray-100 shadow-[0px_-4px_20px_rgba(0,0,0,0.05)]" style="padding-bottom: env(safe-area-inset-bottom, 0px)">
    <div class="flex justify-around items-center h-14 sm:h-16 max-w-4xl mx-auto px-4">
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-transform min-w-[56px] py-1 {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-400' }}">
            <span class="material-symbols-outlined text-[20px] sm:text-lg" style="{{ request()->routeIs('home') ? "font-variation-settings: 'FILL' 1;" : '' }}">menu_book</span>
            <span class="text-[10px] sm:text-[11px] font-medium">Menu</span>
        </a>
        <a href="{{ route('orders') }}" class="flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-transform min-w-[56px] py-1 {{ request()->routeIs('orders') ? 'text-primary' : 'text-gray-400' }}">
            <span class="material-symbols-outlined text-[20px] sm:text-lg" style="{{ request()->routeIs('orders') ? "font-variation-settings: 'FILL' 1;" : '' }}">receipt_long</span>
            <span class="text-[10px] sm:text-[11px] font-medium">Orders</span>
        </a>
        <a href="{{ route('cart') }}" class="flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-transform min-w-[56px] py-1 {{ request()->routeIs('cart') ? 'text-primary' : 'text-gray-400' }}">
            <span class="material-symbols-outlined text-[20px] sm:text-lg" style="{{ request()->routeIs('cart') ? "font-variation-settings: 'FILL' 1;" : '' }}">shopping_cart</span>
            <span class="text-[10px] sm:text-[11px] font-medium">Cart</span>
        </a>
    </div>
</nav>
