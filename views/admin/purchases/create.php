<?php
// views/admin/purchases/create.php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$csrfToken = \Core\CSRF::token();
?>
<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-6">
    <!-- Left: Product Selection for Purchase -->
    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
        <div class="p-4 border-b border-secondary-100 flex gap-4">
            <div class="relative flex-1">
                <input type="text" id="productSearch" placeholder="Search product by name or SKU..." class="w-full pl-10 pr-4 py-2 bg-secondary-50 border border-secondary-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                    <ion-icon name="search-outline"></ion-icon>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 bg-secondary-50">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="productsGrid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card bg-white p-3 rounded-xl shadow-sm border border-secondary-100 cursor-pointer hover:border-primary-500 transition-all flex flex-col h-full"
                         onclick='addItem(<?php echo json_encode($p); ?>)'>
                        <h4 class="font-medium text-secondary-800 text-sm line-clamp-2 mb-1 flex-1"><?php echo htmlspecialchars($p['name']); ?></h4>
                        <div class="text-xs text-secondary-500 mb-1 product-sku"><?php echo htmlspecialchars($p['sku'] ?? ''); ?></div>
                        <div class="flex justify-between items-end mt-2">
                             <span class="text-xs text-secondary-600">Stock: <?php echo $p['stock_qty']; ?></span>
                             <span class="font-bold text-primary-600 text-xs">Buy: ৳ <?php echo number_format($p['buy_price']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right: Purchase Details -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-xl shadow-sm border border-secondary-100 h-full overflow-hidden">
        <div class="p-4 border-b border-secondary-100 bg-secondary-50">
            <label class="block text-secondary-600 text-xs font-bold uppercase tracking-wide mb-1">Supplier / Vendor</label>
            <select id="vendorSelect" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm mb-3">
                <option value="">Select Vendor...</option>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="flex gap-2 mb-2">
                 <div class="flex-1">
                    <label class="block text-secondary-600 text-xs font-bold uppercase tracking-wide mb-1">Invoice No</label>
                    <input type="text" id="invoiceNo" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm" placeholder="INV-123">
                 </div>
                 <div class="flex-1">
                    <label class="block text-secondary-600 text-xs font-bold uppercase tracking-wide mb-1">Date</label>
                    <input type="date" id="purchaseDate" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm" value="<?php echo date('Y-m-d'); ?>">
                 </div>
            </div>

            <div class="mb-2">
                <label class="block text-secondary-600 text-xs font-bold uppercase tracking-wide mb-1">Notes</label>
                <textarea id="purchaseNotes" rows="2" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm" placeholder="Optional notes..."></textarea>
            </div>
        </div>

        <!-- Items Table -->
        <div class="flex-1 overflow-y-auto p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-500 sticky top-0">
                    <tr>
                        <th class="px-3 py-2">Item</th>
                        <th class="px-3 py-2 text-center w-16">Qty</th>
                        <th class="px-3 py-2 text-right w-20">Price</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="cartItems" class="divide-y divide-secondary-100">
                    <!-- Items -->
                </tbody>
            </table>
            <div id="emptyCartMsg" class="text-center text-secondary-400 py-8 text-sm italic">No items added</div>
        </div>

        <!-- Totals & Action -->
        <div class="p-4 border-t border-secondary-100 bg-secondary-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-secondary-800 font-bold text-lg">Total</span>
                <span class="font-bold text-primary-600 text-xl" id="cartTotal">৳ 0.00</span>
            </div>
            <button id="savePurchaseBtn" onclick="savePurchase()" class="w-full bg-primary-600 hover:bg-primary-700 active:scale-98 text-white font-bold py-3 px-4 rounded-lg flex justify-center items-center transition-all cursor-pointer shadow-sm">
                <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon> 
                Save Purchase
            </button>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= $base ?>';
    const csrfToken = '<?= $csrfToken ?>';
    let cart = [];

    function addItem(product) {
        // Ensure ID is distinct
        const pId = parseInt(product.id);
        const existing = cart.find(i => i.id === pId);

        if (existing) {
            existing.qty = parseFloat((existing.qty + 1).toFixed(3));
        } else {
            cart.push({
                id: pId,
                name: product.name,
                qty: 1,
                price: parseFloat(product.buy_price) || 0
            });
        }
        renderCart();
    }

    function removeItem(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function updateItem(id, field, value) {
        const item = cart.find(i => i.id === id);
        if (item) {
            if (field === 'qty') item.qty = parseFloat(value) || 0;
            if (field === 'price') item.price = parseFloat(value) || 0;
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        
        if (cart.length === 0) {
            container.innerHTML = '';
            emptyMsg.style.display = 'block';
            document.getElementById('cartTotal').innerText = '৳ 0.00';
            return;
        }

        emptyMsg.style.display = 'none';
        container.innerHTML = cart.map(item => `
            <tr>
                <td class="px-2 py-2">
                    <div class="font-medium text-secondary-800 line-clamp-1">${item.name}</div>
                </td>
                <td class="px-1 py-1">
                    <input type="number" value="${item.qty}" min="0.001" step="any" onchange="updateItem(${item.id}, 'qty', this.value)" class="w-14 px-1 py-1 border border-secondary-200 rounded text-center text-xs">
                </td>
                <td class="px-1 py-1">
                     <input type="number" value="${item.price}" min="0" step="any" onchange="updateItem(${item.id}, 'price', this.value)" class="w-20 px-1 py-1 border border-secondary-200 rounded text-right text-xs">
                </td>
                <td class="px-1 py-1 text-center">
                    <button onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700 cursor-pointer">
                        <ion-icon name="close-circle"></ion-icon>
                    </button>
                </td>
            </tr>
        `).join('');

        const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('cartTotal').innerText = '৳ ' + total.toFixed(2);
    }

    async function savePurchase() {
        const vendorId = document.getElementById('vendorSelect').value;
        const invoiceNo = document.getElementById('invoiceNo').value.trim();
        const purchaseDate = document.getElementById('purchaseDate').value;
        const notes = document.getElementById('purchaseNotes').value.trim();
        const saveBtn = document.getElementById('savePurchaseBtn');
        
        if (!vendorId) return alert('সরবরাহকারী / ভেন্ডর সিলেক্ট করুন');
        if (!invoiceNo) return alert('ইনভয়েস নম্বর লিখুন');
        if (cart.length === 0) return alert('কমপক্ষে একটি পণ্য যোগ করুন');

        const payload = {
            csrf_token: csrfToken,
            vendor_id: vendorId,
            invoice_no: invoiceNo,
            purchase_date: purchaseDate,
            total_amount: cart.reduce((sum, item) => sum + (item.price * item.qty), 0),
            notes: notes,
            items: cart.map(i => ({
                product_id: i.id,
                quantity: i.qty,
                unit_price: i.price
            }))
        };

        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⟳</span> সংরক্ষণ হচ্ছে...';
        }

        try {
            const res = await fetch(`${baseUrl}/admin/purchases/store`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error('Server response:', text);
                throw new Error('সার্ভার থেকে সঠিক ফরম্যাটে রেসপন্স পাওয়া যায়নি');
            }
            
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                const errMsg = data.error || data.message || 'ক্রয় চালান সংরক্ষণ করতে সমস্যা হয়েছে';
                alert('ত্রুটি: ' + errMsg);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon> Save Purchase';
                }
            }
        } catch (e) {
            console.error(e);
            alert('ব্যর্থ হয়েছে: ' + e.message);
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon> Save Purchase';
            }
        }
    }

    // Search Filter
    document.getElementById('productSearch').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = (card.querySelector('h4')?.innerText || '').toLowerCase();
            const sku = (card.querySelector('.product-sku')?.innerText || '').toLowerCase();
            card.style.display = (!term || name.includes(term) || sku.includes(term)) ? 'flex' : 'none';
        });
    });
</script>
