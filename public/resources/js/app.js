import Alpine from 'alpinejs';

function getCookie(name) {
    const match = document.cookie.match(new RegExp('XSRF-TOKEN=([^;]+)'));
    return match ? decodeURIComponent(match[1]) : null;
}

async function apiPost(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify(data),
    });
    return response.json();
}

const cartData = window.__INITIAL_CART__ || [];

Alpine.store('cart', {
    items: cartData,
    get count() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    },
    get subtotal() {
        return this.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
    },
    get serviceFee() {
        return Math.round(this.subtotal * 0.05);
    },
    get tax() {
        return Math.round(this.subtotal * 0.1);
    },
    get total() {
        return this.subtotal + this.serviceFee + this.tax;
    },
    async add(item) {
        const existing = this.items.find(i => i.id === item.id);
        if (existing) {
            existing.quantity += item.quantity || 1;
        } else {
            this.items.push({ ...item, quantity: item.quantity || 1, notes: '' });
        }
        try {
            await apiPost('/cart/add', { menu_item_id: item.id, quantity: item.quantity || 1 });
        } catch (e) {
            console.error('Cart sync error:', e);
        }
    },
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
        apiPost('/cart/remove', { menu_item_id: id }).catch(e => console.error('Cart sync error:', e));
    },
    async updateQuantity(id, quantity) {
        const item = this.items.find(i => i.id === id);
        if (item) {
            if (quantity <= 0) {
                this.remove(id);
                return;
            }
            item.quantity = quantity;
            try {
                await apiPost('/cart/update', { menu_item_id: id, quantity });
            } catch (e) {
                console.error('Cart sync error:', e);
            }
        }
    },
    clear() {
        this.items = [];
    },
});

Alpine.store('ui', {
    cartOpen: false,
    menuSearch: '',
    activeCategory: 'all',
    detailOpen: false,
    detailItem: null,
    qty: 1,
    note: '',
    spicyLevel: 'Medium',
    toppings: [],
    toggleCart() {
        this.cartOpen = !this.cartOpen;
    },
    openDetail(item) {
        this.detailItem = item;
        this.qty = 1;
        this.note = '';
        this.spicyLevel = 'Medium';
        this.toppings = [];
        this.detailOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeDetail() {
        this.detailOpen = false;
        document.body.style.overflow = '';
    },
    addToCart() {
        if (this.detailItem) {
            const store = Alpine.store('cart');
            store.add({ ...this.detailItem, quantity: this.qty });
            this.closeDetail();
        }
    },
    toggleTopping(name) {
        const idx = this.toppings.indexOf(name);
        if (idx > -1) {
            this.toppings.splice(idx, 1);
        } else {
            this.toppings.push(name);
        }
    },
    setSpicy(level) {
        this.spicyLevel = level;
    },

    setCategory(slug) {
        this.activeCategory = slug;
        this.filterMenu();
    },

    filterMenu() {
        const query = (this.menuSearch || '').toLowerCase().trim();
        const category = this.activeCategory || 'all';
        const grid = document.getElementById('menu-grid');
        const empty = document.getElementById('search-empty');
        if (! grid) return;

        const cards = grid.querySelectorAll('.menu-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const cat = card.dataset.category || '';
            const matchSearch = query === '' || name.includes(query);
            const matchCategory = category === 'all' || cat === category;
            const visible = matchSearch && matchCategory;
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (empty) {
            if (visibleCount === 0) {
                empty.classList.remove('hidden');
                const msg = document.getElementById('empty-message');
                if (msg) {
                    msg.textContent = query
                        ? 'Tidak ada menu "' + query + '" di kategori ini.'
                        : 'Belum ada menu di kategori ini.';
                }
            } else {
                empty.classList.add('hidden');
            }
        }
    },
});

Alpine.store('order', {
    status: 'processing',
    items: [],
    get subtotal() {
        return this.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
    },
    get tax() {
        return Math.round(this.subtotal * 0.1);
    },
    get deliveryFee() {
        return 8275;
    },
    get total() {
        return this.subtotal + this.tax + this.deliveryFee;
    },
});

window.Alpine = Alpine;
Alpine.start();
