import Alpine from 'alpinejs';

const cartData = window.__INITIAL_CART__ || [];

function configKey(item) {
    const optIds = (item._selectedOptionIds || []).sort().join(',');
    return `${item.id}_${optIds}_${item.notes || ''}`;
}

Alpine.store('cart', {
    items: cartData,
    get count() { return this.items.reduce((t, i) => t + (i.quantity || 0), 0); },
    get subtotal() { return this.items.reduce((s, i) => s + (i.finalPrice || i.price || 0) * (i.quantity || 0), 0); },
    get serviceCharge() { return Math.round(this.subtotal * 0.05); },
    get tax() { return Math.round(this.subtotal * 0.1); },
    get total() { return this.subtotal + this.tax + this.serviceCharge; },
    add(item) {
        const ex = this.items.find(i => configKey(i) === configKey(item));
        if (ex) { ex.quantity += item.quantity || 1; }
        else { this.items.push({ id: item.id, name: item.name, price: item.price, image: item.image || null, quantity: item.quantity || 1, notes: item.notes || '', finalPrice: item.finalPrice || item.price, _options: item._options || [], _selectedOptionIds: item._selectedOptionIds || [] }); }
        fetch('/cart/add', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }, body: JSON.stringify({ menu_item_id: item.id, quantity: item.quantity || 1, notes: item.notes, options: JSON.stringify(item._selectedOptionIds) }) }).catch(() => {});
        Alpine.store('toast').show(`${item.name} ditambahkan ke keranjang`, 'success');
    },
    updateQuantity(id, qty, selectedIds, notes) {
        if (qty <= 0) { this.remove(id, selectedIds, notes); return; }
        const item = this.items.find(i => configKey(i) === configKey({ id, _selectedOptionIds: selectedIds, notes }));
        if (item) item.quantity = qty;
    },
    remove(id, selectedIds, notes) { this.items = this.items.filter(i => configKey(i) !== configKey({ id, _selectedOptionIds: selectedIds, notes })); },
    clear() { this.items = []; },
});

Alpine.store('toast', {
    visible: false, message: '', type: 'success', timer: null,
    show(msg, type = 'success') {
        this.message = msg; this.type = type; this.visible = true;
        if (this.timer) clearTimeout(this.timer);
        this.timer = setTimeout(() => { this.visible = false; }, 3000);
    },
});

Alpine.store('ui', {
    cartOpen: false, menuSearch: '', activeCategory: 'all', detailOpen: false, detailItem: null, qty: 1, note: '', currentOptions: [], editMode: false, editItemKey: null,     loaded: true,
    toggleCart() { this.cartOpen = !this.cartOpen; },
    get liveTotal() {
        if (!this.detailItem) return 0;
        let base = this.detailItem.price || 0, extras = 0;
        this.currentOptions.forEach(og => (og.choices || []).forEach(ch => { if (ch.selected) extras += (ch.price_modifier || 0); }));
        return (base + extras) * this.qty;
    },
    get optionSummary() {
        const parts = [];
        this.currentOptions.forEach(og => { const sel = (og.choices || []).filter(ch => ch.selected).map(ch => ch.name); if (sel.length) parts.push(sel.join(', ')); });
        return parts.join(' | ');
    },
    openDetail(item) {
        this.detailItem = item; this.qty = 1; this.note = '';
        this.currentOptions = (item._options || []).map(og => ({ ...og, choices: (og.choices || []).map(ch => ({ ...ch, selected: false })) }));
        this.editMode = false; this.editItemKey = null; this.detailOpen = true; document.body.style.overflow = 'hidden';
    },
    openEdit(item) {
        this.detailItem = { id: item.id, name: item.name, price: item.price, image: item.image, desc: '', _options: item._options || [] };
        this.qty = item.quantity; this.note = item.notes || '';
        const storedIds = item._selectedOptionIds || [];
        this.currentOptions = (item._options || []).map(og => ({ ...og, choices: (og.choices || []).map(ch => ({ ...ch, selected: storedIds.includes(ch.id) })) }));
        this.editMode = true; this.editItemKey = configKey(item); this.detailOpen = true; document.body.style.overflow = 'hidden';
    },
    closeDetail() { this.detailOpen = false; document.body.style.overflow = ''; },
    selectRadio(gi, choiceId) { const g = this.currentOptions[gi]; if (g) (g.choices || []).forEach(ch => { ch.selected = (ch.id === choiceId); }); },
    toggleCheckbox(choiceId) { const all = this.currentOptions.flatMap(og => og.choices || []); const ch = all.find(c => c.id === choiceId); if (ch) ch.selected = !ch.selected; },
    addToCart() {
        if (!this.detailItem) return;
        const cart = Alpine.store('cart');
        const selectedIds = [];
        this.currentOptions.forEach(og => (og.choices || []).forEach(ch => { if (ch.selected) selectedIds.push(ch.id); }));
        const base = this.detailItem.price || 0;
        let extras = 0;
        this.currentOptions.forEach(og => (og.choices || []).forEach(ch => { if (ch.selected) extras += (ch.price_modifier || 0); }));
        const payload = { id: this.detailItem.id, name: this.detailItem.name, price: this.detailItem.price, image: this.detailItem.image, quantity: this.qty, notes: this.note, finalPrice: base + extras, _options: this.detailItem._options || [], _selectedOptionIds: selectedIds };
        if (this.editMode && this.editItemKey) cart.items = cart.items.filter(i => configKey(i) !== this.editItemKey);
        cart.add(payload);
        this.closeDetail();
    },
});

window.Alpine = Alpine;
Alpine.start();
