<?php ob_start(); ?>

<div class="space-y-5">
    <?php if (!empty($success)): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3.5 flex items-center gap-2 text-sm font-semibold">
        <ion-icon name="checkmark-circle" class="text-emerald-600 text-lg flex-shrink-0"></ion-icon>
        প্রোফাইল সফলভাবে আপডেট হয়েছে!
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3.5 flex items-center gap-2 text-sm font-semibold">
        <ion-icon name="alert-circle" class="text-red-500 text-lg flex-shrink-0"></ion-icon>
        <?= $error === 'name_required' ? 'নাম খালি রাখা যাবে না।' : 'একটি ত্রুটি ঘটেছে।' ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <h1 class="font-black text-gray-900 text-lg flex items-center gap-2">
                <ion-icon name="person-circle-outline" class="text-emerald-600 text-xl"></ion-icon> প্রোফাইল সম্পাদনা
            </h1>
        </div>
        <form action="/sodai-dorkar/public/account/profile/update" method="POST" class="p-5 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">নাম *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($customer['name'] ?? '') ?>"
                           required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">ইমেইল</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">ফোন নম্বর</label>
                <input type="text" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">ফোন নম্বর পরিবর্তন করা যাবে না।</p>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                    <ion-icon name="location-outline" class="text-emerald-600"></ion-icon> ডেলিভারি এলাকা
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">এলাকা (Area)</label>
                        <select name="area_id" id="profileArea" onchange="loadZones(this.value)"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">-- নির্বাচন করুন --</option>
                            <?php foreach ($areas as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= ($customer['area_id'] == $a['id']) ? 'selected' : '' ?>><?= htmlspecialchars($a['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">জোন (Zone)</label>
                        <select name="zone_id" id="profileZone" onchange="loadPoints(this.value)"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">-- নির্বাচন করুন --</option>
                            <?php foreach ($zones as $z): ?>
                            <option value="<?= $z['id'] ?>" <?= ($customer['zone_id'] == $z['id']) ? 'selected' : '' ?>><?= htmlspecialchars($z['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">পয়েন্ট (Point)</label>
                        <select name="point_id" id="profilePoint"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">-- নির্বাচন করুন --</option>
                            <?php foreach ($points as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($customer['point_id'] == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">বিস্তারিত ঠিকানা</label>
                <textarea name="address_details" rows="2"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none"
                          placeholder="বাড়ি নম্বর, রাস্তা, মহল্লা..."><?= htmlspecialchars($customer['address_details'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                <ion-icon name="save-outline" class="text-base"></ion-icon> পরিবর্তন সংরক্ষণ করুন
            </button>
        </form>
    </div>
</div>

<script>
function loadZones(areaId) {
    const zoneSelect = document.getElementById('profileZone');
    const pointSelect = document.getElementById('profilePoint');
    zoneSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    pointSelect.innerHTML = '<option value="">-- নির্বাচন করুন --</option>';
    if (!areaId) { zoneSelect.innerHTML = '<option value="">-- নির্বাচন করুন --</option>'; return; }
    fetch('/sodai-dorkar/public/api/zones?area_id=' + areaId)
        .then(r => r.json()).then(data => {
            zoneSelect.innerHTML = '<option value="">-- নির্বাচন করুন --</option>';
            data.forEach(z => { zoneSelect.innerHTML += '<option value="'+z.id+'">'+z.name+'</option>'; });
        });
}
function loadPoints(zoneId) {
    const pointSelect = document.getElementById('profilePoint');
    pointSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    if (!zoneId) { pointSelect.innerHTML = '<option value="">-- নির্বাচন করুন --</option>'; return; }
    fetch('/sodai-dorkar/public/api/points?zone_id=' + zoneId)
        .then(r => r.json()).then(data => {
            pointSelect.innerHTML = '<option value="">-- নির্বাচন করুন --</option>';
            data.forEach(p => { pointSelect.innerHTML += '<option value="'+p.id+'">'+p.name+'</option>'; });
        });
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

