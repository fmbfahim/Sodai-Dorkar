<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-4" x-data="{
    // Customer Search & Selection State
    customerSearch: '',
    customerSearchResults: [],
    searchLoading: false,
    selectedCustomer: null,
    
    // Add New Customer Modal State
    addCustomerModal: false,
    newCustomer: {
        name: '',
        phone: '',
        area_id: '<?= !empty($assignedAreas[0]['area_id']) ? $assignedAreas[0]['area_id'] : (!empty($availableAreas[0]['id']) ? $availableAreas[0]['id'] : '') ?>',
        zone_id: '',
        point_id: '',
        address_details: ''
    },
    zonesList: [],
    pointsList: [],
    submittingCustomer: false,

    // Cart & Order State
    subtotal: <?= (float)$subtotal ?>,
    deliveryCharge: 30.00,
    paymentMethod: 'cash',
    trxId: '',
    orderNote: '',
    submittingOrder: false,

    get totalAmount() {
        return Math.max(0, this.subtotal + parseFloat(this.deliveryCharge || 0));
    },

    // Search customers via Agent API
    async searchCustomers() {
        const q = this.customerSearch.trim();
        if (q.length < 2) {
            this.customerSearchResults = [];
            return;
        }

        this.searchLoading = true;
        try {
            const res = await fetch('<?= $base ?>/agent/api/customers/search?q=' + encodeURIComponent(q));
            this.customerSearchResults = await res.json();
        } catch (e) {
            console.error(e);
            this.customerSearchResults = [];
        } finally {
            this.searchLoading = false;
        }
    },

    selectCustomer(cust) {
        this.selectedCustomer = cust;
        this.customerSearchResults = [];
        this.customerSearch = '';
        window.showAgentToast('কাস্টমার নির্বাচিত হয়েছে: ' + cust.name, 'success');
    },

    clearCustomer() {
        this.selectedCustomer = null;
        this.customerSearch = '';
    },

    // Load Zones when Area changes
    async loadZones(areaId) {
        this.newCustomer.zone_id = '';
        this.newCustomer.point_id = '';
        this.zonesList = [];
        this.pointsList = [];

        if (!areaId) return;

        try {
            const res = await fetch('<?= $base ?>/agent/api/zones?area_id=' + areaId);
            this.zonesList = await res.json();
        } catch (e) {
            console.error(e);
        }
    },

    // Load Points when Zone changes
    async loadPoints(zoneId) {
        this.newCustomer.point_id = '';
        this.pointsList = [];

        if (!zoneId) return;

        try {
            const res = await fetch('<?= $base ?>/agent/api/points?zone_id=' + zoneId);
            this.pointsList = await res.json();
        } catch (e) {
            console.error(e);
        }
    },

    // Save New Customer
    async saveNewCustomer() {
        if (!this.newCustomer.name.trim() || !this.newCustomer.phone.trim() || !this.newCustomer.area_id) {
            alert('অনুগ্রহ করে নাম, মোবাইল নম্বর এবং এরিয়া পূরণ করুন');
            return;
        }

        this.submittingCustomer = true;
        try {
            const res = await fetch('<?= $base ?>/agent/api/customers/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.newCustomer)
            });
            const data = await res.json();

            if (data.success && data.customer) {
                this.selectedCustomer = data.customer;
                this.addCustomerModal = false;
                window.showAgentToast(data.message || 'নতুন কাস্টমার যুক্ত হয়েছে!', 'success');
                // Reset form
                this.newCustomer.name = '';
                this.newCustomer.phone = '';
                this.newCustomer.address_details = '';
            } else {
                alert(data.message || 'কাস্টমার সংরক্ষণ ব্যর্থ হয়েছে');
            }
        } catch (e) {
            console.error(e);
            alert('নেটওয়ার্ক সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        } finally {
            this.submittingCustomer = false;
        }
    }
}" x-init="if (newCustomer.area_id) { loadZones(newCustomer.area_id); }">

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl shadow-xs text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg text-red-600"></ion-icon>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?= $base ?>/agent/shop" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-emerald-700 transition-colors">
            <ion-icon name="arrow-back"></ion-icon>
            <span>পণ্য তালিকায় ফিরুন</span>
        </a>
        <span class="text-xs font-black text-gray-900 uppercase tracking-wider">অর্ডার কনফার্মেশন</span>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- SECTION 1: CUSTOMER SELECTION (THE CORE FEATURE) -->
    <!-- ─────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl p-4.5 border border-gray-200/90 shadow-xs space-y-3">
        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg font-black">
                    <ion-icon name="person"></ion-icon>
                </div>
                <div>
                    <h2 class="text-sm font-black text-gray-900 leading-tight">কাস্টমার নির্বাচন করুন *</h2>
                    <p class="text-[11px] text-gray-500">যে কাস্টমারের নামে অর্ডার হবে তাকে সিলেক্ট করুন</p>
                </div>
            </div>

            <!-- Quick Add Customer Trigger Button -->
            <button type="button" @click="addCustomerModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1 transition-colors">
                <ion-icon name="person-add"></ion-icon>
                <span>নতুন কাস্টমার</span>
            </button>
        </div>

        <!-- STATE A: No Customer Selected Yet -> Show Search Input -->
        <template x-if="!selectedCustomer">
            <div class="space-y-2">
                <div class="relative">
                    <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()" placeholder="কাস্টমারের ফোন নম্বর বা নাম দিয়ে খুঁজুন..." class="w-full pl-10 pr-10 py-3 bg-gray-50 rounded-2xl border border-gray-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs font-medium transition-all">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-3.5 text-base text-gray-400"></ion-icon>
                    <div x-show="searchLoading" class="absolute right-3.5 top-3.5">
                        <ion-icon name="sync-outline" class="animate-spin text-emerald-600 text-base"></ion-icon>
                    </div>
                </div>

                <!-- Assigned Area Notice -->
                <div class="text-[11px] text-gray-500 flex items-center gap-1">
                    <ion-icon name="location-outline" class="text-emerald-600"></ion-icon>
                    <span>অনুসন্ধান শুধুমাত্র আপনার এসাইন করা এরিয়ার কাস্টমারদের প্রদর্শন করবে।</span>
                </div>

                <!-- Suggestions Dropdown / List -->
                <div x-show="customerSearchResults.length > 0" x-cloak class="bg-white border border-gray-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="cust in customerSearchResults" :key="cust.id">
                        <div @click="selectCustomer(cust)" class="p-3 hover:bg-emerald-50/50 cursor-pointer transition-colors flex items-center justify-between">
                            <div>
                                <div class="text-xs font-black text-gray-900 flex items-center gap-2">
                                    <span x-text="cust.name"></span>
                                    <span class="text-[11px] font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded" x-text="cust.phone"></span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1.5">
                                    <span x-text="cust.area_name || 'এরিয়া'"></span>
                                    <span>•</span>
                                    <span x-text="cust.address_details || 'ঠিকানা নেই'" class="truncate max-w-[180px]"></span>
                                </div>
                            </div>
                            <button type="button" class="px-3 py-1 bg-emerald-600 text-white rounded-xl text-[11px] font-bold shadow-xs">
                                সিলেক্ট
                            </button>
                        </div>
                    </template>
                </div>

                <!-- If typed but no results found -->
                <div x-show="customerSearch.trim().length >= 2 && customerSearchResults.length === 0 && !searchLoading" x-cloak class="p-4 bg-gray-50 rounded-2xl text-center space-y-2 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500">এই নম্বরে কোনো কাস্টমার পাওয়া যায়নি।</p>
                    <button type="button" @click="newCustomer.phone = customerSearch; addCustomerModal = true;" class="inline-flex items-center gap-1 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-xs">
                        <ion-icon name="person-add"></ion-icon>
                        <span>"+ নতুন কাস্টমার হিসেবে যুক্ত করুন"</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- STATE B: Customer Selected -> Show Selected Card -->
        <template x-if="selectedCustomer">
            <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl p-3.5 flex items-start justify-between gap-3 animate-in fade-in duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-base shrink-0 shadow-xs">
                        <ion-icon name="person-circle"></ion-icon>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-emerald-950" x-text="selectedCustomer.name"></h3>
                            <span class="text-[10px] font-mono font-bold bg-white text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200" x-text="selectedCustomer.phone"></span>
                        </div>
                        <div class="text-[11px] text-emerald-800 font-medium mt-1 space-y-0.5">
                            <div>এরিয়া: <strong x-text="selectedCustomer.area_name || 'N/A'"></strong></div>
                            <div x-show="selectedCustomer.address_details">ঠিকানা: <span x-text="selectedCustomer.address_details"></span></div>
                        </div>
                    </div>
                </div>

                <button type="button" @click="clearCustomer()" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-gray-100 text-gray-700 text-[11px] font-bold border border-emerald-200 shadow-2xs transition-colors shrink-0">
                    পরিবর্তন
                </button>
            </div>
        </template>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- SECTION 2: CART ITEMS REVIEW                    -->
    <!-- ─────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl p-4.5 border border-gray-200/90 shadow-xs space-y-3">
        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
            <h2 class="text-sm font-black text-gray-900 flex items-center gap-1.5">
                <ion-icon name="cart-outline" class="text-emerald-600 text-base"></ion-icon>
                <span>অর্ডারকৃত পণ্যসমূহ (<?= count($cart) ?> টি আইটেম)</span>
            </h2>
            <a href="<?= $base ?>/agent/shop" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                + আরও যোগ করুন
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            <?php foreach ($cart as $item): ?>
                <?php
                $itemTotal = (float)$item['price'] * (int)$item['quantity'];
                $img = !empty($item['image_path']) ? $base . '/' . ltrim($item['image_path'], '/') : 'https://placehold.co/100x100/f3f4f6/15803d?text=Product';
                ?>
                <div class="py-2.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <img src="<?= htmlspecialchars($img) ?>" alt="" class="w-10 h-10 object-contain rounded-xl bg-gray-50 border border-gray-100 shrink-0">
                        <div>
                            <h4 class="text-xs font-black text-gray-900 line-clamp-1"><?= htmlspecialchars($item['name']) ?></h4>
                            <div class="text-[11px] text-gray-500 mt-0.5">
                                ৳<?= number_format($item['price'], 0) ?> × <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-black text-gray-900">৳<?= number_format($itemTotal, 2) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- SECTION 3: ORDER FORM SUBMISSION                -->
    <!-- ─────────────────────────────────────────────── -->
    <form action="<?= $base ?>/agent/place-order" method="POST" @submit="submittingOrder = true" class="space-y-4">
        <!-- Hidden selected customer ID -->
        <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''" required>

        <!-- Payment Method Selection -->
        <div class="bg-white rounded-3xl p-4.5 border border-gray-200/90 shadow-xs space-y-3">
            <h2 class="text-sm font-black text-gray-900 flex items-center gap-1.5 border-b border-gray-100 pb-2">
                <ion-icon name="card-outline" class="text-emerald-600 text-base"></ion-icon>
                <span>পেমেন্ট মাধ্যম *</span>
            </h2>

            <div class="grid grid-cols-2 gap-2.5">
                <label class="flex items-center gap-2.5 p-3 rounded-2xl border cursor-pointer transition-all" :class="paymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50/50 text-emerald-950 font-bold' : 'border-gray-200 text-gray-700'">
                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="w-4 h-4 text-emerald-600">
                    <div class="text-xs">
                        <div class="font-black">ক্যাশ অন ডেলিভারি</div>
                        <div class="text-[10px] text-gray-500 font-normal">পণ্য পেয়ে মূল্য পরিশোধ</div>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl border cursor-pointer transition-all" :class="paymentMethod === 'bkash' ? 'border-pink-500 bg-pink-50/50 text-pink-950 font-bold' : 'border-gray-200 text-gray-700'">
                    <input type="radio" name="payment_method" value="bkash" x-model="paymentMethod" class="w-4 h-4 text-pink-600">
                    <div class="text-xs">
                        <div class="font-black">বিকাশ / নগদ</div>
                        <div class="text-[10px] text-gray-500 font-normal">ডিজিটাল পেমেন্ট</div>
                    </div>
                </label>
            </div>

            <div x-show="paymentMethod !== 'cash'" x-cloak class="pt-2">
                <label class="block text-xs font-bold text-gray-700 mb-1">ট্রানজেকশন আইডি (TrxID) / রেফারেন্স</label>
                <input type="text" name="payment_trx_id" x-model="trxId" placeholder="বিকাশ/নগদ TrxID লিখুন (যদি থাকে)" class="w-full px-3.5 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-xs font-mono">
            </div>

            <!-- Optional Agent Note for rider / packaging -->
            <div class="pt-2">
                <label class="block text-xs font-bold text-gray-700 mb-1">অর্ডার নোট / বিশেষ নির্দেশনা (ঐচ্ছিক)</label>
                <input type="text" name="order_note" x-model="orderNote" placeholder="ডেলিভারির সময় বা পণ্য সম্পর্কিত বিশেষ তথ্য..." class="w-full px-3.5 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-xs">
            </div>
        </div>

        <!-- ─────────────────────────────────────────────── -->
        <!-- SECTION 4: BILL SUMMARY & CONFIRM BUTTON        -->
        <!-- ─────────────────────────────────────────────── -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-md space-y-3">
            <h2 class="text-sm font-black text-gray-900 border-b border-gray-100 pb-2">
                মূল্য ও বিলের বিবরণী
            </h2>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between text-gray-600">
                    <span>পণ্যের উপমোট (Subtotal)</span>
                    <span class="font-bold text-gray-900">৳<span x-text="subtotal.toFixed(2)"></span></span>
                </div>

                <div class="flex items-center justify-between text-gray-600">
                    <span>ডেলিভারি চার্জ</span>
                    <span class="font-bold text-emerald-700">৳<span x-text="deliveryCharge.toFixed(2)"></span></span>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-base">
                    <span class="font-black text-gray-900">সর্বমোট প্রদেয় বিল</span>
                    <span class="font-black text-emerald-700 text-lg">৳<span x-text="totalAmount.toFixed(2)"></span></span>
                </div>
            </div>

            <!-- Warning if no customer selected -->
            <template x-if="!selectedCustomer">
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-[11px] font-bold text-amber-800 flex items-center gap-2">
                    <ion-icon name="warning-outline" class="text-base text-amber-600"></ion-icon>
                    <span>অর্ডার কনফার্ম করতে প্রথমে উপরে কাস্টমার নির্বাচন করুন।</span>
                </div>
            </template>

            <!-- Submit Button -->
            <button type="submit" :disabled="!selectedCustomer || submittingOrder" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-black text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 active:scale-95">
                <template x-if="!submittingOrder">
                    <div class="flex items-center gap-2">
                        <ion-icon name="checkmark-done-circle" class="text-xl"></ion-icon>
                        <span>অর্ডার কনফার্ম করুন (Confirm Order)</span>
                    </div>
                </template>
                <template x-if="submittingOrder">
                    <div class="flex items-center gap-2">
                        <ion-icon name="sync-outline" class="animate-spin text-xl"></ion-icon>
                        <span>অর্ডার সম্পন্ন হচ্ছে...</span>
                    </div>
                </template>
            </button>
        </div>
    </form>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL: ADD NEW CUSTOMER FROM AGENT CHECKOUT     -->
    <!-- ─────────────────────────────────────────────── -->
    <div x-show="addCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="addCustomerModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                        <ion-icon name="person-add"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">নতুন কাস্টমার নিবন্ধন</h3>
                        <p class="text-[10px] text-gray-500">আপনার নির্ধারিত এরিয়ার কাস্টমার যুক্ত করুন</p>
                    </div>
                </div>
                <button type="button" @click="addCustomerModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">কাস্টমারের নাম *</label>
                    <input type="text" x-model="newCustomer.name" placeholder="যেমন: মো: কামরুল ইসলাম" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">মোবাইল নম্বর *</label>
                    <input type="text" x-model="newCustomer.phone" placeholder="017xxxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-medium">
                </div>

                <!-- Area Dropdown (Filtered only to Agent's Allowed Areas) -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1">ইউনিয়ন / এরিয়া *</label>
                    <select x-model="newCustomer.area_id" @change="loadZones($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- এরিয়া নির্বাচন করুন --</option>
                        <?php foreach ($availableAreas as $area): ?>
                            <?php 
                            $aId = $area['area_id'] ?? $area['id'];
                            $aName = $area['area_name'] ?? $area['name'];
                            ?>
                            <option value="<?= $aId ?>"><?= htmlspecialchars($aName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Zone / Ward Dropdown -->
                <div x-show="zonesList.length > 0">
                    <label class="block font-bold text-gray-700 mb-1">ওয়ার্ড / জোন (ঐচ্ছিক)</label>
                    <select x-model="newCustomer.zone_id" @change="loadPoints($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- ওয়ার্ড / জোন নির্বাচন করুন --</option>
                        <template x-for="z in zonesList" :key="z.id">
                            <option :value="z.id" x-text="z.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Point / Village Dropdown -->
                <div x-show="pointsList.length > 0">
                    <label class="block font-bold text-gray-700 mb-1">পয়েন্ট / গ্রাম (ঐচ্ছিক)</label>
                    <select x-model="newCustomer.point_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- পয়েন্ট নির্বাচন করুন --</option>
                        <template x-for="p in pointsList" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">বিস্তারিত ঠিকানা (বাড়ি/রোড/ল্যান্ডমার্ক)</label>
                    <textarea x-model="newCustomer.address_details" rows="2" placeholder="যেমন: পশ্চিম পাড়া, করিম মিয়ার বাড়ির পাশে" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="addCustomerModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl font-bold transition-colors">
                        বাতিল
                    </button>
                    <button type="button" @click="saveNewCustomer()" :disabled="submittingCustomer" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md transition-colors flex items-center gap-1.5 disabled:opacity-50">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span x-text="submittingCustomer ? 'সংরক্ষণ হচ্ছে...' : 'কাস্টমার যোগ করুন'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
