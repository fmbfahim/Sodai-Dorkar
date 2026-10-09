<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-4" x-data="{
    addCustomerModal: false,
    newCustomer: {
        name: '',
        phone: '',
        area_id: '<?= !empty($assignedAreas[0]['area_id']) ? $assignedAreas[0]['area_id'] : '' ?>',
        zone_id: '',
        point_id: '',
        address_details: ''
    },
    zonesList: [],
    pointsList: [],
    submittingCustomer: false,

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
            if (data.success) {
                alert(data.message || 'নতুন কাস্টমার সফলভাবে যুক্ত হয়েছে!');
                window.location.reload();
            } else {
                alert(data.message || 'কাস্টমার সংরক্ষণ ব্যর্থ হয়েছে');
            }
        } catch (e) {
            console.error(e);
            alert('নেটওয়ার্ক ত্রুটি। আবার চেষ্টা করুন।');
        } finally {
            this.submittingCustomer = false;
        }
    }
}" x-init="if (newCustomer.area_id) { loadZones(newCustomer.area_id); }">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">গ্রাহক তালিকা</h1>
            <p class="text-xs text-gray-500">আপনার নির্ধারিত কর্ম-এরিয়ার কাস্টমারদের তালিকা</p>
        </div>
        <button type="button" @click="addCustomerModal = true" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors">
            <ion-icon name="person-add"></ion-icon>
            <span>নতুন গ্রাহক</span>
        </button>
    </div>

    <!-- Search Bar -->
    <form action="<?= $base ?>/agent/customers" method="GET" class="relative">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="গ্রাহকের নাম বা মোবাইল নম্বর দিয়ে খুঁজুন..." class="w-full pl-10 pr-10 py-2.5 bg-white rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-xs text-xs font-medium">
        <ion-icon name="search-outline" class="absolute left-3.5 top-3 text-lg text-gray-400"></ion-icon>
        <?php if (!empty($search)): ?>
            <a href="<?= $base ?>/agent/customers" class="absolute right-3.5 top-2.5 text-gray-400 hover:text-gray-600 text-lg">
                <ion-icon name="close-circle"></ion-icon>
            </a>
        <?php endif; ?>
    </form>

    <!-- Assigned Areas Pill Notice -->
    <?php if (!empty($assignedAreas)): ?>
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs py-1">
            <span class="text-gray-400 font-bold shrink-0">এসাইন এরিয়া:</span>
            <?php foreach ($assignedAreas as $area): ?>
                <span class="px-2.5 py-0.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold shrink-0">
                    <?= htmlspecialchars($area['area_name']) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Customer Cards List -->
    <div class="space-y-3">
        <?php if (empty($customers)): ?>
            <div class="bg-white rounded-3xl p-10 text-center text-gray-400 border border-gray-100 space-y-2">
                <ion-icon name="people-outline" class="text-4xl text-gray-300"></ion-icon>
                <p class="text-xs font-bold text-gray-600">কোনো গ্রাহক পাওয়া যায়নি।</p>
                <p class="text-[11px] text-gray-400">নতুন গ্রাহক যোগ করতে ওপরের বাটনে চাপুন।</p>
                <button type="button" @click="addCustomerModal = true" class="mt-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">
                    + নতুন গ্রাহক যোগ করুন
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($customers as $cust): ?>
                <div class="bg-white rounded-3xl p-4 border border-gray-200/80 shadow-xs hover:border-emerald-300 transition-all space-y-2.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-black text-sm shrink-0">
                                <?= strtoupper(mb_substr($cust['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900"><?= htmlspecialchars($cust['name']) ?></h3>
                                <div class="text-xs font-mono text-emerald-700 font-bold mt-0.5 flex items-center gap-1">
                                    <ion-icon name="call-outline"></ion-icon>
                                    <a href="tel:<?= htmlspecialchars($cust['phone']) ?>" class="hover:underline"><?= htmlspecialchars($cust['phone']) ?></a>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($cust['unique_code'])): ?>
                            <span class="text-[10px] font-mono font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-lg shrink-0">
                                <?= htmlspecialchars($cust['unique_code']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="text-[11px] text-gray-600 space-y-0.5 pt-1 border-t border-gray-100">
                        <div>ইউনিয়ন / এরিয়া: <strong class="text-gray-900"><?= htmlspecialchars($cust['area_name'] ?? 'N/A') ?></strong></div>
                        <?php if (!empty($cust['address_details'])): ?>
                            <div class="text-gray-500">ঠিকানা: <?= htmlspecialchars($cust['address_details']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <a href="tel:<?= htmlspecialchars($cust['phone']) ?>" class="p-2 rounded-xl bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 text-xs font-bold transition-colors flex items-center gap-1">
                            <ion-icon name="call"></ion-icon>
                            <span>কল করুন</span>
                        </a>

                        <a href="<?= $base ?>/agent/shop" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1 transition-colors">
                            <span>অর্ডার নিন</span>
                            <ion-icon name="cart"></ion-icon>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL: ADD CUSTOMER                             -->
    <!-- ─────────────────────────────────────────────── -->
    <div x-show="addCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="addCustomerModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                        <ion-icon name="person-add"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">নতুন গ্রাহক যোগ করুন</h3>
                        <p class="text-[10px] text-gray-500">ফিল্ড কাস্টমার রেজিস্ট্রেশন</p>
                    </div>
                </div>
                <button type="button" @click="addCustomerModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">গ্রাহকের পূর্ণ নাম *</label>
                    <input type="text" x-model="newCustomer.name" placeholder="যেমন: মো: আল আমিন" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">মোবাইল নম্বর *</label>
                    <input type="text" x-model="newCustomer.phone" placeholder="017xxxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">ইউনিয়ন / এরিয়া *</label>
                    <select x-model="newCustomer.area_id" @change="loadZones($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- এরিয়া নির্বাচন করুন --</option>
                        <?php if (!empty($assignedAreas)): ?>
                            <?php foreach ($assignedAreas as $area): ?>
                                <option value="<?= $area['area_id'] ?>"><?= htmlspecialchars($area['area_name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php 
                            $allAreas = (new \Models\Area())->all();
                            foreach ($allAreas as $area): ?>
                                <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div x-show="zonesList.length > 0">
                    <label class="block font-bold text-gray-700 mb-1">ওয়ার্ড / জোন (ঐচ্ছিক)</label>
                    <select x-model="newCustomer.zone_id" @change="loadPoints($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- ওয়ার্ড / জোন নির্বাচন করুন --</option>
                        <template x-for="z in zonesList" :key="z.id">
                            <option :value="z.id" x-text="z.name"></option>
                        </template>
                    </select>
                </div>

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
                    <label class="block font-bold text-gray-700 mb-1">বিস্তারিত ঠিকানা</label>
                    <textarea x-model="newCustomer.address_details" rows="2" placeholder="বাড়ি, রোড বা আশেপাশের পরিচিত স্থান..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="addCustomerModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl font-bold transition-colors">
                        বাতিল
                    </button>
                    <button type="button" @click="saveNewCustomer()" :disabled="submittingCustomer" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md transition-colors flex items-center gap-1.5 disabled:opacity-50">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span x-text="submittingCustomer ? 'সংরক্ষণ হচ্ছে...' : 'কাস্টমার সংরক্ষণ করুন'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
