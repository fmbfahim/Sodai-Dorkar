<?php 
ob_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-5">
    <?php if (!empty($success)): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-2.5 text-xs sm:text-sm font-bold shadow-2xs">
        <ion-icon name="checkmark-circle" class="text-emerald-600 text-xl flex-shrink-0"></ion-icon>
        <span>Your profile has been updated successfully!</span>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-center gap-2.5 text-xs sm:text-sm font-bold shadow-2xs">
        <ion-icon name="alert-circle" class="text-rose-600 text-xl flex-shrink-0"></ion-icon>
        <span><?= $error === 'name_required' ? 'Full name is required.' : 'Failed to update profile. Please try again.' ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50">
            <h1 class="font-black text-gray-900 text-base sm:text-lg flex items-center gap-2">
                <ion-icon name="person-circle-outline" class="text-emerald-600 text-xl"></ion-icon> 
                <span>Edit Profile Information</span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Keep your contact information and delivery address up to date.</p>
        </div>

        <form action="<?= $base ?>/account/profile/update" method="POST" class="p-4 sm:p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Full Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($customer['name'] ?? '') ?>"
                           required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all shadow-2xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                           placeholder="your.email@example.com"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all shadow-2xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Registered Phone Number</label>
                <input type="text" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" disabled
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm bg-gray-100/70 text-gray-500 cursor-not-allowed font-medium">
                <p class="text-[11px] text-gray-400 mt-1">Phone number is linked to your login identity and cannot be changed directly.</p>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800 mb-3 flex items-center gap-1.5">
                    <ion-icon name="location-outline" class="text-emerald-600"></ion-icon> 
                    <span>Primary Delivery Region</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Area</label>
                        <select name="area_id" id="profileArea" onchange="loadZones(this.value)"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-2xs bg-white">
                            <option value="">-- Select Area --</option>
                            <?php foreach ($areas as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= ($customer['area_id'] == $a['id']) ? 'selected' : '' ?>><?= htmlspecialchars($a['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Zone</label>
                        <select name="zone_id" id="profileZone" onchange="loadPoints(this.value)"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-2xs bg-white">
                            <option value="">-- Select Zone --</option>
                            <?php foreach ($zones as $z): ?>
                            <option value="<?= $z['id'] ?>" <?= ($customer['zone_id'] == $z['id']) ? 'selected' : '' ?>><?= htmlspecialchars($z['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Point / Landmark</label>
                        <select name="point_id" id="profilePoint"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-2xs bg-white">
                            <option value="">-- Select Point --</option>
                            <?php foreach ($points as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($customer['point_id'] == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Detailed Street Address</label>
                <textarea name="address_details" rows="2"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none shadow-2xs"
                          placeholder="House No, Road, Flat, Apartment, Landmark..."><?= htmlspecialchars($customer['address_details'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-xs sm:text-sm shadow-md hover:shadow-lg">
                <ion-icon name="save-outline" class="text-base"></ion-icon> 
                <span>Save Profile Changes</span>
            </button>
        </form>
    </div>
</div>

<script>
function loadZones(areaId) {
    const zoneSelect = document.getElementById('profileZone');
    const pointSelect = document.getElementById('profilePoint');
    zoneSelect.innerHTML = '<option value="">Loading...</option>';
    pointSelect.innerHTML = '<option value="">-- Select Point --</option>';
    if (!areaId) { zoneSelect.innerHTML = '<option value="">-- Select Zone --</option>'; return; }
    fetch((window.APP_BASE || '') + '/api/zones?area_id=' + areaId)
        .then(r => r.json()).then(data => {
            zoneSelect.innerHTML = '<option value="">-- Select Zone --</option>';
            data.forEach(z => { zoneSelect.innerHTML += '<option value="'+z.id+'">'+z.name+'</option>'; });
        });
}
function loadPoints(zoneId) {
    const pointSelect = document.getElementById('profilePoint');
    pointSelect.innerHTML = '<option value="">Loading...</option>';
    if (!zoneId) { pointSelect.innerHTML = '<option value="">-- Select Point --</option>'; return; }
    fetch((window.APP_BASE || '') + '/api/points?zone_id=' + zoneId)
        .then(r => r.json()).then(data => {
            pointSelect.innerHTML = '<option value="">-- Select Point --</option>';
            data.forEach(p => { pointSelect.innerHTML += '<option value="'+p.id+'">'+p.name+'</option>'; });
        });
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
