<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 flex gap-3 items-start">
    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gradient-to-br from-primary/5 to-secondary/5">
        <img src="{{ asset('assets/images/default/no-image.svg') }}" alt="" class="w-full h-full object-cover" loading="lazy">
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start mb-1">
            <h3 class="text-sm font-semibold text-gray-800 truncate pr-2" x-text="item.name"></h3>
            <button @click="$store.cart.remove(item.id)" class="text-gray-300 hover:text-red-400 transition-colors shrink-0">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>
        <p class="text-sm font-semibold text-primary mb-2" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
        <div class="flex items-center justify-between">
            <div class="flex items-center bg-gray-100 rounded-full px-1 py-0.5">
                <button @click="$store.cart.updateQuantity(item.id, item.quantity - 1)" class="w-7 h-7 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-sm">remove</span>
                </button>
                <span class="w-8 text-center text-sm font-semibold text-gray-800" x-text="item.quantity"></span>
                <button @click="$store.cart.updateQuantity(item.id, item.quantity + 1)" class="w-7 h-7 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-sm">add</span>
                </button>
            </div>
            <button class="flex items-center gap-1 text-xs text-gray-400 hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-sm">edit_note</span>
                <span>Notes</span>
            </button>
        </div>
    </div>
</div>
