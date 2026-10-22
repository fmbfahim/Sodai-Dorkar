<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6" x-data="{
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

    <!-- Top Breadcrumb & Header Actions -->
    <div class="flex items-center justify-between border-b border-gray-200/80 pb-3">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-0.5">
                <a href="<?= $base ?>/account" class="hover:text-emerald-600">একাউন্ট</a>
                <span>/</span>
                <a href="<?= $base ?>/account/agent/dashboard" class="hover:text-emerald-600">এজেন্ট হাব</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">এলাকার কাস্টমার তালিকা</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>আমার এলাকার গ্রাহক তালিকা (<?= count($customers) ?> জন)</span>
                <ion-icon name="people" class="text-emerald-600"></ion-icon>
            </h1>
        </div>

        <button type="button" @click="addCustomerModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all">
            <ion-icon name="person-add-outline" class="text-base"></ion-icon>
            <span>+ নতুন গ্রাহক নিবন্ধন</span>
        </button>
    </div>

    <!-- Search Bar -->
    <form action="<?= $base ?>/account/agent/customers" method="GET" class="relative">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="গ্রাহকের নাম বা মোবাইল নম্বর দিয়ে খুঁজুন..." class="w-full pl-10 pr-10 py-3 bg-white rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs text-xs font-medium">
        <ion-icon name="search-outline" class="absolute left-3.5 top-3.5 text-lg text-gray-400"></ion-icon>
        <?php if (!empty($search)): ?>
            <a href="<?= $base ?>/account/agent/customers" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 text-lg">
                <ion-icon name="close-circle"></ion-icon>
            </a>
        <?php endif; ?>
    </form>

    <!-- Customers Grid / List -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
        <?php if (empty($customers)): ?>
            <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-gray-200/80 space-y-2">
                <ion-icon name="people-outline" class="text-4xl text-gray-300"></ion-icon>
                <p class="text-xs font-bold text-gray-600">কোনো গ্রাহক পাওয়া যায়নি</p>
                <p class="text-[11px] text-gray-400">আপনার নির্ধারিত এরিয়ায় এখনও কোনো গ্রাহক নিবন্ধিত নেই অথবা সার্চে মিল পাওয়া যায়নি।</p>
                <button type="button" @click="addCustomerModal = true" class="inline-flex items-center gap-1.5 mt-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs">
                    <ion-icon name="person-add-outline"></ion-icon>
                    <span>নতুন গ্রাহক যোগ করুন</span>
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($customers as $c): ?>
                <?php
                $initial = strtoupper(mb_substr($c['name'] ?? 'C', 0, 1));
                $orderCount = (int)($c['orders_count'] ?? 0);
                ?>
                <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs hover:border-emerald-300 transition-all flex flex-col justify-between space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-100 flex items-center justify-center font-black text-sm shrink-0">
                                <?= $initial ?>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 leading-tight"><?= htmlspecialchars($c['name']) ?></h3>
                                <div class="text-[11px] font-mono text-emerald-700 font-bold mt-0.5"><?= htmlspecialchars($c['phone']) ?></div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5"><?= htmlspecialchars($c['unique_code'] ?? '') ?></div>
                            </div>
                        </div>

                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?= $orderCount > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500' ?>" title="মোট অর্ডার">
                            <?= $orderCount ?> অর্ডার
                        </span>
                    </div>

                    <div class="text-[11px] text-gray-500 bg-gray-50 rounded-xl p-2.5 space-y-1">
                        <div class="flex items-center gap-1">
                            <ion-icon name="location-outline" class="text-gray-400"></ion-icon>
                            <span class="font-bold text-gray-700"><?= htmlspecialchars($c['area_name'] ?? 'এরিয়া') ?></span>
                            <?php if (!empty($c['zone_name'])): ?>
                                <span>• <?= htmlspecialchars($c['zone_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($c['address_details'])): ?>
                            <div class="text-[10px] text-gray-400 truncate" title="<?= htmlspecialchars($c['address_details']) ?>">
                                <?= htmlspecialchars($c['address_details']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action: Take Order for this customer -->
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <a href="<?= $base ?>/account/agent/shop" class="w-full py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5">
                            <ion-icon name="cart-outline"></ion-icon>
                            <span>পণ্য সিলেক্ট করে অর্ডার দিন</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL: ADD NEW CUSTOMER                         -->
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

                <!-- Area Dropdown -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1">ইউনিয়ন / এরিয়া *</label>
                    <select x-model="newCustomer.area_id" @change="loadZones($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- এরিয়া নির্বাচন করুন --</option>
                        <?php foreach ($assignedAreas as $area): ?>
                            <option value="<?= $area['area_id'] ?>"><?= htmlspecialchars($area['area_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Zone Dropdown -->
                <div x-show="zonesList.length > 0">
                    <label class="block font-bold text-gray-700 mb-1">ওয়ার্ড / জোন (ঐচ্ছিক)</label>
                    <select x-model="newCustomer.zone_id" @change="loadPoints($event.target.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- ওয়ার্ড / জোন নির্বাচন করুন --</option>
                        <template x-for="z in zonesList" :key="z.id">
                            <option :value="z.id" x-text="z.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Point Dropdown -->
                <div x-show="pointsList.length > 0">
                    <label class="block font-bold text-gray-700 mb-1">পয়েন্ট / গ্রাম (ঐচ্ছিক)</label>
                    <select x-model="newCustomer.point_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                        <option value="">-- পয়েন্ট নির্বাচন করুন --</option>
                        <template x-for="p in pointsList" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">ঠিকানা / ডেলিভারি বিবরণ (ঐচ্ছিক)</label>
                    <textarea x-model="newCustomer.address_details" rows="2" placeholder="বাড়ি নং, রাস্তা, ল্যান্ডমার্ক ইত্যাদি..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"></textarea>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" @click="addCustomerModal = false" class="px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 text-xs font-bold">বাতিল</button>
                <button type="button" @click="saveNewCustomer()" :disabled="submittingCustomer" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold shadow-xs">
                    <span x-show="!submittingCustomer">সংরক্ষণ করুন</span>
                    <span x-show="submittingCustomer">সংরক্ষণ হচ্ছে...</span>
                </button>
            </div>
        </div>
    </div>

</div>
