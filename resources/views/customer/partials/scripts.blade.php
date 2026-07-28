{{-- Cart Slide-Over --}}
<div x-show="$store.ui.cartOpen" x-cloak class="fixed inset-0 z-[100]" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$store.ui.cartOpen = false"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl bottom-sheet max-w-2xl mx-auto flex flex-col max-h-[85vh]" x-transition:enter="transition-transform duration-500 ease-out" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition-transform duration-500 ease-in" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
        <div class="shrink-0 flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-200 rounded-full"></div></div>
        <div class="shrink-0 px-4 sm:px-6 pb-3 border-b border-gray-100">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800">Your Cart</h2>
            <p class="text-xs sm:text-sm text-gray-400">Review your selection</p>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar px-4 sm:px-6 py-3 sm:py-4">
            <template x-if="$store.cart.items.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">shopping_basket</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mb-1">Your cart is empty</h3>
                    <p class="text-sm text-gray-400 mb-4">Add items from our menu.</p>
                    <button @click="$store.ui.cartOpen = false" class="px-5 py-2 bg-primary text-white rounded-full text-sm font-semibold active:scale-95 transition-transform shadow-md shadow-primary/20">Browse Menu</button>
                </div>
            </template>
            <template x-for="item in $store.cart.items" :key="item.id"><div class="mb-3">@include('customer.components.cart-item')</div></template>
            <div class="h-4"></div>
        </div>
        <template x-if="$store.cart.items.length > 0">
            <div class="shrink-0 sticky bottom-0 bg-white/95 backdrop-blur-md border-t border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
                <div class="flex justify-between items-center mb-2 sm:mb-3">
                    <span class="text-xs sm:text-sm font-medium text-gray-500">Total</span>
                    <span class="text-base sm:text-lg font-bold text-primary" x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span>
                </div>
                <a href="{{ route('checkout') }}" class="gradient-button w-full h-11 sm:h-12 rounded-full flex items-center justify-between px-4 sm:px-5 text-white text-xs sm:text-sm font-semibold active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                    <span class="flex items-center gap-2"><span class="material-symbols-outlined text-base sm:text-lg">payments</span> Proceed to Checkout</span>
                    <span class="material-symbols-outlined text-base sm:text-lg">chevron_right</span>
                </a>
            </div>
        </template>
    </div>
</div>

{{-- Detail Menu Bottom Sheet --}}
<div x-show="$store.ui.detailOpen" x-cloak class="fixed inset-0 z-[100]" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$store.ui.closeDetail()"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl bottom-sheet max-w-2xl mx-auto flex flex-col max-h-[90vh]" x-transition:enter="transition-transform duration-500 ease-out" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition-transform duration-500 ease-in" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
        <div class="shrink-0 flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-200 rounded-full"></div></div>
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="shrink-0 relative aspect-video w-full bg-gradient-to-br from-primary/5 to-secondary/5 overflow-hidden">
                <img :src="$store.ui.detailItem?.image || '{{ asset('assets/images/default/no-image.svg') }}'" :alt="$store.ui.detailItem?.name" class="w-full h-full object-cover" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
                <div class="absolute top-3 right-3">
                    <button @click="$store.ui.closeDetail()" class="w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm shadow-md text-gray-600 active:scale-90 transition-all">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>
            <div class="px-4 sm:px-6 pt-3 sm:pt-4 pb-2">
                <div class="flex justify-between items-start gap-4 mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 flex-1" x-text="$store.ui.detailItem?.name"></h2>
                    <span class="text-base font-bold text-primary whitespace-nowrap" x-text="'Rp ' + ($store.ui.detailItem?.price || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex items-center gap-1 mb-3">
                    <span class="material-symbols-outlined text-sm text-tertiary" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="text-xs font-medium text-gray-400">4.8</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">Indulge in our signature bite-sized masterpiece.</p>
            </div>
            <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Spicy Level</h3>
                <div class="flex gap-2">
                    <template x-for="level in ['Low', 'Medium', 'High']">
                        <button @click="$store.ui.setSpicy(level)" :class="$store.ui.spicyLevel === level ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-500 hover:bg-gray-50'" class="px-5 py-2 rounded-full text-sm font-medium border transition-colors" x-text="level"></button>
                    </template>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Extra Toppings</h3>
                <div class="space-y-2">
                    <label class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" @change="$store.ui.toggleTopping('Caramelized Onion')" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary/30">
                            <span class="text-sm text-gray-700">Caramelized Onion</span>
                        </div>
                        <span class="text-xs font-medium text-gray-400 whitespace-nowrap">+Rp 5.000</span>
                    </label>
                    <label class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" @change="$store.ui.toggleTopping('Extra Cheese')" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary/30">
                            <span class="text-sm text-gray-700">Extra Cheese</span>
                        </div>
                        <span class="text-xs font-medium text-gray-400 whitespace-nowrap">+Rp 10.000</span>
                    </label>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Special Note</h3>
                <textarea x-model="$store.ui.note" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-primary/20 resize-none h-24" placeholder="Add a note..."></textarea>
            </div>
            <div class="h-4"></div>
        </div>
        <div class="shrink-0 sticky bottom-0 bg-white/95 backdrop-blur-md border-t border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="flex items-center bg-gray-100 rounded-full p-0.5">
                    <button @click="$store.ui.qty = Math.max(1, $store.ui.qty - 1)" class="w-9 sm:w-10 h-9 sm:h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                        <span class="material-symbols-outlined text-base sm:text-lg">remove</span>
                    </button>
                    <span class="w-8 sm:w-10 text-center font-semibold text-sm sm:text-base text-gray-800" x-text="$store.ui.qty"></span>
                    <button @click="$store.ui.qty++" class="w-9 sm:w-10 h-9 sm:h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-600 active:scale-90 transition-transform">
                        <span class="material-symbols-outlined text-base sm:text-lg">add</span>
                    </button>
                </div>
                <button @click="$store.ui.addToCart()" class="flex-1 h-11 sm:h-12 rounded-full gradient-button text-white text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-primary/20">
                    Add to Cart <span class="material-symbols-outlined text-base sm:text-lg">shopping_basket</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Floating Order Summary Bar --}}
<div x-show="$store.cart.items.length > 0" x-cloak class="fixed bottom-[70px] left-3 right-3 bg-gray-900 text-white py-3 px-4 rounded-2xl shadow-xl flex justify-between items-center z-40 max-w-lg mx-auto" x-transition:enter="transition-all duration-500" x-transition:enter-start="translate-y-32 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition-all duration-500" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-32 opacity-0">
    <div class="flex items-center gap-3 min-w-0">
        <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm shrink-0" x-text="$store.cart.count"></div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-400">Subtotal</p>
            <p class="text-sm font-bold leading-none truncate" x-text="'Rp ' + $store.cart.subtotal.toLocaleString('id-ID')"></p>
        </div>
    </div>
    <button @click="$store.ui.toggleCart()" class="bg-primary text-white px-5 py-2 rounded-full text-sm font-semibold flex items-center gap-2 active:scale-95 transition-transform shadow-md shadow-primary/30 shrink-0">
        Checkout <span class="material-symbols-outlined text-lg">shopping_basket</span>
    </button>
</div>
@stack('scripts')
