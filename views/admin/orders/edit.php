<?php
// views/admin/orders/edit.php
// Clone of create.php with Pre-population logic
?>

<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-6">
    <!-- Left: Product Selection -->
    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
        
        <!-- Header -->
        <div class="p-4 border-b border-secondary-100 flex flex-col gap-3 shrink-0">
            <div class="relative w-full">
                <input type="text" id="productSearch" placeholder="Search product..." class="w-full pl-10 pr-4 py-2 bg-secondary-50 border border-secondary-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                    <ion-icon name="search-outline"></ion-icon>
                </div>
            </div>

            <!-- Dynamic Category Filter -->
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide select-none transition-all" id="categoryFilter"></div>
        </div>
        
        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 bg-secondary-50">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="productsGrid">
                <?php if (!empty($products)): foreach ($products as $p): ?>
                    <div class="product-card bg-white p-2 rounded-xl shadow-sm border border-secondary-100 cursor-pointer hover:border-primary-500 transition-all flex flex-col h-full group"
                         data-category="<?php echo $p['category_id'] ?? ''; ?>"
                         onclick='addToCart(<?php echo json_encode($p); ?>)'>
                         
                         <div class="mb-2 bg-secondary-50 rounded-lg overflow-hidden shrink-0 flex items-center justify-center">
                             <?php 
                             $pImg = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : '/sodai-dorkar/public/images/default-product.svg';
                             ?>
                             <img src="<?php echo $pImg; ?>" style="height: 120px; width: 100%; object-fit: contain;" class="p-1" onerror="this.src='/sodai-dorkar/public/images/default-product.svg'">
                         </div>

                        <h4 class="font-medium text-secondary-800 text-sm line-clamp-2 win-h-[40px] mb-1 flex-1"><?php echo htmlspecialchars($p['name']); ?></h4>
                        <div class="text-xs text-secondary-500 mb-1 hidden"><?php echo $p['code'] ?? ''; ?></div>
                        
                        <div class="flex justify-between items-end mt-1 pt-1 border-t border-dashed border-secondary-100">
                             <div class="flex flex-col">
                                 <span class="text-[10px] text-secondary-400">Stock</span>
                                 <span class="text-xs font-bold text-secondary-700"><?php echo $p['stock_qty']; ?></span>
                             </div>
                             <span class="font-bold text-primary-600">৳ <?php echo number_format($p['sell_price']); ?></span>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
                <div id="noResults" class="hidden col-span-full py-10 flex flex-col items-center justify-center text-secondary-400">
                    <ion-icon name="search-outline" class="text-4xl mb-2 opacity-30"></ion-icon>
                    <p class="text-sm">No products found</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Order Details -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-xl shadow-sm border border-secondary-100 h-full overflow-hidden">
        <div class="p-4 border-b border-secondary-100 bg-secondary-50">
            <div class="flex justify-between items-center mb-1">
                <label class="block text-secondary-600 text-xs font-bold uppercase tracking-wide">Customer (Edit)</label>
            </div>
            
            <div class="relative hidden" id="custSearchWrapper">
                <input type="text" id="customerSearchInput" placeholder="Search ID, Name or Phone..." class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm mb-1">
                <select id="customerSelect" class="hidden">
                     <?php if (!empty($customers)): foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" data-phone="<?php echo $c['phone']; ?>"><?php echo htmlspecialchars($c['name']); ?> - <?php echo $c['phone']; ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <div id="selectedCustomerDisplay" class="bg-white border border-blue-200 bg-blue-50 p-3 rounded-lg mb-2 relative group">
                <button onclick="clearCustomer()" class="absolute top-2 right-2 text-secondary-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm border border-secondary-100 transition-colors" title="Change Customer">
                    <ion-icon name="create-outline"></ion-icon>
                </button>
                <div class="font-bold text-secondary-800 text-sm" id="dispName"><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></div>
                <div class="text-xs text-secondary-500 font-mono mt-0.5" id="dispPhone"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></div>
                <div class="text-[10px] text-blue-600 font-bold mt-1 px-1.5 py-0.5 bg-blue-100 rounded inline-block">EDITING #<?php echo $order['id']; ?></div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-500 sticky top-0 z-10 shadow-sm border-b border-secondary-100 bg-white">
                    <tr>
                        <th class="px-3 py-2">Item</th>
                        <th class="px-2 py-2 text-center w-24">Qty</th>
                        <th class="px-2 py-2 text-right w-20">Price</th>
                        <th class="px-2 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="cartItems" class="divide-y divide-secondary-100"></tbody>
            </table>
            <div id="emptyCartMsg" class="hidden text-center text-secondary-400 py-10 text-sm italic flex flex-col items-center">
                <ion-icon name="cart-outline" class="text-3xl mb-2 opacity-20"></ion-icon>
                Cart empty
            </div>
        </div>

        <div class="p-4 border-t border-secondary-100 bg-secondary-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-secondary-800 font-bold text-lg">Total</span>
                <span class="font-bold text-primary-600 text-xl" id="cartTotal">৳ <?php echo number_format($order['total_amount']); ?></span>
            </div>
            <button onclick="updateOrder()" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg flex justify-center items-center transition-colors shadow-md">
                <span id="btnText">Update Order</span>
                <ion-icon name="save" class="ml-2"></ion-icon> 
            </button>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    const products = <?php echo !empty($products) ? json_encode($products) : '[]'; ?>;
    const allCategories = <?php echo !empty($categories) ? json_encode($categories) : '[]'; ?>;
    
    // Initialize Cart with existing items from DB
    // We map PHP items to JS objects. Note: Order Item usually has 'product_id', 'quantity', 'price'
    // We need 'max' (Stock) from products list to enforce limits. 
    
    // Server items: [{product_id, quantity, price, product_name, ...}]
    const serverItems = <?php echo json_encode($order['items']); ?>;
    
    // Aggregate duplicates from DB (if any existence from prior bug)
    const aggregatedItems = {};
    serverItems.forEach(si => {
        const pid = parseInt(si.product_id);
        if (!aggregatedItems[pid]) {
            aggregatedItems[pid] = { ...si, quantity: 0 };
        }
        aggregatedItems[pid].quantity += parseInt(si.quantity);
        // Keep latest price or valid price
        aggregatedItems[pid].price = parseFloat(si.price); 
    });

    let cart = Object.values(aggregatedItems).map(si => {
        const prod = products.find(p => p.id == si.product_id);
        const maxStock = prod ? parseInt(prod.stock_qty) : 0;
        // Important: When editing, "Available Max" = "Current Order Qty (Summed)" + "Current DB Stock".
        const realMax = maxStock + parseInt(si.quantity);
        
        return {
            id: parseInt(si.product_id),
            name: si.product_name,
            qty: parseInt(si.quantity),
            price: parseFloat(si.price),
            max: realMax 
        };
    });

    let selectedCustId = <?php echo $order['customer_id']; ?>;
    let orderId = <?php echo $order['id']; ?>;
    let filterCatId = 'all';

    // Same functions as Create
    let currentParentId = null;

    function getChildren(parentId) {
        return allCategories.filter(c => {
            if (parentId === null) return !c.parent_id || c.parent_id == 0; 
            return c.parent_id == parentId;
        });
    }

    function renderCategories(parentId = null) {
        currentParentId = parentId;
        const container = document.getElementById('categoryFilter');
        container.innerHTML = '';
        if (parentId !== null) {
            const currentObj = allCategories.find(c => c.id == parentId);
            const grandParentId = currentObj && currentObj.parent_id ? currentObj.parent_id : null;
            const backBtn = document.createElement('button');
            backBtn.className = "cat-btn flex flex-col items-center gap-1 min-w-[66px] group transition-all shrink-0";
            backBtn.onclick = () => renderCategories(grandParentId);
            backBtn.innerHTML = `
                <div style="width: 60px; height: 60px;" class="rounded-full bg-secondary-200 text-secondary-600 flex items-center justify-center shadow-sm"><ion-icon name="arrow-back" class="text-2xl"></ion-icon></div>
                <span class="text-[10px] font-bold text-secondary-600">Back</span>`;
            container.appendChild(backBtn);
        } else {
            const allBtn = document.createElement('button');
            allBtn.className = `cat-btn flex flex-col items-center gap-1 min-w-[66px] group transition-all shrink-0 ${filterCatId === 'all' ? 'active' : 'opacity-70'}`;
            allBtn.onclick = () => { filterCatId = 'all'; renderCategories(null); applyFilters(); };
            allBtn.innerHTML = `
                <div style="width: 60px; height: 60px;" class="rounded-full ${filterCatId === 'all' ? 'bg-primary-600 text-white' : 'bg-secondary-100 text-secondary-500'} flex items-center justify-center shadow-md ring-2 ring-transparent"><ion-icon name="apps" class="text-2xl"></ion-icon></div>
                <span class="text-[10px] font-bold text-secondary-800">All</span>`;
            container.appendChild(allBtn);
        }
        getChildren(parentId).forEach(cat => {
            const hasSub = allCategories.some(c => c.parent_id == cat.id);
            const isActive = filterCatId == cat.id;
            const btn = document.createElement('button');
            btn.className = `cat-btn flex flex-col items-center gap-1 min-w-[66px] group transition-all shrink-0 ${isActive ? 'active opacity-100' : 'opacity-70 hover:opacity-100'}`;
            btn.onclick = () => {
                if (hasSub) renderCategories(cat.id);
                else {
                    filterCatId = cat.id;
                    document.querySelectorAll('.cat-btn > div').forEach(d => d.classList.remove('ring-primary-600', 'ring-offset-2'));
                    btn.querySelector('div').classList.add('ring-2', 'ring-primary-600', 'ring-offset-2');
                    btn.querySelector('div').classList.remove('ring-transparent');
                    applyFilters();
                }
            };
            const imgHtml = cat.image_path ? `<img src="${cat.image_path}" class="w-full h-full object-cover">` : `<span class="text-xl font-bold text-secondary-400 uppercase">${cat.name.charAt(0)}</span>`;
            btn.innerHTML = `<div style="width: 60px; height: 60px;" class="rounded-full bg-secondary-50 border border-secondary-200 overflow-hidden flex items-center justify-center shadow-sm transition-all group-hover:border-primary-400 group-hover:shadow-md ring-2 ring-transparent">${imgHtml}</div><span class="text-[10px] font-medium text-secondary-600 text-center leading-tight max-w-[70px] truncate group-hover:text-primary-600">${cat.name} ${hasSub ? '›' : ''}</span>`;
            container.appendChild(btn);
        });
        if (parentId !== null && filterCatId !== parentId) { filterCatId = parentId; applyFilters(); }
    }
    
    renderCategories(null);
    renderCart(); // Initial Render

    document.getElementById('productSearch').addEventListener('input', applyFilters);
    function applyFilters() {
        const term = document.getElementById('productSearch').value.toLowerCase();
        let allowedCatIds = filterCatId === 'all' ? null : [filterCatId];
        if (allowedCatIds) {
            const stack = [filterCatId];
            while(stack.length > 0) {
                const pid = stack.pop();
                const kids = allCategories.filter(c => c.parent_id == pid).map(c => c.id);
                allowedCatIds.push(...kids);
                stack.push(...kids);
            }
        }
        let visible = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('h4').innerText.toLowerCase();
            const cat = card.dataset.category;
            if ((name.includes(term) || term === '') && (allowedCatIds === null || allowedCatIds.some(id => id == cat))) {
                card.style.display = 'flex'; visible++;
            } else card.style.display = 'none';
        });
        document.getElementById('noResults').className = visible === 0 ? 'col-span-full py-10 flex flex-col items-center justify-center text-secondary-400' : 'hidden'; // Fixed class toggle
    }

    function addToCart(product) {
        const pId = parseInt(product.id);
        const existing = cart.find(i => i.id === pId);
        
        // When adding new product not in editing cart, current stock is max
        const maxStock = parseInt(product.stock_qty); 

        if (existing) { 
             existing.qty++; 
        } else { 
             cart.push({ id: pId, name: product.name, qty: 1, price: parseFloat(product.sell_price), max: maxStock });
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        if (cart.length === 0) {
            container.innerHTML = '';
            document.getElementById('emptyCartMsg').classList.remove('hidden'); // Show empty
            document.getElementById('cartTotal').innerText = '৳ 0.00';
            return;
        }
        document.getElementById('emptyCartMsg').classList.add('hidden');
        container.innerHTML = cart.map(item => `
            <tr class="group hover:bg-secondary-50">
                <td class="px-3 py-2 align-middle text-xs font-medium text-secondary-800 line-clamp-2">${item.name}</td>
                <td class="px-1 py-1 align-middle">
                    <div class="flex items-center justify-center border border-secondary-200 rounded-md overflow-hidden h-7 w-20">
                        <button onclick="changeQty(${item.id}, -1)" class="w-6 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-600"><ion-icon name="remove" class="text-xs"></ion-icon></button>
                        <input type="number" readonly value="${item.qty}" class="w-8 h-full text-center text-xs border-none focus:ring-0 p-0 text-secondary-800 font-bold bg-white">
                        <button onclick="changeQty(${item.id}, 1)" class="w-6 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-600"><ion-icon name="add" class="text-xs"></ion-icon></button>
                    </div>
                </td>
                <td class="px-2 py-2 text-right text-xs align-middle font-medium text-secondary-700">৳ ${(item.price * item.qty).toFixed(0)}</td>
                <td class="px-1 py-1 text-center align-middle"><button onclick="removeItem(${item.id})" class="text-secondary-400 hover:text-red-500 rounded-full p-1"><ion-icon name="trash-outline"></ion-icon></button></td>
            </tr>
        `).join('');
        const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('cartTotal').innerText = '৳ ' + total.toFixed(2);
    }
    
    function changeQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if(item) {
            const newQty = item.qty + delta;
            // Note: item.max is (Allocated + Stock). So we can go up to that.
            if(newQty > item.max) { alert('Stock Limit Reached (Max: '+item.max+')'); return; }
            if(newQty > 0) { item.qty = newQty; renderCart(); }
        }
    }
    function removeItem(id) { cart = cart.filter(i => i.id !== id); renderCart(); }

    // Customer
    const custSearch = document.getElementById('customerSearchInput');
    const custSelect = document.getElementById('customerSelect');
    custSearch.addEventListener('keydown', (e) => {
        if(e.key === 'Enter') {
            e.preventDefault();
            const term = custSearch.value.toLowerCase().trim();
            const match = Array.from(custSelect.options).find(o => o.value && (o.text.toLowerCase().includes(term) || (o.dataset.phone && o.dataset.phone.includes(term)) || o.value == term));
            if(match) selectCustomer(match.value, match.text, match.dataset.phone);
            else alert('Not Found');
        }
    });
    function selectCustomer(id, text, phone) {
        selectedCustId = id;
        document.getElementById('custSearchWrapper').classList.add('hidden');
        document.getElementById('selectedCustomerDisplay').classList.remove('hidden');
        document.getElementById('dispName').innerText = text.split('-')[0].trim();
        document.getElementById('dispPhone').innerText = phone || 'ID: ' + id;
    }
    function clearCustomer() {
        document.getElementById('custSearchWrapper').classList.remove('hidden');
        document.getElementById('selectedCustomerDisplay').classList.add('hidden');
        document.getElementById('customerSearchInput').focus();
    }

    async function updateOrder() {
        if(!selectedCustId) return alert('Select Customer');
        const total = parseFloat(document.getElementById('cartTotal').innerText.replace(/[^\d.-]/g, ''));
        const payload = {
            customer_id: selectedCustId,
            total: total,
            items: cart.map(i => ({ product_id: i.id, quantity: i.qty, price: i.price }))
        };

        const btn = document.querySelector('button[onclick="updateOrder()"]');
        btn.disabled = true; btn.innerText = 'Updating...';

        try {
            const res = await fetch(`/sodai-dorkar/public/admin/orders/update?id=${orderId}`, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '<?= \Core\CSRF::token() ?>'
                }, 
                body: JSON.stringify(payload)
            });
            const text = await res.text();
            let data;
            try { data = JSON.parse(text); } catch(e) { console.error(text); throw new Error(text); }

            if(data.success) window.location.href = data.redirect;
            else { alert(data.error); btn.disabled=false; btn.innerText='Update Order'; }
        } catch(e) { alert('Failed: '+e.message); btn.disabled=false; btn.innerText='Update Order'; }
    }
</script>
