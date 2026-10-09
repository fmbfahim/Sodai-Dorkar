<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$selectedZoneId = $_GET['zone_id'] ?? $_SESSION['last_point_zone_id'] ?? ($zones[0]['id'] ?? '');
?>

<!-- Alert Container for Dynamic & Static Messages -->
<div id="alertContainer">
    <?php if (!empty($_GET['success'])): ?>
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-xs animate-fade-in">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-xl text-emerald-600 shrink-0"></ion-icon>
                <span class="font-medium"><?= htmlspecialchars($_GET['success']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 sticky top-6">
            <div class="flex items-center justify-between mb-5 border-b border-secondary-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center text-lg">
                        <ion-icon name="home-outline"></ion-icon>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900 leading-tight">Add Point (House)</h3>
                        <p class="text-xs text-secondary-500">বাড়ি বা পয়েন্ট যোগ করুন</p>
                    </div>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                    বাড়ি তালিকা
                </span>
            </div>

            <form action="<?= $base ?>/admin/points/store" method="POST" id="pointForm">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                
                <!-- 1. Searchable Select: Zone (Ward) -->
                <div class="mb-5">
                    <label class="block text-secondary-800 text-sm font-bold mb-1.5 flex items-center justify-between">
                        <span>Select Zone (Ward) <span class="text-red-500">*</span></span>
                        <span class="text-[11px] font-normal text-secondary-400">ওয়ার্ড সার্চ করে সিলেক্ট করুন</span>
                    </label>

                    <!-- Hidden Input holding the actual selected zone ID -->
                    <input type="hidden" name="zone_id" id="zone_id_input" value="<?= htmlspecialchars((string)$selectedZoneId) ?>" required>

                    <!-- Searchable Dropdown Container -->
                    <div class="relative" id="zoneSelectWrapper">
                        <!-- Trigger Button -->
                        <button type="button" id="zoneSelectTrigger" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-semibold text-secondary-800 hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <ion-icon name="location-outline" class="text-primary-600 text-base shrink-0"></ion-icon>
                                <span id="zoneSelectedLabel" class="truncate">ওয়ার্ড নির্বাচন করুন...</span>
                            </div>
                            <ion-icon name="chevron-down-outline" id="zoneSelectChevron" class="text-secondary-400 text-base shrink-0 transition-transform duration-200"></ion-icon>
                        </button>

                        <!-- Floating Searchable Menu -->
                        <div id="zoneDropdownMenu" 
                             class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-secondary-200 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                            <!-- Search Field Inside Dropdown -->
                            <div class="p-2.5 border-b border-secondary-100 bg-secondary-50/50">
                                <div class="relative">
                                    <ion-icon name="search-outline" class="absolute left-3 top-2.5 text-secondary-400 text-sm"></ion-icon>
                                    <input type="text" id="zoneSearchInput" 
                                           placeholder="ওয়ার্ড বা ইউনিয়ন সার্চ করুন..." 
                                           autocomplete="off"
                                           class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-secondary-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 text-secondary-800 font-medium placeholder-secondary-400">
                                </div>
                            </div>

                            <!-- Options List -->
                            <div id="zoneOptionsList" class="max-h-60 overflow-y-auto divide-y divide-secondary-100/60 text-xs">
                                <?php foreach ($zones as $zone): 
                                    $isSelected = ((string)$selectedZoneId === (string)$zone['id']);
                                ?>
                                    <div class="zone-option px-3.5 py-2.5 hover:bg-primary-50/80 cursor-pointer flex items-center justify-between transition-colors <?= $isSelected ? 'bg-primary-50 text-primary-900 font-bold' : 'text-secondary-700' ?>"
                                         data-id="<?= $zone['id'] ?>"
                                         data-name="<?= htmlspecialchars($zone['name']) ?>"
                                         data-area="<?= htmlspecialchars($zone['area_name'] ?? '') ?>">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $isSelected ? 'bg-primary-600' : 'bg-secondary-300' ?> shrink-0"></span>
                                            <span class="font-medium truncate"><?= htmlspecialchars($zone['name']) ?></span>
                                            <span class="text-[10px] text-secondary-400 font-normal truncate">(<?= htmlspecialchars($zone['area_name'] ?? '') ?>)</span>
                                        </div>
                                        <?php if ($isSelected): ?>
                                            <ion-icon name="checkmark-circle" class="text-primary-600 text-sm shrink-0 checkmark-icon"></ion-icon>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Empty Search Result -->
                            <div id="zoneNoResults" class="hidden px-4 py-6 text-center text-xs text-secondary-400">
                                <ion-icon name="alert-circle-outline" class="text-lg mb-1"></ion-icon>
                                <p>কোনো ওয়ার্ড বা ইউনিয়ন পাওয়া যায়নি</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-emerald-600 mt-1.5 flex items-center gap-1 font-medium">
                        <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                        <span>পয়েন্ট এড করার পরও এই ওয়ার্ডটি সবসময় ফিক্সড থাকবে (রিসেট হবে না)।</span>
                    </p>
                </div>

                <!-- 2. Point Name(s) with Multi-Add Support -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-secondary-800 text-sm font-bold">
                            Point Name (House / বাড়ির নাম) <span class="text-red-500">*</span>
                        </label>
                        <span id="pointCountBadge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 transition-all shadow-2xs">
                            ১টি পয়েন্ট
                        </span>
                    </div>

                    <!-- Input Mode Switcher (Row Mode vs Bulk Paste Mode) -->
                    <div class="flex items-center p-1 bg-secondary-100/80 rounded-xl mb-3 text-xs font-semibold">
                        <button type="button" id="tabRowMode" onclick="switchInputMode('rows')" 
                                class="flex-1 py-1.5 px-2 rounded-lg text-center transition-all bg-white text-secondary-800 shadow-2xs">
                            📝 আলাদা ঘর (Row Mode)
                        </button>
                        <button type="button" id="tabBulkMode" onclick="switchInputMode('bulk')" 
                                class="flex-1 py-1.5 px-2 rounded-lg text-center transition-all text-secondary-500 hover:text-secondary-800">
                            📋 এক সাথে পেস্ট (Bulk Paste)
                        </button>
                    </div>

                    <!-- Mode A: Dynamic Rows -->
                    <div id="rowModeContainer" class="space-y-2">
                        <div id="pointRowsList" class="space-y-2">
                            <!-- Rows will be injected here via JS -->
                        </div>
                        <button type="button" onclick="addPointRow()" 
                                class="w-full py-2 px-3 border border-dashed border-secondary-300 hover:border-primary-500 hover:bg-primary-50/50 text-secondary-600 hover:text-primary-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                            <span>+ আরও একটি বাড়ি যোগ করুন (Add Row)</span>
                        </button>
                    </div>

                    <!-- Mode B: Bulk Paste Textarea -->
                    <div id="bulkModeContainer" class="hidden">
                        <textarea id="bulkTextarea" name="name" rows="4" 
                                  placeholder="যেমন:&#10;সরদার বাড়ি&#10;মিজি বাড়ি&#10;গাজি বাড়ি&#10;অথবা কমা (,) দিয়ে: সরদার বাড়ি, মিজি বাড়ি, মোল্লা বাড়ি" 
                                  class="w-full px-3.5 py-2.5 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 font-sans text-secondary-800 placeholder-secondary-400"></textarea>
                    </div>

                    <!-- Rule Hint -->
                    <div class="mt-2 text-[11px] text-secondary-500 bg-secondary-50 p-2.5 rounded-lg border border-secondary-200/80 flex items-start gap-1.5">
                        <ion-icon name="bulb-outline" class="text-amber-500 text-sm shrink-0 mt-0.5"></ion-icon>
                        <span>এন্ট্রি লেখার সময় <kbd class="px-1 py-0.5 bg-white border border-secondary-200 rounded text-[10px]">Enter</kbd> চাপলে স্বয়ংক্রিয়ভাবে পরবর্তী ঘর তৈরি হবে। কমা (,) বা নতুন লাইনে লিখলেও আলাদা পয়েন্ট তৈরি হবে।</span>
                    </div>

                    <!-- Live Detected Chips Preview -->
                    <div id="chipsContainer" class="hidden mt-3 flex flex-wrap gap-1.5 max-h-28 overflow-y-auto p-2 bg-emerald-50/60 border border-emerald-200 rounded-xl"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <ion-icon name="add-circle" class="text-lg"></ion-icon>
                    <span id="submitBtnText">Add Point (বাড়ি যোগ করুন)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 overflow-hidden">
            <!-- Header with Live Filter & Search -->
            <div class="p-5 border-b border-secondary-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-bold text-secondary-900 text-base">Point List</h3>
                    <span id="filteredCountBadge" class="text-xs bg-secondary-100 text-secondary-700 font-bold px-2.5 py-0.5 rounded-full border border-secondary-200">
                        <?= count($points) ?> টি
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Clean Corrupted (??) Button -->
                    <button type="button" id="cleanCorruptedBtn" title="ত্রুটিপূর্ণ (??) বা অকেজো পয়েন্টগুলো স্বয়ংক্রিয়ভাবে মুছে ফেলুন" 
                            class="text-xs font-bold px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap">
                        <ion-icon name="trash-bin-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>অকেজো (??) পয়েন্ট মুছুন</span>
                    </button>

                    <!-- Zone Filter -->
                    <select id="listFilterZone" class="text-xs font-semibold border border-secondary-300 rounded-xl px-3 py-2 bg-white text-secondary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="">সকল ওয়ার্ড (All Zones)</option>
                        <?php foreach ($zones as $zone): ?>
                            <option value="<?= $zone['id'] ?>" <?= ((string)$selectedZoneId === (string)$zone['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($zone['name']) ?> (<?= htmlspecialchars($zone['area_name'] ?? '') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="pointSearch" placeholder="বাড়ি সার্চ করুন..." 
                               class="text-xs border border-secondary-300 rounded-xl pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500/20 w-full sm:w-44 text-secondary-800 font-medium">
                        <ion-icon name="search-outline" class="absolute left-2.5 top-2.5 text-secondary-400 text-sm"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Floating / Inline Bulk Action Bar -->
            <div id="bulkActionBar" class="hidden px-5 py-3 bg-red-50 border-b border-red-200 flex flex-wrap items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2">
                    <ion-icon name="checkbox-outline" class="text-red-600 text-lg"></ion-icon>
                    <span class="text-xs font-bold text-red-800" id="selectedCountText">০ টি বাড়ি নির্বাচিত</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="bulkDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 cursor-pointer shadow-xs transition-colors">
                        <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                        <span>নির্বাচিতগুলো মুছে ফেলুন (Delete Selected)</span>
                    </button>
                    <button type="button" id="cancelBulkBtn" class="text-secondary-600 hover:text-secondary-900 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-100/60 transition-colors cursor-pointer">
                        বাতিল
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600" id="pointsTable">
                    <thead class="bg-secondary-50/80 text-secondary-500 text-xs uppercase tracking-wider font-bold">
                        <tr>
                            <th class="w-10 px-4 py-3.5 text-center">
                                <input type="checkbox" id="selectAllCheckbox" title="সবগুলো নির্বাচন করুন" 
                                       class="rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-3.5">Point (House)</th>
                            <th class="px-6 py-3.5">Zone & Union</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100" id="pointsTbody">
                         <?php if (empty($points)): ?>
                            <tr id="emptyRow">
                                <td colspan="4" class="px-6 py-10 text-center text-secondary-400">
                                    <ion-icon name="home-outline" class="text-3xl text-secondary-300 mb-2"></ion-icon>
                                    <p class="font-medium">কোনো পয়েন্ট পাওয়া যায়নি। বাম পাশের ফর্ম থেকে নতুন বাড়ি যোগ করুন।</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($points as $point): ?>
                                <tr class="hover:bg-secondary-50/80 transition-colors point-row" 
                                    data-id="<?= $point['id'] ?>"
                                    data-zone-id="<?= $point['zone_id'] ?>" 
                                    data-name="<?= htmlspecialchars(mb_strtolower($point['name'])) ?>"
                                    data-zone-name="<?= htmlspecialchars(mb_strtolower(($point['zone_name'] ?? '') . ' ' . ($point['area_name'] ?? ''))) ?>">
                                    <td class="w-10 px-4 py-4 text-center">
                                        <input type="checkbox" class="point-row-checkbox rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                                               value="<?= $point['id'] ?>">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-secondary-900 point-name-cell">
                                        <?= htmlspecialchars($point['name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-secondary-800 font-semibold"><?= htmlspecialchars($point['zone_name'] ?? ''); ?></div>
                                        <div class="text-xs text-secondary-400"><?= htmlspecialchars($point['area_name'] ?? ''); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="<?= $base ?>/admin/points/edit?id=<?= $point['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 inline-block transition-colors mr-1"
                                           title="Edit">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="<?= $base ?>/admin/points/delete" method="POST" 
                                              onsubmit="return confirm('আপনি কি নিশ্চিত যে এই পয়েন্টটি মুছে ফেলতে চান?');" 
                                              class="inline delete-form">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?= $point['id']; ?>">
                                            <input type="hidden" name="zone_id" value="<?= $selectedZoneId ?>">
                                            <button type="submit" 
                                                    class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 inline-block transition-colors cursor-pointer"
                                                    title="Delete">
                                                <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const APP_BASE = '<?= $base ?>';
    const zoneIdInput = document.getElementById('zone_id_input');
    const zoneTrigger = document.getElementById('zoneSelectTrigger');
    const zoneChevron = document.getElementById('zoneSelectChevron');
    const zoneLabel = document.getElementById('zoneSelectedLabel');
    const zoneDropdown = document.getElementById('zoneDropdownMenu');
    const zoneSearchInput = document.getElementById('zoneSearchInput');
    const zoneOptions = document.querySelectorAll('.zone-option');
    const zoneNoResults = document.getElementById('zoneNoResults');

    const pointForm = document.getElementById('pointForm');
    const pointRowsList = document.getElementById('pointRowsList');
    const bulkTextarea = document.getElementById('bulkTextarea');
    const chipsContainer = document.getElementById('chipsContainer');
    const badge = document.getElementById('pointCountBadge');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const alertContainer = document.getElementById('alertContainer');

    const listFilterZone = document.getElementById('listFilterZone');
    const pointSearch = document.getElementById('pointSearch');
    const filteredCountBadge = document.getElementById('filteredCountBadge');
    const pointsTbody = document.getElementById('pointsTbody');

    let currentInputMode = 'rows'; // 'rows' or 'bulk'

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ==========================================
    // 1. SEARCHABLE ZONE SELECT LOGIC
    // ==========================================
    function setZoneSelected(id, name, area, saveLocal = true) {
        if (!id) return;
        zoneIdInput.value = id;
        zoneLabel.innerHTML = `<span>${escapeHtml(name)}</span> <span class="text-xs text-secondary-400 font-normal">(${escapeHtml(area)})</span>`;
        
        // Update checkmarks in options
        zoneOptions.forEach(opt => {
            const isMatch = (opt.getAttribute('data-id') === String(id));
            if (isMatch) {
                opt.classList.add('bg-primary-50', 'text-primary-900', 'font-bold');
                opt.classList.remove('text-secondary-700');
                if (!opt.querySelector('.checkmark-icon')) {
                    const icon = document.createElement('ion-icon');
                    icon.setAttribute('name', 'checkmark-circle');
                    icon.className = 'text-primary-600 text-sm shrink-0 checkmark-icon';
                    opt.appendChild(icon);
                }
            } else {
                opt.classList.remove('bg-primary-50', 'text-primary-900', 'font-bold');
                opt.classList.add('text-secondary-700');
                const ic = opt.querySelector('.checkmark-icon');
                if (ic) ic.remove();
            }
        });

        if (saveLocal) {
            localStorage.setItem('admin_last_point_zone_id', id);
        }

        // Synchronize table filter with selected zone
        if (listFilterZone) {
            listFilterZone.value = id;
            filterTable();
        }
    }

    // Dropdown open/close
    function toggleZoneDropdown(open) {
        if (open === undefined) open = zoneDropdown.classList.contains('hidden');
        if (open) {
            zoneDropdown.classList.remove('hidden');
            zoneChevron.classList.add('rotate-180');
            zoneSearchInput.value = '';
            filterZoneOptions('');
            setTimeout(() => zoneSearchInput.focus(), 50);
        } else {
            zoneDropdown.classList.add('hidden');
            zoneChevron.classList.remove('rotate-180');
        }
    }

    zoneTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleZoneDropdown();
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('zoneSelectWrapper').contains(e.target)) {
            toggleZoneDropdown(false);
        }
    });

    // Real-time Search in Zone Dropdown
    function filterZoneOptions(query) {
        query = query.trim().toLowerCase();
        let matches = 0;
        zoneOptions.forEach(opt => {
            const name = (opt.getAttribute('data-name') || '').toLowerCase();
            const area = (opt.getAttribute('data-area') || '').toLowerCase();
            if (!query || name.includes(query) || area.includes(query)) {
                opt.style.display = '';
                matches++;
            } else {
                opt.style.display = 'none';
            }
        });
        if (zoneNoResults) {
            zoneNoResults.classList.toggle('hidden', matches > 0);
        }
    }

    zoneSearchInput.addEventListener('input', (e) => {
        filterZoneOptions(e.target.value);
    });

    // Click on option
    zoneOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            const id = opt.getAttribute('data-id');
            const name = opt.getAttribute('data-name');
            const area = opt.getAttribute('data-area');
            setZoneSelected(id, name, area, true);
            toggleZoneDropdown(false);
        });
    });

    // Initialize Zone selection from LocalStorage / URL / Default
    const urlParams = new URLSearchParams(window.location.search);
    const urlZoneId = urlParams.get('zone_id');
    const savedZoneId = localStorage.getItem('admin_last_point_zone_id');

    let initZoneId = urlZoneId || zoneIdInput.value || savedZoneId;
    let targetOpt = document.querySelector(`.zone-option[data-id="${initZoneId}"]`);
    if (!targetOpt && zoneOptions.length > 0) {
        targetOpt = zoneOptions[0];
    }
    if (targetOpt) {
        setZoneSelected(
            targetOpt.getAttribute('data-id'),
            targetOpt.getAttribute('data-name'),
            targetOpt.getAttribute('data-area'),
            true
        );
    }

    // ==========================================
    // 2. MULTI-POINT DYNAMIC INPUTS LOGIC
    // ==========================================
    window.switchInputMode = function(mode) {
        currentInputMode = mode;
        const tabRow = document.getElementById('tabRowMode');
        const tabBulk = document.getElementById('tabBulkMode');
        const rowCont = document.getElementById('rowModeContainer');
        const bulkCont = document.getElementById('bulkModeContainer');

        if (mode === 'rows') {
            tabRow.classList.add('bg-white', 'text-secondary-800', 'shadow-2xs');
            tabRow.classList.remove('text-secondary-500');
            tabBulk.classList.remove('bg-white', 'text-secondary-800', 'shadow-2xs');
            tabBulk.classList.add('text-secondary-500');
            rowCont.classList.remove('hidden');
            bulkCont.classList.add('hidden');
        } else {
            tabBulk.classList.add('bg-white', 'text-secondary-800', 'shadow-2xs');
            tabBulk.classList.remove('text-secondary-500');
            tabRow.classList.remove('bg-white', 'text-secondary-800', 'shadow-2xs');
            tabRow.classList.add('text-secondary-500');
            bulkCont.classList.remove('hidden');
            rowCont.classList.add('hidden');
        }
        updatePointsPreview();
    };

    window.addPointRow = function(value = '') {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 point-row-item animate-fade-in';
        row.innerHTML = `
            <div class="relative flex-1">
                <input type="text" name="names[]" value="${escapeHtml(value)}" 
                       placeholder="বাড়ির নাম লিখুন (যেমন: সরদার বাড়ি)..." 
                       class="point-input-field w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-secondary-800 font-medium placeholder-secondary-400">
            </div>
            <button type="button" onclick="removePointRow(this)" 
                    class="p-2 text-secondary-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors cursor-pointer" title="এই ঘরটি মুছুন">
                <ion-icon name="close-circle-outline" class="text-xl"></ion-icon>
            </button>
        `;
        pointRowsList.appendChild(row);

        const input = row.querySelector('.point-input-field');
        input.addEventListener('input', updatePointsPreview);
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addPointRow();
            }
        });

        if (!value) {
            setTimeout(() => input.focus(), 50);
        }
        updatePointsPreview();
    };

    window.removePointRow = function(btn) {
        const rows = pointRowsList.querySelectorAll('.point-row-item');
        if (rows.length > 1) {
            btn.closest('.point-row-item').remove();
        } else {
            // If only 1 row left, just clear it
            const input = rows[0].querySelector('.point-input-field');
            if (input) input.value = '';
        }
        updatePointsPreview();
    };

    // Initialize 2 default rows
    addPointRow();
    addPointRow();

    bulkTextarea.addEventListener('input', updatePointsPreview);

    function getCollectedNames() {
        const names = [];
        if (currentInputMode === 'rows') {
            const inputs = pointRowsList.querySelectorAll('.point-input-field');
            inputs.forEach(inp => {
                const val = inp.value.trim();
                if (val) {
                    val.split(/[\r\n,;।\u0964\u0965]+/u).map(s => s.trim()).filter(Boolean).forEach(s => names.push(s));
                }
            });
        } else {
            const raw = bulkTextarea.value.trim();
            if (raw) {
                raw.split(/[\r\n,;।\u0964\u0965]+/u).map(s => s.trim()).filter(Boolean).forEach(s => names.push(s));
            }
        }
        return [...new Set(names)];
    }

    function updatePointsPreview() {
        const items = getCollectedNames();
        if (items.length === 0) {
            badge.classList.add('hidden');
            chipsContainer.classList.add('hidden');
            chipsContainer.innerHTML = '';
            submitBtnText.textContent = 'Add Point (বাড়ি যোগ করুন)';
        } else if (items.length === 1) {
            badge.classList.remove('hidden');
            badge.textContent = '১টি পয়েন্ট';
            chipsContainer.classList.add('hidden');
            chipsContainer.innerHTML = '';
            submitBtnText.textContent = 'Add Point (১টি বাড়ি যোগ করুন)';
        } else {
            badge.classList.remove('hidden');
            badge.textContent = `মোট ${items.length}টি পয়েন্ট`;
            chipsContainer.classList.remove('hidden');
            chipsContainer.innerHTML = items.map(item => `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-800 text-xs font-bold shadow-2xs">
                    <ion-icon name="checkmark-circle" class="text-emerald-600 text-sm"></ion-icon>
                    <span>${escapeHtml(item)}</span>
                </span>
            `).join('');
            submitBtnText.textContent = `Add ${items.length} Points (একত্রে ${items.length}টি বাড়ি যোগ করুন)`;
        }
    }

    // ==========================================
    // 3. AJAX SUBMISSION WITHOUT RESETING ZONE!
    // ==========================================
    pointForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const zoneId = zoneIdInput.value;
        const items = getCollectedNames();

        if (!zoneId) {
            showAlert('দয়া করে একটি ওয়ার্ড (Zone) নির্বাচন করুন।', 'error');
            toggleZoneDropdown(true);
            return;
        }

        if (items.length === 0) {
            showAlert('দয়া করে অন্তত একটি বাড়ির নাম লিখুন।', 'error');
            const firstInp = pointRowsList.querySelector('.point-input-field');
            if (firstInp) firstInp.focus();
            return;
        }

        submitBtn.disabled = true;
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>যোগ করা হচ্ছে...</span>
        `;

        const formData = new FormData();
        const csrfVal = pointForm.querySelector('input[name="csrf_token"]').value;
        formData.append('csrf_token', csrfVal);
        formData.append('zone_id', zoneId);
        formData.append('is_ajax', '1');
        items.forEach(name => formData.append('names[]', name));

        fetch(pointForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;

            if (data.success) {
                showAlert(data.message, 'success');

                // Prepend newly added points to the table
                const emptyRow = document.getElementById('emptyRow');
                if (emptyRow) emptyRow.remove();

                if (Array.isArray(data.points)) {
                    data.points.forEach(pt => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-secondary-50/80 transition-colors point-row bg-emerald-50/60 animate-fade-in';
                        tr.setAttribute('data-id', pt.id);
                        tr.setAttribute('data-zone-id', pt.zone_id);
                        tr.setAttribute('data-name', (pt.name || '').toLowerCase());
                        tr.setAttribute('data-zone-name', ((pt.zone_name || '') + ' ' + (pt.area_name || '')).toLowerCase());
                        tr.innerHTML = `
                            <td class="w-10 px-4 py-4 text-center">
                                <input type="checkbox" class="point-row-checkbox rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                                       value="${pt.id}">
                            </td>
                            <td class="px-6 py-4 font-bold text-secondary-900 point-name-cell">
                                ${escapeHtml(pt.name)}
                                <span class="ml-2 text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-600 text-white">নতুন</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-secondary-800 font-semibold">${escapeHtml(pt.zone_name)}</div>
                                <div class="text-xs text-secondary-400">${escapeHtml(pt.area_name)}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="${APP_BASE}/admin/points/edit?id=${pt.id}" 
                                   class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 inline-block transition-colors mr-1"
                                   title="Edit">
                                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                </a>
                                <form action="${APP_BASE}/admin/points/delete" method="POST" 
                                      class="inline delete-form">
                                    <input type="hidden" name="csrf_token" value="${csrfVal}">
                                    <input type="hidden" name="id" value="${pt.id}">
                                    <input type="hidden" name="zone_id" value="${zoneId}">
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 inline-block transition-colors cursor-pointer"
                                            title="Delete">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </button>
                                </form>
                            </td>
                        `;
                        pointsTbody.prepend(tr);
                        attachDeleteHandler(tr.querySelector('.delete-form'));
                    });
                }

                // Reset inputs ONLY (ZONE REMAINS INTACT!)
                pointRowsList.innerHTML = '';
                addPointRow();
                addPointRow();
                bulkTextarea.value = '';
                updatePointsPreview();

                // Focus back on the first field for seamless multi-entry
                const firstField = pointRowsList.querySelector('.point-input-field');
                if (firstField) firstField.focus();

                // Re-filter table to show newly added points for this zone
                filterTable();
            } else {
                showAlert(data.message || 'পয়েন্ট যোগ করতে সমস্যা হয়েছে।', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            pointForm.submit();
        });
    });

    function showAlert(msg, type = 'success') {
        const isSuccess = (type === 'success');
        const alertDiv = document.createElement('div');
        alertDiv.className = `mb-6 ${isSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'} border px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-xs animate-fade-in`;
        alertDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <ion-icon name="${isSuccess ? 'checkmark-circle' : 'alert-circle'}" class="text-xl ${isSuccess ? 'text-emerald-600' : 'text-red-600'} shrink-0"></ion-icon>
                <span class="font-bold">${escapeHtml(msg)}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="${isSuccess ? 'text-emerald-500 hover:text-emerald-800' : 'text-red-500 hover:text-red-800'} p-1 cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 7000);
    }

    // ==========================================
    // 4. LIVE TABLE FILTERING & SEARCH
    // ==========================================
    function filterTable() {
        const selectedZone = listFilterZone ? listFilterZone.value : '';
        const searchKeyword = pointSearch ? pointSearch.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('.point-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowZone = row.getAttribute('data-zone-id');
            const rowName = row.getAttribute('data-name') || '';
            const rowZoneName = row.getAttribute('data-zone-name') || '';

            const matchesZone = !selectedZone || (rowZone === selectedZone);
            const matchesSearch = !searchKeyword || rowName.includes(searchKeyword) || rowZoneName.includes(searchKeyword);

            if (matchesZone && matchesSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                const cb = row.querySelector('.point-row-checkbox');
                if (cb) cb.checked = false;
            }
        });

        if (filteredCountBadge) {
            filteredCountBadge.textContent = `${visibleCount} টি`;
        }
        updateBulkBar();
    }

    if (listFilterZone) {
        listFilterZone.addEventListener('change', function() {
            filterTable();
            const opt = document.querySelector(`.zone-option[data-id="${this.value}"]`);
            if (opt) {
                setZoneSelected(this.value, opt.getAttribute('data-name'), opt.getAttribute('data-area'), true);
            }
        });
    }

    if (pointSearch) {
        pointSearch.addEventListener('input', filterTable);
    }

    // ==========================================
    // 5. BULK SELECTION & BULK DELETE
    // ==========================================
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCountText = document.getElementById('selectedCountText');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const cancelBulkBtn = document.getElementById('cancelBulkBtn');
    const cleanCorruptedBtn = document.getElementById('cleanCorruptedBtn');

    function getSelectedCheckboxes() {
        return Array.from(document.querySelectorAll('.point-row-checkbox:checked'));
    }

    function updateBulkBar() {
        const checked = getSelectedCheckboxes();
        const count = checked.length;
        if (count > 0) {
            bulkActionBar.classList.remove('hidden');
            selectedCountText.textContent = `${count} টি বাড়ি নির্বাচিত`;
        } else {
            bulkActionBar.classList.add('hidden');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.point-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const cb = row.querySelector('.point-row-checkbox');
                    if (cb) cb.checked = isChecked;
                }
            });
            updateBulkBar();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('point-row-checkbox')) {
            updateBulkBar();
        }
    });

    if (cancelBulkBtn) {
        cancelBulkBtn.addEventListener('click', function() {
            document.querySelectorAll('.point-row-checkbox').forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            updateBulkBar();
        });
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checked = getSelectedCheckboxes();
            const ids = checked.map(cb => cb.value);
            if (ids.length === 0) return;

            if (!confirm(`আপনি কি নিশ্চিত যে নির্বাচিত মোট ${ids.length} টি বাড়ি মুছে ফেলতে চান?`)) {
                return;
            }

            const csrfToken = pointForm.querySelector('input[name="csrf_token"]').value;
            const originalHtml = bulkDeleteBtn.innerHTML;
            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>মুছে ফেলা হচ্ছে...</span>
            `;

            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('is_ajax', '1');
            formData.append('zone_id', zoneIdInput.value);
            ids.forEach(id => formData.append('ids[]', id));

            fetch(`${APP_BASE}/admin/points/bulk-delete`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.innerHTML = originalHtml;
                if (data.success) {
                    showAlert(data.message, 'success');
                    ids.forEach(id => {
                        const tr = document.querySelector(`.point-row[data-id="${id}"]`);
                        if (tr) tr.remove();
                    });
                    updateBulkBar();
                    filterTable();
                } else {
                    showAlert(data.message || 'বাড়িগুলো মুছতে সমস্যা হয়েছে।', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.innerHTML = originalHtml;
                showAlert('সার্ভারের সাথে যোগাযোগে ত্রুটি ঘটেছে।', 'error');
            });
        });
    }

    // ==========================================
    // 6. CLEAN CORRUPTED (??) POINTS
    // ==========================================
    if (cleanCorruptedBtn) {
        cleanCorruptedBtn.addEventListener('click', function() {
            if (!confirm('আপনি কি নিশ্চিত যে সকল ত্রুটিপূর্ণ (??) বা খালি নামের বাড়িগুলো মুছে ফেলতে চান?')) {
                return;
            }

            const csrfToken = pointForm.querySelector('input[name="csrf_token"]').value;
            const originalHtml = cleanCorruptedBtn.innerHTML;
            cleanCorruptedBtn.disabled = true;
            cleanCorruptedBtn.innerHTML = `<span>পরিষ্কার করা হচ্ছে...</span>`;

            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('is_ajax', '1');
            if (zoneIdInput.value) {
                formData.append('zone_id', zoneIdInput.value);
            }

            fetch(`${APP_BASE}/admin/points/clean-corrupted`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                cleanCorruptedBtn.disabled = false;
                cleanCorruptedBtn.innerHTML = originalHtml;
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Remove all corrupted rows from table
                    document.querySelectorAll('.point-row').forEach(row => {
                        const nameCell = row.querySelector('.point-name-cell');
                        const text = nameCell ? nameCell.textContent.trim() : '';
                        if (text === '??' || text === '?' || text === '' || text.includes('??')) {
                            row.remove();
                        }
                    });
                    updateBulkBar();
                    filterTable();
                } else {
                    showAlert(data.message || 'পরিষ্কার করতে সমস্যা হয়েছে।', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                cleanCorruptedBtn.disabled = false;
                cleanCorruptedBtn.innerHTML = originalHtml;
                showAlert('সার্ভারের সাথে যোগাযোগে ত্রুটি ঘটেছে।', 'error');
            });
        });
    }

    // ==========================================
    // 7. AJAX SINGLE ROW DELETE HANDLER
    // ==========================================
    function attachDeleteHandler(form) {
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('আপনি কি নিশ্চিত যে এই পয়েন্টটি মুছে ফেলতে চান?')) {
                return;
            }

            const formData = new FormData(form);
            formData.append('is_ajax', '1');

            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    const tr = form.closest('.point-row');
                    if (tr) tr.remove();
                    updateBulkBar();
                    filterTable();
                } else {
                    if (btn) btn.disabled = false;
                    showAlert(data.message || 'পয়েন্ট মুছতে সমস্যা হয়েছে।', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                form.submit();
            });
        });
    }

    document.querySelectorAll('.delete-form').forEach(form => attachDeleteHandler(form));

    // Trigger initial filter on load
    filterTable();
});
</script>
