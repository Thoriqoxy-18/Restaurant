<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 h-14 flex items-center px-4 sm:px-6">
    <div class="flex items-center justify-between w-full max-w-4xl mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm">
                <span class="material-symbols-outlined text-lg">restaurant</span>
            </div>
            <span class="font-semibold text-base sm:text-lg text-gray-800 truncate max-w-[120px] sm:max-w-none">Verdant Bistro</span>
        </a>
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="hidden sm:flex items-center gap-1.5 bg-primary/10 text-primary px-3 py-1.5 rounded-full text-sm font-medium">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">table_restaurant</span>
                <span>{{ session('table_number', '—') }}</span>
            </div>
            <a href="{{ route('notifications') }}" class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors active:scale-90">
                <span class="material-symbols-outlined text-gray-600">notifications</span>
                <template x-if="$store.notifications?.unread > 0">
                    <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full border-2 border-white px-0.5" x-text="$store.notifications.unread"></span>
                </template>
            </a>
        </div>
    </div>
</header>
