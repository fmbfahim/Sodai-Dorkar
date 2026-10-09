<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$selectedAreaId = $_GET['area_id'] ?? $_SESSION['last_zone_area_id'] ?? ($areas[0]['id'] ?? '');
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
                        <ion-icon name="map-outline"></ion-icon>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900 leading-tight">Add Zone (Ward)</h3>
                        <p class="text-xs text-secondary-500">ওয়ার্ড বা ডেলিভারি জোন যোগ করুন</p>
                    </div>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                    ওয়ার্ড তালিকা
                </span>
            </div>

            <form action="<?= $base ?>/admin/zones/store" method="POST" id="zoneForm">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                
                <!-- 1. Searchable Select: Union (Area) -->
                <div class="mb-5">
                    <label class="block text-secondary-800 text-sm font-bold mb-1.5 flex items-center justify-between">
                        <span>Select Union (Area) <span class="text-red-500">*</span></span>
                        <span class="text-[11px] font-normal text-secondary-400">ইউনিয়ন সার্চ করে সিলেক্ট করুন</span>
                    </label>

                    <!-- Hidden Input holding the actual selected area ID -->
                    <input type="hidden" name="area_id" id="area_id_input" value="<?= htmlspecialchars((string)$selectedAreaId) ?>" required>

                    <!-- Searchable Dropdown Container -->
                    <div class="relative" id="areaSelectWrapper">
                        <!-- Trigger Button -->
                        <button type="button" id="areaSelectTrigger" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-semibold text-secondary-800 hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <ion-icon name="business-outline" class="text-primary-600 text-base shrink-0"></ion-icon>
                                <span id="areaSelectedLabel" class="truncate">ইউনিয়ন নির্বাচন করুন...</span>
                            </div>
                            <ion-icon name="chevron-down-outline" id="areaSelectChevron" class="text-secondary-400 text-base shrink-0 transition-transform duration-200"></ion-icon>
                        </button>

                        <!-- Floating Searchable Menu -->
                        <div id="areaDropdownMenu" 
                             class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-secondary-200 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                            <!-- Search Field Inside Dropdown -->
                            <div class="p-2.5 border-b border-secondary-100 bg-secondary-50/50">
                                <div class="relative">
                                    <ion-icon name="search-outline" class="absolute left-3 top-2.5 text-secondary-400 text-sm"></ion-icon>
                                    <input type="text" id="areaSearchInput" 
                                           placeholder="ইউনিয়ন বা এরিয়া সার্চ করুন..." 
                                           autocomplete="off"
                                           class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-secondary-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 text-secondary-800 font-medium placeholder-secondary-400">
                                </div>
                            </div>

                            <!-- Options List -->
                            <div id="areaOptionsList" class="max-h-60 overflow-y-auto divide-y divide-secondary-100/60 text-xs">
                                <?php foreach ($areas as $area): 
                                    $isSelected = ((string)$selectedAreaId === (string)$area['id']);
                                ?>
                                    <div class="area-option px-3.5 py-2.5 hover:bg-primary-50/80 cursor-pointer flex items-center justify-between transition-colors <?= $isSelected ? 'bg-primary-50 text-primary-900 font-bold' : 'text-secondary-700' ?>"
                                         data-id="<?= $area['id'] ?>"
                                         data-name="<?= htmlspecialchars($area['name']) ?>">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $isSelected ? 'bg-primary-600' : 'bg-secondary-300' ?> shrink-0"></span>
                                            <span class="font-medium truncate"><?= htmlspecialchars($area['name']) ?></span>
                                        </div>
                                        <?php if ($isSelected): ?>
                                            <ion-icon name="checkmark-circle" class="text-primary-600 text-sm shrink-0 checkmark-icon"></ion-icon>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Empty Search Result -->
                            <div id="areaNoResults" class="hidden px-4 py-6 text-center text-xs text-secondary-400">
                                <ion-icon name="alert-circle-outline" class="text-lg mb-1"></ion-icon>
                                <p>কোনো ইউনিয়ন বা এরিয়া পাওয়া যায়নি</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-emerald-600 mt-1.5 flex items-center gap-1 font-medium">
                        <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                        <span>ওয়ার্ড এড করার পরও এই ইউনিয়নটি সবসময় ফিক্সড থাকবে (রিসেট হবে না)।</span>
                    </p>
                </div>

                <!-- 2. Zone Name(s) with Multi-Add Support -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-secondary-800 text-sm font-bold">
                            Zone Name (Ward No / ওয়ার্ডের নাম) <span class="text-red-500">*</span>
                        </label>
                        <span id="zoneCountBadge" class="hidden text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 transition-all shadow-2xs">
                            ১টি ওয়ার্ড
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
                        <div id="zoneRowsList" class="space-y-2">
                            <!-- Rows will be injected here via JS -->
                        </div>
                        <button type="button" onclick="addZoneRow()" 
                                class="w-full py-2 px-3 border border-dashed border-secondary-300 hover:border-primary-500 hover:bg-primary-50/50 text-secondary-600 hover:text-primary-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                            <span>+ আরও একটি ওয়ার্ড যোগ করুন (Add Row)</span>
                        </button>
                    </div>

                    <!-- Mode B: Bulk Paste Textarea -->
                    <div id="bulkModeContainer" class="hidden">
                        <textarea id="bulkTextarea" name="name" rows="4" 
                                  placeholder="যেমন:&#10;১ নং ওয়ার্ড&#10;২ নং ওয়ার্ড&#10;৩ নং ওয়ার্ড&#10;অথবা কমা (,) দিয়ে: ১ নং ওয়ার্ড, ২ নং ওয়ার্ড, ৩ নং ওয়ার্ড" 
                                  class="w-full px-3.5 py-2.5 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 font-sans text-secondary-800 placeholder-secondary-400"></textarea>
                    </div>

                    <!-- Rule Hint -->
                    <div class="mt-2 text-[11px] text-secondary-500 bg-secondary-50 p-2.5 rounded-lg border border-secondary-200/80 flex items-start gap-1.5">
                        <ion-icon name="bulb-outline" class="text-amber-500 text-sm shrink-0 mt-0.5"></ion-icon>
                        <span>এন্ট্রি লেখার সময় <kbd class="px-1 py-0.5 bg-white border border-secondary-200 rounded text-[10px]">Enter</kbd> চাপলে স্বয়ংক্রিয়ভাবে পরবর্তী ঘর তৈরি হবে। কমা (,) বা নতুন লাইনে লিখলেও আলাদা ওয়ার্ড তৈরি হবে।</span>
                    </div>

                    <!-- Live Detected Chips Preview -->
                    <div id="chipsContainer" class="hidden mt-3 flex flex-wrap gap-1.5 max-h-28 overflow-y-auto p-2 bg-emerald-50/60 border border-emerald-200 rounded-xl"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <ion-icon name="add-circle" class="text-lg"></ion-icon>
                    <span id="submitBtnText">Add Zone (ওয়ার্ড যোগ করুন)</span>
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
                    <h3 class="font-bold text-secondary-900 text-base">Zone List</h3>
                    <span id="filteredCountBadge" class="text-xs bg-secondary-100 text-secondary-700 font-bold px-2.5 py-0.5 rounded-full border border-secondary-200">
                        <?= count($zones) ?> টি
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Clean Corrupted (??) Button -->
                    <button type="button" id="cleanCorruptedBtn" title="ত্রুটিপূর্ণ (??) বা অকেজো ওয়ার্ডগুলো স্বয়ংক্রিয়ভাবে মুছে ফেলুন" 
                            class="text-xs font-bold px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap">
                        <ion-icon name="trash-bin-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>অকেজো (??) ওয়ার্ড মুছুন</span>
                    </button>

                    <!-- Area Filter -->
                    <select id="listFilterArea" class="text-xs font-semibold border border-secondary-300 rounded-xl px-3 py-2 bg-white text-secondary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="">সকল ইউনিয়ন (All Unions)</option>
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>" <?= ((string)$selectedAreaId === (string)$area['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($area['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="zoneSearch" placeholder="ওয়ার্ড বা ইউনিয়ন খুঁজুন..." 
                                class="text-xs border border-secondary-300 rounded-xl pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500/20 w-full sm:w-44 text-secondary-800 font-medium">
                        <ion-icon name="search-outline" class="absolute left-2.5 top-2.5 text-secondary-400 text-sm"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Floating / Inline Bulk Action Bar -->
            <div id="bulkActionBar" class="hidden px-5 py-3 bg-red-50 border-b border-red-200 flex flex-wrap items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2">
                    <ion-icon name="checkbox-outline" class="text-red-600 text-lg"></ion-icon>
                    <span class="text-xs font-bold text-red-800" id="selectedCountText">০ টি ওয়ার্ড নির্বাচিত</span>
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
                <table class="w-full text-left text-sm text-secondary-600" id="zonesTable">
                    <thead class="bg-secondary-50/80 text-secondary-500 text-xs uppercase tracking-wider font-bold">
                        <tr>
                            <th class="w-10 px-4 py-3.5 text-center">
                                <input type="checkbox" id="selectAllCheckbox" title="সবগুলো নির্বাচন করুন" 
                                       class="rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-3.5">Zone Name</th>
                            <th class="px-6 py-3.5">Union (Area)</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100" id="zonesTbody">
                         <?php if (empty($zones)): ?>
                            <tr id="emptyRow">
                                <td colspan="4" class="px-6 py-10 text-center text-secondary-400">
                                    <ion-icon name="map-outline" class="text-3xl text-secondary-300 mb-2"></ion-icon>
                                    <p class="font-medium">কোনো ওয়ার্ড পাওয়া যায়নি। বাম পাশের ফর্ম থেকে নতুন ওয়ার্ড যোগ করুন।</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($zones as $zone): ?>
                                <tr class="hover:bg-secondary-50/80 transition-colors zone-row" 
                                    data-id="<?= $zone['id'] ?>"
                                    data-area-id="<?= $zone['area_id'] ?>" 
                                    data-name="<?= htmlspecialchars(mb_strtolower($zone['name'])) ?>"
                                    data-area-name="<?= htmlspecialchars(mb_strtolower($zone['area_name'])) ?>">
                                    <td class="w-10 px-4 py-4 text-center">
                                        <input type="checkbox" class="zone-row-checkbox rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                                               value="<?= $zone['id'] ?>">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-secondary-900 zone-name-cell">
                                        <?= htmlspecialchars($zone['name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-secondary-800 font-semibold"><?= htmlspecialchars($zone['area_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="<?= $base ?>/admin/zones/edit?id=<?= $zone['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 inline-block transition-colors mr-1"
                                           title="Edit">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="<?= $base ?>/admin/zones/delete" method="POST" 
                                              onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ওয়ার্ডটি মুছে ফেলতে চান?');" 
                                              class="inline delete-form">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?= $zone['id']; ?>">
                                            <input type="hidden" name="area_id" value="<?= $selectedAreaId ?>">
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
    const areaIdInput = document.getElementById('area_id_input');
    const areaTrigger = document.getElementById('areaSelectTrigger');
    const areaChevron = document.getElementById('areaSelectChevron');
    const areaLabel = document.getElementById('areaSelectedLabel');
    const areaDropdown = document.getElementById('areaDropdownMenu');
    const areaSearchInput = document.getElementById('areaSearchInput');
    const areaOptions = document.querySelectorAll('.area-option');
    const areaNoResults = document.getElementById('areaNoResults');

    const zoneForm = document.getElementById('zoneForm');
    const zoneRowsList = document.getElementById('zoneRowsList');
    const bulkTextarea = document.getElementById('bulkTextarea');
    const chipsContainer = document.getElementById('chipsContainer');
    const badge = document.getElementById('zoneCountBadge');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const alertContainer = document.getElementById('alertContainer');

    const listFilterArea = document.getElementById('listFilterArea');
    const zoneSearch = document.getElementById('zoneSearch');
    const filteredCountBadge = document.getElementById('filteredCountBadge');
    const zonesTbody = document.getElementById('zonesTbody');

    let currentInputMode = 'rows'; // 'rows' or 'bulk'

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ==========================================
    // 1. SEARCHABLE AREA SELECT LOGIC
    // ==========================================
    function setAreaSelected(id, name, saveLocal = true) {
        if (!id) return;
        areaIdInput.value = id;
        areaLabel.textContent = name;
        
        // Update checkmarks in options
        areaOptions.forEach(opt => {
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
            localStorage.setItem('admin_last_zone_area_id', id);
        }

        // Synchronize table filter with selected area
        if (listFilterArea) {
            listFilterArea.value = id;
            filterTable();
        }
    }

    // Dropdown open/close
    function toggleAreaDropdown(open) {
        if (open === undefined) open = areaDropdown.classList.contains('hidden');
        if (open) {
            areaDropdown.classList.remove('hidden');
            areaChevron.classList.add('rotate-180');
            areaSearchInput.value = '';
            filterAreaOptions('');
            setTimeout(() => areaSearchInput.focus(), 50);
        } else {
            areaDropdown.classList.add('hidden');
            areaChevron.classList.remove('rotate-180');
        }
    }

    areaTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleAreaDropdown();
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('areaSelectWrapper').contains(e.target)) {
            toggleAreaDropdown(false);
        }
    });

    // Real-time Search in Area Dropdown
    function filterAreaOptions(query) {
        query = query.trim().toLowerCase();
        let matches = 0;
        areaOptions.forEach(opt => {
            const name = (opt.getAttribute('data-name') || '').toLowerCase();
            if (!query || name.includes(query)) {
                opt.style.display = '';
                matches++;
            } else {
                opt.style.display = 'none';
            }
        });
        if (areaNoResults) {
            areaNoResults.classList.toggle('hidden', matches > 0);
        }
    }

    areaSearchInput.addEventListener('input', (e) => {
        filterAreaOptions(e.target.value);
    });

    // Click on option
    areaOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            const id = opt.getAttribute('data-id');
            const name = opt.getAttribute('data-name');
            setAreaSelected(id, name, true);
            toggleAreaDropdown(false);
        });
    });

    // Initialize Area selection from LocalStorage / URL / Default
    const urlParams = new URLSearchParams(window.location.search);
    const urlAreaId = urlParams.get('area_id');
    const savedAreaId = localStorage.getItem('admin_last_zone_area_id');

    let initAreaId = urlAreaId || areaIdInput.value || savedAreaId;
    let targetOpt = document.querySelector(`.area-option[data-id="${initAreaId}"]`);
    if (!targetOpt && areaOptions.length > 0) {
        targetOpt = areaOptions[0];
    }
    if (targetOpt) {
        setAreaSelected(
            targetOpt.getAttribute('data-id'),
            targetOpt.getAttribute('data-name'),
            true
        );
    }

    // ==========================================
    // 2. MULTI-ZONE DYNAMIC INPUTS LOGIC
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
        updateZonesPreview();
    };

    window.addZoneRow = function(value = '') {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 zone-row-item animate-fade-in';
        row.innerHTML = `
            <div class="relative flex-1">
                <input type="text" name="names[]" value="${escapeHtml(value)}" 
                       placeholder="ওয়ার্ডের নাম লিখুন (যেমন: ১ নং ওয়ার্ড)..." 
                       class="zone-input-field w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-secondary-800 font-medium placeholder-secondary-400">
            </div>
            <button type="button" onclick="removeZoneRow(this)" 
                    class="p-2 text-secondary-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors cursor-pointer" title="এই ঘরটি মুছুন">
                <ion-icon name="close-circle-outline" class="text-xl"></ion-icon>
            </button>
        `;
        zoneRowsList.appendChild(row);

        const input = row.querySelector('.zone-input-field');
        input.addEventListener('input', updateZonesPreview);
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addZoneRow();
            }
        });

        if (!value) {
            setTimeout(() => input.focus(), 50);
        }
        updateZonesPreview();
    };

    window.removeZoneRow = function(btn) {
        const rows = zoneRowsList.querySelectorAll('.zone-row-item');
        if (rows.length > 1) {
            btn.closest('.zone-row-item').remove();
        } else {
            // If only 1 row left, just clear it
            const input = rows[0].querySelector('.zone-input-field');
            if (input) input.value = '';
        }
        updateZonesPreview();
    };

    // Initialize 2 default rows
    addZoneRow();
    addZoneRow();

    bulkTextarea.addEventListener('input', updateZonesPreview);

    function getCollectedNames() {
        const names = [];
        if (currentInputMode === 'rows') {
            const inputs = zoneRowsList.querySelectorAll('.zone-input-field');
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

    function updateZonesPreview() {
        const items = getCollectedNames();
        if (items.length === 0) {
            badge.classList.add('hidden');
            chipsContainer.classList.add('hidden');
            chipsContainer.innerHTML = '';
            submitBtnText.textContent = 'Add Zone (ওয়ার্ড যোগ করুন)';
        } else if (items.length === 1) {
            badge.classList.remove('hidden');
            badge.textContent = '১টি ওয়ার্ড';
            chipsContainer.classList.add('hidden');
            chipsContainer.innerHTML = '';
            submitBtnText.textContent = 'Add Zone (১টি ওয়ার্ড যোগ করুন)';
        } else {
            badge.classList.remove('hidden');
            badge.textContent = `মোট ${items.length}টি ওয়ার্ড`;
            chipsContainer.classList.remove('hidden');
            chipsContainer.innerHTML = items.map(item => `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-800 text-xs font-bold shadow-2xs">
                    <ion-icon name="checkmark-circle" class="text-emerald-600 text-sm"></ion-icon>
                    <span>${escapeHtml(item)}</span>
                </span>
            `).join('');
            submitBtnText.textContent = `Add ${items.length} Zones (একত্রে ${items.length}টি ওয়ার্ড যোগ করুন)`;
        }
    }

    // ==========================================
    // 3. AJAX SUBMISSION WITHOUT RESETING AREA!
    // ==========================================
    zoneForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const areaId = areaIdInput.value;
        const items = getCollectedNames();

        if (!areaId) {
            showAlert('দয়া করে একটি ইউনিয়ন (Area) নির্বাচন করুন।', 'error');
            toggleAreaDropdown(true);
            return;
        }

        if (items.length === 0) {
            showAlert('দয়া করে অন্তত একটি ওয়ার্ডের নাম লিখুন।', 'error');
            const firstInp = zoneRowsList.querySelector('.zone-input-field');
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
        const csrfVal = zoneForm.querySelector('input[name="csrf_token"]').value;
        formData.append('csrf_token', csrfVal);
        formData.append('area_id', areaId);
        formData.append('is_ajax', '1');
        items.forEach(name => formData.append('names[]', name));

        fetch(zoneForm.action, {
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

                // Prepend newly added zones to the table
                const emptyRow = document.getElementById('emptyRow');
                if (emptyRow) emptyRow.remove();

                if (Array.isArray(data.zones)) {
                    data.zones.forEach(zn => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-secondary-50/80 transition-colors zone-row bg-emerald-50/60 animate-fade-in';
                        tr.setAttribute('data-id', zn.id);
                        tr.setAttribute('data-area-id', zn.area_id);
                        tr.setAttribute('data-name', (zn.name || '').toLowerCase());
                        tr.setAttribute('data-area-name', (zn.area_name || '').toLowerCase());
                        tr.innerHTML = `
                            <td class="w-10 px-4 py-4 text-center">
                                <input type="checkbox" class="zone-row-checkbox rounded border-secondary-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                                       value="${zn.id}">
                            </td>
                            <td class="px-6 py-4 font-bold text-secondary-900 zone-name-cell">
                                ${escapeHtml(zn.name)}
                                <span class="ml-2 text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-600 text-white">নতুন</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-secondary-800 font-semibold">${escapeHtml(zn.area_name)}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="${APP_BASE}/admin/zones/edit?id=${zn.id}" 
                                   class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 inline-block transition-colors mr-1"
                                   title="Edit">
                                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                </a>
                                <form action="${APP_BASE}/admin/zones/delete" method="POST" 
                                      class="inline delete-form">
                                    <input type="hidden" name="csrf_token" value="${csrfVal}">
                                    <input type="hidden" name="id" value="${zn.id}">
                                    <input type="hidden" name="area_id" value="${areaId}">
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 inline-block transition-colors cursor-pointer"
                                            title="Delete">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </button>
                                </form>
                            </td>
                        `;
                        zonesTbody.prepend(tr);
                        attachDeleteHandler(tr.querySelector('.delete-form'));
                    });
                }

                // Reset inputs ONLY (AREA REMAINS INTACT!)
                zoneRowsList.innerHTML = '';
                addZoneRow();
                addZoneRow();
                bulkTextarea.value = '';
                updateZonesPreview();

                // Focus back on the first field for seamless multi-entry
                const firstField = zoneRowsList.querySelector('.zone-input-field');
                if (firstField) firstField.focus();

                // Re-filter table to show newly added zones for this area
                filterTable();
            } else {
                showAlert(data.message || 'ওয়ার্ড যোগ করতে সমস্যা হয়েছে।', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            zoneForm.submit();
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
        const selectedArea = listFilterArea ? listFilterArea.value : '';
        const searchKeyword = zoneSearch ? zoneSearch.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('.zone-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowArea = row.getAttribute('data-area-id');
            const rowName = row.getAttribute('data-name') || '';
            const rowAreaName = row.getAttribute('data-area-name') || '';

            const matchesArea = !selectedArea || (rowArea === selectedArea);
            const matchesSearch = !searchKeyword || rowName.includes(searchKeyword) || rowAreaName.includes(searchKeyword);

            if (matchesArea && matchesSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                const cb = row.querySelector('.zone-row-checkbox');
                if (cb) cb.checked = false;
            }
        });

        if (filteredCountBadge) {
            filteredCountBadge.textContent = `${visibleCount} টি`;
        }
        updateBulkBar();
    }

    if (listFilterArea) {
        listFilterArea.addEventListener('change', function() {
            filterTable();
            const opt = document.querySelector(`.area-option[data-id="${this.value}"]`);
            if (opt) {
                setAreaSelected(this.value, opt.getAttribute('data-name'), true);
            }
        });
    }

    if (zoneSearch) {
        zoneSearch.addEventListener('input', filterTable);
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
        return Array.from(document.querySelectorAll('.zone-row-checkbox:checked'));
    }

    function updateBulkBar() {
        const checked = getSelectedCheckboxes();
        const count = checked.length;
        if (count > 0) {
            bulkActionBar.classList.remove('hidden');
            selectedCountText.textContent = `${count} টি ওয়ার্ড নির্বাচিত`;
        } else {
            bulkActionBar.classList.add('hidden');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.zone-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const cb = row.querySelector('.zone-row-checkbox');
                    if (cb) cb.checked = isChecked;
                }
            });
            updateBulkBar();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('zone-row-checkbox')) {
            updateBulkBar();
        }
    });

    if (cancelBulkBtn) {
        cancelBulkBtn.addEventListener('click', function() {
            document.querySelectorAll('.zone-row-checkbox').forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            updateBulkBar();
        });
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checked = getSelectedCheckboxes();
            const ids = checked.map(cb => cb.value);
            if (ids.length === 0) return;

            if (!confirm(`আপনি কি নিশ্চিত যে নির্বাচিত মোট ${ids.length} টি ওয়ার্ড মুছে ফেলতে চান?`)) {
                return;
            }

            const csrfToken = zoneForm.querySelector('input[name="csrf_token"]').value;
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
            formData.append('area_id', areaIdInput.value);
            ids.forEach(id => formData.append('ids[]', id));

            fetch(`${APP_BASE}/admin/zones/bulk-delete`, {
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
                        const tr = document.querySelector(`.zone-row[data-id="${id}"]`);
                        if (tr) tr.remove();
                    });
                    updateBulkBar();
                    filterTable();
                } else {
                    showAlert(data.message || 'ওয়ার্ডগুলো মুছতে সমস্যা হয়েছে।', 'error');
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
    // 6. CLEAN CORRUPTED (??) ZONES
    // ==========================================
    if (cleanCorruptedBtn) {
        cleanCorruptedBtn.addEventListener('click', function() {
            if (!confirm('আপনি কি নিশ্চিত যে সকল ত্রুটিপূর্ণ (??) বা খালি নামের ওয়ার্ডগুলো মুছে ফেলতে চান?')) {
                return;
            }

            const csrfToken = zoneForm.querySelector('input[name="csrf_token"]').value;
            const originalHtml = cleanCorruptedBtn.innerHTML;
            cleanCorruptedBtn.disabled = true;
            cleanCorruptedBtn.innerHTML = `<span>পরিষ্কার করা হচ্ছে...</span>`;

            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('is_ajax', '1');
            if (areaIdInput.value) {
                formData.append('area_id', areaIdInput.value);
            }

            fetch(`${APP_BASE}/admin/zones/clean-corrupted`, {
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
                    document.querySelectorAll('.zone-row').forEach(row => {
                        const nameCell = row.querySelector('.zone-name-cell');
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
            if (!confirm('আপনি কি নিশ্চিত যে এই ওয়ার্ডটি মুছে ফেলতে চান?')) {
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
                    const tr = form.closest('.zone-row');
                    if (tr) tr.remove();
                    updateBulkBar();
                    filterTable();
                } else {
                    if (btn) btn.disabled = false;
                    showAlert(data.message || 'ওয়ার্ড মুছতে সমস্যা হয়েছে।', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                // Fallback to regular submit
                form.submit();
            });
        });
    }

    document.querySelectorAll('.delete-form').forEach(form => attachDeleteHandler(form));

    // Trigger initial filter on load
    filterTable();
});
</script>
