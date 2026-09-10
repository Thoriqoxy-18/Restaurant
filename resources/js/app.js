import Alpine from 'alpinejs';

// ==== Cart Persistence ====
function loadCart() {
    try { return JSON.parse(localStorage.getItem('vb_cart')) || []; } catch { return []; }
}
function saveCart(items) {
    localStorage.setItem('vb_cart', JSON.stringify(items));
}

// ==== Debounce utility ====
function debounce(fn, ms = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
}

// ==== Toast Store ====
Alpine.store('toast', {
    visible: false, message: '', type: 'success', timer: null,
    show(msg, type = 'success', duration = 2500) {
        this.message = msg; this.type = type; this.visible = true;
        if (this.timer) clearTimeout(this.timer);
        this.timer = setTimeout(() => { this.visible = false; }, duration);
    },
});

// ==== Order Status Banner (customer, compact, in-flow di bawah header) ====
Alpine.store('orderBanner', {
    visible: false,
    timer: null,
    _current: null,
    CONFIG: {
        confirmed: { icon: '✅', title: 'Pesanan Diterima', desc: 'Pesananmu sudah diterima dan akan segera diproses.', bar: 'bg-green-500', box: 'bg-white border-green-200', titleCls: 'text-green-700' },
        preparing: { icon: '🍳', title: 'Pesanan Sedang Diproses', desc: 'Pesananmu sedang disiapkan oleh dapur.', bar: 'bg-amber-500', box: 'bg-white border-amber-200', titleCls: 'text-amber-700' },
        served: { icon: '🍽️', title: 'Pesanan Siap Diambil', desc: 'Pesananmu sudah siap. Silakan ambil di kasir.', bar: 'bg-blue-500', box: 'bg-white border-blue-200', titleCls: 'text-blue-700' },
        completed: { icon: '✅', title: 'Pesanan Selesai', desc: 'Pesananmu sudah selesai. Terima kasih sudah memesan!', bar: 'bg-emerald-500', box: 'bg-white border-emerald-200', titleCls: 'text-emerald-700' },
        cancelled: { icon: '❌', title: 'Pesanan Dibatalkan', desc: 'Pesananmu telah dibatalkan.', bar: 'bg-red-500', box: 'bg-white border-red-200', titleCls: 'text-red-700' },
    },
    get cfg() { return this._current || this.CONFIG.confirmed; },
    show(status) {
        this._current = this.CONFIG[status] || this.CONFIG.confirmed;
        this._open();
    },
    showCustom(icon, title, desc, opts = {}) {
        this._current = {
            icon, title, desc,
            bar: opts.bar || 'bg-green-500',
            box: opts.box || 'bg-white border-green-200',
            titleCls: opts.titleCls || 'text-green-700',
        };
        this._open();
    },
    _open() {
        this.visible = true;
        if (this.timer) clearTimeout(this.timer);
        this.timer = setTimeout(() => { this.visible = false; }, 6000);
    },
});

// ==== Cart Store ====
Alpine.store('cart', {
    items: loadCart(),
    cartOpen: false,
    modalOpen: false,

    _save() { saveCart(this.items); },

    get count() { return this.items.reduce((t, i) => t + (i.quantity || 0), 0); },
    countById(id) {
        return this.items.filter(i => i.id === id).reduce((t, i) => t + (i.quantity || 0), 0);
    },
    get subtotal() {
        return this.items.reduce((s, i) => {
            const base = i.price || 0;
            const vPrice = i._variationPrice || 0;
            const tPrice = (i._toppingPrices || []).reduce((a, b) => a + b, 0);
            const sPrice = (i._saucePrices || []).reduce((a, b) => a + b, 0);
            return s + (base + vPrice + tPrice + sPrice) * (i.quantity || 1);
        }, 0);
    },
    get tax() { return Math.round(this.subtotal * 0.05 * 100) / 100; },
    get serviceCharge() { return Math.round(this.subtotal * 0.05 * 100) / 100; },
    get total() { return Math.round((this.subtotal + this.tax + this.serviceCharge) * 100) / 100; },
    get itemsCount() { return this.items.length; },

    add(item) {
        const key = this._key(item);
        const ex = this.items.find(i => this._key(i) === key);
        if (ex) {
            ex.quantity += item.quantity || 1;
        } else {
            this.items.push({
                id: item.id, name: item.name, price: item.price, image: item.image,
                quantity: item.quantity || 1, notes: item.notes || '',
                variationId: item.variationId || null, variationName: item.variationName || null,
                _variationPrice: item._variationPrice || 0,
                toppingIds: item.toppingIds || [], toppingNames: item.toppingNames || [],
                _toppingPrices: item._toppingPrices || [],
                sauceIds: item.sauceIds || [], sauceNames: item.sauceNames || [],
                _saucePrices: item._saucePrices || [],
                _variations: item._variations || [], _toppings: item._toppings || [], _sauces: item._sauces || [],
            });
        }
        this._save();
        Alpine.store('toast').show(item.name + ' ditambahkan ke keranjang', 'success');
    },

    remove(id, variationId) {
        const item = this.items.find(i => i.id === id && (i.variationId || null) === (variationId || null));
        this.items = this.items.filter(i => !(i.id === id && (i.variationId || null) === (variationId || null)));
        this._save();
        if (item) Alpine.store('toast').show(item.name + ' dihapus dari keranjang', 'info');
    },

    updateQty(id, variationId, delta) {
        const item = this.items.find(i => i.id === id && (i.variationId || null) === (variationId || null));
        if (item) {
            const newQty = item.quantity + delta;
            if (newQty <= 0) {
                this.remove(id, variationId);
            } else {
                item.quantity = newQty;
                this._save();
                Alpine.store('toast').show('Jumlah ' + item.name + ' diubah', 'info', 1500);
            }
        }
    },

    decrementFirst(id) {
        const item = this.items.find(i => i.id === id);
        if (item) this.updateQty(id, item.variationId, -1);
    },

    getItemTotal(item) {
        const base = item.price || 0;
        const vPrice = item._variationPrice || 0;
        const tPrice = (item._toppingPrices || []).reduce((a, b) => a + b, 0);
        const sPrice = (item._saucePrices || []).reduce((a, b) => a + b, 0);
        return (base + vPrice + tPrice + sPrice) * (item.quantity || 1);
    },

    _key(item) {
        const v = item.variationId || 'none';
        const t = (item.toppingIds || []).sort().join(',');
        const s = (item.sauceIds || []).sort().join(',');
        const n = (item.notes || '').trim();
        return `${item.id}_${v}_${t}_${s}_${n}`;
    },

    clear() { this.items = []; this._save(); localStorage.removeItem('vb_cart'); },

    toJSON() {
        return this.items.map(i => ({
            id: i.id, quantity: i.quantity, notes: i.notes,
            variation_id: i.variationId,
            topping_ids: i.toppingIds,
            sauce_ids: i.sauceIds,
        }));
    },
});

// ==== UI Store (Sort/Filter) ====
Alpine.store('ui', {
    cards: [],
    visibleMap: {},
    _grid: null,
    _sortTimeout: null,

    registerCard(card) {
        this.cards.push(card);
        this.visibleMap[card.id] = true;
    },

    // Debounced sort to avoid excessive re-renders
    sortMenu: debounce(function() {
        if (!this._grid) this._grid = document.getElementById('menu-grid');
        const grid = this._grid;
        if (!grid) return;

        // Find Alpine data from grid
        const search = (Alpine.raw(document.querySelector('[x-data]')?.__x?.$data)?.search || '').toLowerCase().trim();
        const activeCat = Alpine.raw(document.querySelector('[x-data]')?.__x?.$data)?.activeCat || 'all';
        const sortBy = Alpine.raw(document.querySelector('[x-data]')?.__x?.$data)?.sortBy || '';

        let filtered = this.cards.filter(c => {
            const matchCat = activeCat === 'all' || c.cat === activeCat;
            const matchSearch = search === '' || c.name.includes(search);
            return matchCat && matchSearch;
        });

        if (sortBy === 'bestseller') filtered = filtered.filter(c => c.bestseller);
        else if (sortBy === 'promo') filtered = filtered.filter(c => c.promo);
        else if (sortBy === 'rating') filtered.sort((a, b) => b.rating - a.rating);
        else if (sortBy === 'price_asc') filtered.sort((a, b) => a.price - b.price);
        else if (sortBy === 'price_desc') filtered.sort((a, b) => b.price - a.price);
        else filtered.sort((a, b) => (b.bestseller ? 1 : 0) - (a.bestseller ? 1 : 0));

        this.visibleMap = {};
        const ids = new Set(filtered.map(c => c.id));
        filtered.forEach(c => { this.visibleMap[c.id] = true; });
        this.cards.forEach(c => { if (!ids.has(c.id)) this.visibleMap[c.id] = false; });

        // Batch DOM reorder (minimize reflows)
        const els = {};
        grid.querySelectorAll('.menu-card').forEach(el => {
            const id = parseInt(el.dataset.id);
            if (id) els[id] = el;
        });

        requestAnimationFrame(() => {
            filtered.forEach((c, i) => {
                const el = els[c.id];
                if (el) {
                    el.style.display = '';
                    const ref = grid.children[i];
                    if (ref && ref !== el) grid.insertBefore(el, ref);
                    else if (!ref && el.parentNode) grid.appendChild(el);
                }
            });
            this.cards.forEach(c => {
                if (!ids.has(c.id) && els[c.id]) els[c.id].style.display = 'none';
            });
        });
    }, 200),
});

// ==== Body scroll lock (count-based, supports multiple overlays) ====
let _bodyLockCount = 0;
function lockBody() {
    _bodyLockCount++;
    document.body.style.overflow = 'hidden';
}
function unlockBody() {
    _bodyLockCount = Math.max(0, _bodyLockCount - 1);
    if (_bodyLockCount === 0) document.body.style.overflow = '';
}
window.lockBody = lockBody;
window.unlockBody = unlockBody;

// ==== Overlay / Back-button manager ====
// Push a history entry when an overlay (sheet/modal) opens so the phone's
// back button closes the overlay instead of leaving the page.
const _overlayStack = [];
window.__overlayOpen = (name) => {
    _overlayStack.push(name);
    try { history.pushState({ __overlay: name }, ''); } catch (e) {}
};
window.__overlayClose = (name) => {
    const i = _overlayStack.indexOf(name);
    if (i !== -1) _overlayStack.splice(i, 1);
};
window.addEventListener('popstate', () => {
    const name = _overlayStack.pop();
    if (name) window.dispatchEvent(new CustomEvent('overlay-back', { detail: name }));
});

// ==== Floating active-order indicator (menu customer) ====
function orderIndicator(orderId, status, payment, itemCount, orderCount, statusUrl, pollUrl, orderNumber) {
    const STATUS = {
        pending:    { label: 'Menunggu Pesanan',       dot: '🕐', cls: 'bg-amber-100 text-amber-700' },
        confirmed:  { label: 'Pesanan Diterima',       dot: '✅', cls: 'bg-green-100 text-green-700' },
        preparing:  { label: 'Pesanan Sedang Diproses', dot: '🕐', cls: 'bg-amber-100 text-amber-700' },
        served:     { label: 'Pesanan Siap Diambil',   dot: '🍽️', cls: 'bg-blue-100 text-blue-700' },
        completed:  { label: 'Pesanan Telah Selesai',  dot: '✅', cls: 'bg-gray-100 text-gray-600' },
        cancelled:  { label: 'Pesanan Dibatalkan',     dot: '❌', cls: 'bg-red-100 text-red-600' },
    };
    return {
        orderId, status, payment, itemCount, orderCount, statusUrl, pollUrl, orderNumber,
        visible: true,
        lastNotified: null,
        init() {
            setInterval(() => {
                fetch(this.pollUrl)
                    .then(r => r.json())
                    .then(d => {
                        if (d.status && d.status !== this.status) {
                            this.status = d.status;
                            Alpine.store('orderBanner').show(this.status);
                            if (this.status === 'cancelled') this.visible = false;
                        }
                        if (d.payment_status && d.payment_status !== this.payment) {
                            this.payment = d.payment_status;
                            if (this.payment === 'paid') {
                                Alpine.store('orderBanner').showCustom('✅', 'Pembayaran Diterima', 'Pembayaran untuk order #' + this.orderNumber + ' sudah dikonfirmasi oleh kasir.', { bar: 'bg-green-500', box: 'bg-white border-green-200', titleCls: 'text-green-700' });
                            }
                        }
                    })
                    .catch(() => {});
            }, 4000);
        },
        get meta() { return STATUS[this.status] || STATUS.pending; },
        get title() { return this.orderCount > 1 ? this.orderCount + ' Pesanan Aktif' : 'Pesanan Anda'; },
        get paymentLine() {
            if (this.payment === 'paid') return 'Pembayaran Diterima';
            if (this.payment === 'unpaid') return 'Menunggu Pembayaran';
            return '';
        },
        get bottomStyle() {
            return Alpine.store('cart').items.length > 0
                ? 'calc(72px + env(safe-area-inset-bottom, 0px))'
                : 'calc(12px + env(safe-area-inset-bottom, 0px))';
        }
    };
}

window.orderIndicator = orderIndicator;

window.Alpine = Alpine;
Alpine.start();
