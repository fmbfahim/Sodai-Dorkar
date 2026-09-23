<div class="max-w-6xl mx-auto mb-16">

    <style>
    /* Custom Styled Radio Cards */
    .radio-select-card {
        position: relative;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background-color: #ffffff;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .radio-select-card:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
    }
    
    /* Radio Dot Indicator (Custom visible circular radio button) */
    .radio-indicator {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .radio-indicator-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        transform: scale(0);
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Emerald Theme Active (Auto Assign, Allowed, Live Store) */
    input[type="radio"][data-theme="emerald"]:checked + .radio-select-card,
    .radio-select-card.is-selected-emerald {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 4px 14px -2px rgba(16, 185, 129, 0.22) !important;
    }
    input[type="radio"][data-theme="emerald"]:checked + .radio-select-card .radio-indicator,
    .is-selected-emerald .radio-indicator {
        border-color: #10b981 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    }
    input[type="radio"][data-theme="emerald"]:checked + .radio-select-card .radio-indicator-dot,
    .is-selected-emerald .radio-indicator-dot {
        background-color: #059669 !important;
        transform: scale(1) !important;
    }
    input[type="radio"][data-theme="emerald"]:checked + .radio-select-card ion-icon,
    .is-selected-emerald ion-icon {
        color: #059669 !important;
    }
    .is-selected-emerald [data-status-dot] {
        background-color: #10b981 !important;
    }
    .is-selected-emerald [data-status-badge] {
        background-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    /* Blue Theme Active (Manual Assign, Catalog Mode) */
    input[type="radio"][data-theme="blue"]:checked + .radio-select-card,
    .radio-select-card.is-selected-blue {
        border-color: #3b82f6 !important;
        background-color: #eff6ff !important;
        box-shadow: 0 4px 14px -2px rgba(59, 130, 246, 0.22) !important;
    }
    input[type="radio"][data-theme="blue"]:checked + .radio-select-card .radio-indicator,
    .is-selected-blue .radio-indicator {
        border-color: #3b82f6 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    }
    input[type="radio"][data-theme="blue"]:checked + .radio-select-card .radio-indicator-dot,
    .is-selected-blue .radio-indicator-dot {
        background-color: #2563eb !important;
        transform: scale(1) !important;
    }
    input[type="radio"][data-theme="blue"]:checked + .radio-select-card ion-icon,
    .is-selected-blue ion-icon {
        color: #2563eb !important;
    }
    .is-selected-blue [data-status-dot] {
        background-color: #3b82f6 !important;
    }
    .is-selected-blue [data-status-badge] {
        background-color: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    /* Amber Theme Active (Maintenance Mode) */
    input[type="radio"][data-theme="amber"]:checked + .radio-select-card,
    .radio-select-card.is-selected-amber {
        border-color: #f59e0b !important;
        background-color: #fffbeb !important;
        box-shadow: 0 4px 14px -2px rgba(245, 158, 11, 0.25) !important;
    }
    input[type="radio"][data-theme="amber"]:checked + .radio-select-card .radio-indicator,
    .is-selected-amber .radio-indicator {
        border-color: #f59e0b !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
    }
    input[type="radio"][data-theme="amber"]:checked + .radio-select-card .radio-indicator-dot,
    .is-selected-amber .radio-indicator-dot {
        background-color: #d97706 !important;
        transform: scale(1) !important;
    }
    input[type="radio"][data-theme="amber"]:checked + .radio-select-card ion-icon,
    .is-selected-amber ion-icon {
        color: #d97706 !important;
    }
    .is-selected-amber [data-status-dot] {
        background-color: #f59e0b !important;
    }
    .is-selected-amber [data-status-badge] {
        background-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    /* Rose Theme Active (Disabled) */
    input[type="radio"][data-theme="rose"]:checked + .radio-select-card,
    .radio-select-card.is-selected-rose {
        border-color: #f43f5e !important;
        background-color: #fff1f2 !important;
        box-shadow: 0 4px 14px -2px rgba(244, 63, 94, 0.22) !important;
    }
    input[type="radio"][data-theme="rose"]:checked + .radio-select-card .radio-indicator,
    .is-selected-rose .radio-indicator {
        border-color: #f43f5e !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.2) !important;
    }
    input[type="radio"][data-theme="rose"]:checked + .radio-select-card .radio-indicator-dot,
    .is-selected-rose .radio-indicator-dot {
        background-color: #e11d48 !important;
        transform: scale(1) !important;
    }
    input[type="radio"][data-theme="rose"]:checked + .radio-select-card ion-icon,
    .is-selected-rose ion-icon {
        color: #e11d48 !important;
    }

    /* Pulse animation for active status dot */
    .is-selected-emerald [data-status-dot],
    .is-selected-amber [data-status-dot],
    .is-selected-blue [data-status-dot] {
        animation: statusPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;
    }
    @keyframes statusPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Unselected default states */
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) {
        border-color: #e2e8f0;
        background-color: #ffffff;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) ion-icon {
        color: #94a3b8 !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) .radio-indicator {
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) .radio-indicator-dot {
        transform: scale(0) !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) [data-status-dot] {
        background-color: #cbd5e1 !important;
        animation: none !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-amber):not(.is-selected-rose) [data-status-badge] {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        box-shadow: none !important;
    }

    /* ====================================================================
       CUSTOM ON/OFF TOGGLE SWITCHES
       ==================================================================== */
    .custom-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        flex-shrink: 0;
        width: 48px;
        height: 26px;
    }
    .custom-toggle input[type="checkbox"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
        margin: 0;
    }
    .custom-toggle .toggle-track {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        background-color: #cbd5e1;
        border-radius: 9999px;
        transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12);
        pointer-events: none;
    }
    .custom-toggle .toggle-thumb {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background-color: #ffffff;
        border-radius: 9999px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }
    /* When checked: green background and slide right */
    .custom-toggle input[type="checkbox"]:checked + .toggle-track,
    .custom-toggle input[type="checkbox"]:checked ~ .toggle-track {
        background-color: #059669 !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12), 0 0 0 1px #047857 !important;
    }
    .custom-toggle input[type="checkbox"]:checked + .toggle-track .toggle-thumb,
    .custom-toggle input[type="checkbox"]:checked ~ .toggle-track .toggle-thumb {
        transform: translateX(22px) !important;
    }
    /* When unchecked: gray background and slide left (guaranteed override) */
    .custom-toggle input[type="checkbox"]:not(:checked) + .toggle-track,
    .custom-toggle input[type="checkbox"]:not(:checked) ~ .toggle-track {
        background-color: #cbd5e1 !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12) !important;
    }
    .custom-toggle input[type="checkbox"]:not(:checked) + .toggle-track .toggle-thumb,
    .custom-toggle input[type="checkbox"]:not(:checked) ~ .toggle-track .toggle-thumb {
        transform: translateX(0) !important;
    }
    .custom-toggle:hover .toggle-track {
        filter: brightness(0.96);
    }
    .custom-toggle input[type="checkbox"]:focus-visible + .toggle-track {
        outline: 2px solid #10b981;
        outline-offset: 2px;
    }
    </style>
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-1.5 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                Store Configuration
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-secondary-900 tracking-tight">E-Commerce Management</h1>
            <p class="text-secondary-500 text-xs sm:text-sm mt-0.5">Configure products, shipping, payment methods, checkout privacy, and site visibility.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/sodai-dorkar/public/admin/settings" class="px-3.5 py-2 text-xs font-bold text-secondary-600 bg-white hover:bg-secondary-50 border border-secondary-200 rounded-xl transition-colors shadow-2xs flex items-center gap-1.5">
                <ion-icon name="settings-outline" class="text-base"></ion-icon>
                <span>General Settings</span>
            </a>
            <a href="/sodai-dorkar/public/admin/settings/units" class="px-3.5 py-2 text-xs font-bold text-secondary-600 bg-white hover:bg-secondary-50 border border-secondary-200 rounded-xl transition-colors shadow-2xs flex items-center gap-1.5">
                <ion-icon name="scale-outline" class="text-base"></ion-icon>
                <span>Units & Packaging</span>
            </a>
        </div>
    </div>

    <!-- Success Feedback Alert -->
    <?php if (isset($_GET['success'])): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-xs mb-6 flex items-center justify-between" role="alert">
        <div class="flex items-center gap-3">
            <ion-icon name="checkmark-circle" class="text-2xl text-emerald-600 shrink-0"></ion-icon>
            <div>
                <p class="font-bold text-sm">Settings saved successfully!</p>
                <p class="text-xs text-emerald-700/80">E-Commerce management configurations have been updated.</p>
            </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
            <ion-icon name="close" class="text-xl"></ion-icon>
        </button>
    </div>
    <?php endif; ?>

    <!-- Main Navigation Tabs -->
    <div class="bg-white rounded-2xl border border-secondary-200/90 p-1.5 shadow-xs mb-6 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-1 min-w-max">
            
            <!-- Tab 1: Products -->
            <a href="<?= $base ?>/admin/ecommerce-settings?tab=products" 
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all <?= $activeTab === 'products' ? 'bg-emerald-600 text-white shadow-sm' : 'text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50' ?>">
                <ion-icon name="cube-outline" class="text-lg"></ion-icon>
                <span>Products</span>
            </a>

            <!-- Tab 2: Shipping -->
            <a href="<?= $base ?>/admin/ecommerce-settings?tab=shipping" 
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all <?= $activeTab === 'shipping' ? 'bg-emerald-600 text-white shadow-sm' : 'text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50' ?>">
                <ion-icon name="bicycle-outline" class="text-lg"></ion-icon>
                <span>Shipping</span>
            </a>

            <!-- Tab 3: Payments -->
            <a href="<?= $base ?>/admin/ecommerce-settings?tab=payments" 
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all <?= $activeTab === 'payments' ? 'bg-emerald-600 text-white shadow-sm' : 'text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50' ?>">
                <ion-icon name="card-outline" class="text-lg"></ion-icon>
                <span>Payments</span>
            </a>

            <!-- Tab 4: Accounts & Privacy -->
            <a href="<?= $base ?>/admin/ecommerce-settings?tab=privacy" 
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all <?= $activeTab === 'privacy' ? 'bg-emerald-600 text-white shadow-sm' : 'text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50' ?>">
                <ion-icon name="shield-checkmark-outline" class="text-lg"></ion-icon>
                <span>Accounts & Privacy</span>
            </a>

            <!-- Tab 5: Site Visibility -->
            <a href="<?= $base ?>/admin/ecommerce-settings?tab=visibility" 
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all <?= $activeTab === 'visibility' ? 'bg-emerald-600 text-white shadow-sm' : 'text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50' ?>">
                <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                <span>Site Visibility</span>
            </a>

        </div>
    </div>

    <!-- Active Tab Form Container -->
    <form action="<?= $base ?>/admin/ecommerce-settings/update" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="active_tab" value="<?= htmlspecialchars($activeTab) ?>">

        <!-- =========================================================
             TAB 1: PRODUCTS
             ========================================================= -->
        <?php if ($activeTab === 'products'): ?>
        
        <!-- Stock & Inventory Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                        <ion-icon name="file-tray-stacked-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Inventory & Stock Tracking</h3>
                        <p class="text-[11px] text-secondary-400">Manage real-time inventory decrement and product stock visibility.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5 divide-y divide-secondary-100">
                <!-- Manage Stock Toggle -->
                <div class="flex items-start justify-between gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="manage_stock_enabled">
                            Manage Stock Automatically
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Automatically decrease inventory quantity when customer orders are placed and confirmed.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="manage_stock_enabled" name="manage_stock_enabled" value="1" 
                               <?= ($settings['manage_stock_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['manage_stock_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Low Stock Threshold -->
                <div class="pt-5 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block font-bold text-xs sm:text-sm text-secondary-800 mb-1">
                            Low Stock Alert Threshold
                        </label>
                        <p class="text-xs text-secondary-500">Trigger dashboard warnings when product stock falls to or below this quantity.</p>
                    </div>
                    <div class="max-w-xs">
                        <div class="relative">
                            <input type="number" name="low_stock_threshold" 
                                   value="<?= htmlspecialchars($settings['low_stock_threshold'] ?? '5') ?>" 
                                   min="0"
                                   class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-bold">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-secondary-400 font-semibold">units</span>
                        </div>
                    </div>
                </div>

                <!-- Hide Out of Stock Products -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="hide_out_of_stock_products">
                            Hide Out of Stock Items
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            When enabled, products with zero stock will be automatically hidden from customer storefront search and categories.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="hide_out_of_stock_products" name="hide_out_of_stock_products" value="1" 
                               <?= ($settings['hide_out_of_stock_products'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['hide_out_of_stock_products'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Pricing & Display Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                    <ion-icon name="pricetags-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Pricing & Badge Displays</h3>
                    <p class="text-[11px] text-secondary-400">Control discount badges, price displays, and taxation rules.</p>
                </div>
            </div>

            <div class="p-6 space-y-5 divide-y divide-secondary-100">
                <!-- Show Discount Badge -->
                <div class="flex items-start justify-between gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="show_discount_badge">
                            Show Discount Percentage Badge
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Display a percentage savings badge (e.g. "20% OFF") on product cards when a discounted price is available.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="show_discount_badge" name="show_discount_badge" value="1" 
                               <?= ($settings['show_discount_badge'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['show_discount_badge'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Prices Include Tax -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="prices_include_tax">
                            Prices Include Tax / VAT
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Specify that all listed product prices already include applicable government taxes and VAT.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="prices_include_tax" name="prices_include_tax" value="1" 
                               <?= ($settings['prices_include_tax'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['prices_include_tax'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Product Reviews & Ratings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg">
                    <ion-icon name="star-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Customer Reviews & Ratings</h3>
                    <p class="text-[11px] text-secondary-400">Define customer feedback policies and review verification rules.</p>
                </div>
            </div>

            <div class="p-6 space-y-5 divide-y divide-secondary-100">
                <!-- Enable Product Reviews -->
                <div class="flex items-start justify-between gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="enable_product_reviews">
                            Enable Product Reviews
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Allow customers to submit star ratings and written feedback on product detail pages.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="enable_product_reviews" name="enable_product_reviews" value="1" 
                               <?= ($settings['enable_product_reviews'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['enable_product_reviews'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Verified Buyers Only -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="reviews_verified_buyers_only">
                            Verified Buyers Only
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Only customers who have purchased and successfully received this item are allowed to leave reviews.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="reviews_verified_buyers_only" name="reviews_verified_buyers_only" value="1" 
                               <?= ($settings['reviews_verified_buyers_only'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['reviews_verified_buyers_only'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <!-- =========================================================
             TAB 2: SHIPPING
             ========================================================= -->
        <?php if ($activeTab === 'shipping'): 
            $areaCharges = json_decode($settings['area_delivery_charges'] ?? '{}', true) ?: [];
            $pointCharges = json_decode($settings['point_delivery_charges'] ?? '{}', true) ?: [];
            $defaultBaseFee = floatval($settings['delivery_charge_default'] ?? '30');
        ?>

        <!-- 1. Free Delivery Offers & Threshold Promotion -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-emerald-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold">
                        🎁
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Free Delivery Offers & Thresholds</h3>
                        <p class="text-[11px] text-secondary-400">Configure free shipping qualification rules and cart motivators.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="free_shipping_enabled" value="1" 
                           <?= ($settings['free_shipping_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['free_shipping_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Free Delivery Threshold -->
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Free Delivery Minimum Order (৳)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                            <input type="number" name="delivery_free_threshold" 
                                   value="<?= htmlspecialchars($settings['delivery_free_threshold'] ?? '1100') ?>" 
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold transition-all">
                        </div>
                        <p class="text-[11px] text-secondary-400 mt-1">Orders with subtotal equal or greater than this value qualify for free delivery.</p>
                    </div>

                    <!-- Free Delivery Qualification Rule -->
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Free Delivery Qualification Mode</label>
                        <?php $freeReq = $settings['free_shipping_requirement'] ?? 'min_amount'; ?>
                        <select name="free_shipping_requirement" class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-semibold">
                            <option value="min_amount" <?= $freeReq === 'min_amount' ? 'selected' : '' ?>>Minimum Order Amount Only (Subtotal Threshold)</option>
                            <option value="all_orders" <?= $freeReq === 'all_orders' ? 'selected' : '' ?>>All Orders Free (Sitewide Promotion Event)</option>
                        </select>
                        <p class="text-[11px] text-secondary-400 mt-1">Determines how customers qualify for free shipping.</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-secondary-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Progress Bar Toggle -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-0.5">
                            <label class="font-bold text-xs sm:text-sm text-secondary-800">Show Free Shipping Progress Bar</label>
                            <p class="text-[11px] text-secondary-500">Live progress bar on Cart page (e.g. "Add ৳250 more for FREE Delivery!").</p>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="free_shipping_progress_bar_enabled" value="1" 
                                   <?= ($settings['free_shipping_progress_bar_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                            <span class="toggle-track <?= ($settings['free_shipping_progress_bar_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                                <span class="toggle-thumb"></span>
                            </span>
                        </label>
                    </div>

                    <!-- Exclude Bulky Items -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-0.5">
                            <label class="font-bold text-xs sm:text-sm text-secondary-800">Exclude Heavy / Bulky Items</label>
                            <p class="text-[11px] text-secondary-500">Exempt bulk sacks (e.g. 25kg/50kg rice or oil drums) from 100% free delivery.</p>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="free_shipping_exclude_heavy" value="1" 
                                   <?= ($settings['free_shipping_exclude_heavy'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <span class="toggle-track <?= ($settings['free_shipping_exclude_heavy'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                                <span class="toggle-thumb"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.5. Spend More & Tiered Promotional Offers (Dynamic Milestones) -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-green-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base font-bold shadow-xs">
                        🚀
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Spend More & Unlock Offers (ডায়নামিক অফার ও রিওয়ার্ডস টিয়ার)</h3>
                        <p class="text-[11px] text-secondary-500">Motivate customers in Cart & Checkout to spend more with tiered reward milestones (Free Delivery, Gifts, Discounts).</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="spend_more_offers_enabled" value="1" 
                           <?= ($settings['spend_more_offers_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['spend_more_offers_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 space-y-6">
                <!-- Info Callout Banner -->
                <div class="bg-emerald-50/70 border border-emerald-200/70 rounded-xl p-3.5 flex items-start gap-3">
                    <span class="text-xl shrink-0 mt-0.5">💡</span>
                    <div class="text-xs text-emerald-950 leading-relaxed">
                        <span class="font-bold text-emerald-900">How it works:</span> Customers will see an interactive multi-step progress bar in their <strong>Slide-Over Cart Drawer</strong>, <strong>Cart Page</strong>, and <strong>Checkout</strong>. When their subtotal reaches each milestone, that benefit (e.g. Free Delivery, Free Gift, Discount) is automatically unlocked with celebratory animations!
                    </div>
                </div>

                <!-- Tiers Repeater Table / Cards -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs sm:text-sm font-bold text-secondary-800 uppercase tracking-wider">
                            Promotional Offer Milestones (টিয়ারসমূহ)
                        </label>
                        <button type="button" onclick="addSpendMoreTierRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-colors cursor-pointer shadow-2xs">
                            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                            <span>+ Add Offer Milestone</span>
                        </button>
                    </div>

                    <div id="spend-more-tiers-container" class="space-y-3">
                        <?php 
                        $rawOffersTiers = json_decode($settings['spend_more_offers_tiers'] ?? '[]', true);
                        if (empty($rawOffersTiers) || !is_array($rawOffersTiers)) {
                            $rawOffersTiers = \Models\Setting::getSpendMoreOffersData(0, 'bn')['raw_tiers'] ?? [];
                        }
                        foreach ($rawOffersTiers as $idx => $tier): 
                            $tMin = htmlspecialchars($tier['min_amount'] ?? '500');
                            $tType = htmlspecialchars($tier['type'] ?? 'free_delivery');
                            $tTitle = htmlspecialchars($tier['title'] ?? 'ফ্রি ডেলিভারি');
                            $tTitleEn = htmlspecialchars($tier['title_en'] ?? 'Free Delivery');
                            $tDesc = htmlspecialchars($tier['desc'] ?? 'ডেলিভারি চার্জ একদম ফ্রি');
                            $tVal = htmlspecialchars($tier['reward_value'] ?? $tier['discount_val'] ?? $tier['gift_item_name'] ?? $tier['custom_perk'] ?? '');
                            $tEnabled = !empty($tier['enabled']);
                        ?>
                        <div class="tier-row bg-white hover:bg-secondary-50/50 border border-secondary-200/90 rounded-2xl p-4 transition-all shadow-xs space-y-3.5">
                            <!-- Top Row: 12-Column Grid (Icon, Spend, Type, Value/Gift, Actions) -->
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <!-- 1. Number & Icon (2 cols) -->
                                <div class="sm:col-span-2 min-w-0">
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Icon / Emoji</label>
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 shrink-0 rounded-lg bg-secondary-100 border border-secondary-200 flex items-center justify-center text-xs font-black text-secondary-700 shadow-2xs row-number">
                                            <?= $idx + 1 ?>
                                        </span>
                                        <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][icon]" value="<?= $tIcon ?>" 
                                               title="Icon / Emoji" placeholder="🚚"
                                               class="w-12 text-center py-1.5 text-base font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                    </div>
                                </div>

                                <!-- 2. Spend Amount (2 cols) -->
                                <div class="sm:col-span-2 min-w-0">
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Spend (৳)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                                        <input type="number" name="spend_more_offers_tiers[<?= $idx ?>][min_amount]" value="<?= $tMin ?>" 
                                               class="w-full pl-6 pr-2 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                    </div>
                                </div>

                                <!-- 3. Reward Type (3 cols) -->
                                <div class="sm:col-span-3 min-w-0">
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Reward Type</label>
                                    <select name="spend_more_offers_tiers[<?= $idx ?>][type]" onchange="updateTierRewardTypeUI(this)" 
                                            class="tier-type-select w-full px-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                        <option value="free_delivery" <?= $tType === 'free_delivery' ? 'selected' : '' ?>>🚚 Free Delivery</option>
                                        <option value="free_gift" <?= $tType === 'free_gift' ? 'selected' : '' ?>>🎁 Free Gift</option>
                                        <option value="discount_flat" <?= $tType === 'discount_flat' ? 'selected' : '' ?>>🏷️ Flat Cash Discount</option>
                                        <option value="discount_percent" <?= $tType === 'discount_percent' ? 'selected' : '' ?>>⚡ % Discount</option>
                                        <option value="custom" <?= $tType === 'custom' ? 'selected' : '' ?>>🌟 Special Perk</option>
                                    </select>
                                </div>

                                <!-- 4. Dynamic Value / Gift Name (3 cols) -->
                                <div class="sm:col-span-3 min-w-0 tier-reward-val-wrapper">
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1 tier-val-label truncate">
                                        <?php 
                                            if ($tType === 'discount_flat') echo 'Discount (৳ ছাড়)';
                                            elseif ($tType === 'discount_percent') echo 'Discount (% ছাড়)';
                                            elseif ($tType === 'free_gift') echo 'Gift Item / উপহার পণ্য';
                                            elseif ($tType === 'custom') echo 'Special Perk / সুবিধা';
                                            else echo 'Reward Value / সুবিধা';
                                        ?>
                                    </label>
                                    <div class="tier-val-input-container">
                                        <?php if ($tType === 'discount_flat'): ?>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                                                <input type="number" step="any" min="0" name="spend_more_offers_tiers[<?= $idx ?>][reward_value]" value="<?= $tVal !== '' ? $tVal : '100' ?>" placeholder="100" 
                                                       class="w-full pl-6 pr-2 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                            </div>
                                        <?php elseif ($tType === 'discount_percent'): ?>
                                            <div class="relative">
                                                <input type="number" step="any" min="0" max="100" name="spend_more_offers_tiers[<?= $idx ?>][reward_value]" value="<?= $tVal !== '' ? $tVal : '10' ?>" placeholder="10" 
                                                       class="w-full pl-2.5 pr-6 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-secondary-400 font-bold text-xs">%</span>
                                            </div>
                                        <?php elseif ($tType === 'free_gift'): ?>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-sm">🎁</span>
                                                <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][reward_value]" value="<?= $tVal ?>" placeholder="যেমন: ১ কেজি চিনি বা মসলা প্যাক" 
                                                       class="w-full pl-7 pr-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                            </div>
                                        <?php elseif ($tType === 'custom'): ?>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-sm">🌟</span>
                                                <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][reward_value]" value="<?= $tVal ?>" placeholder="যেমন: ডাবল রিওয়ার্ড পয়েন্ট" 
                                                       class="w-full pl-7 pr-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold truncate shadow-2xs">
                                                <span>✓</span> <span>100% Free Shipping</span>
                                                <input type="hidden" name="spend_more_offers_tiers[<?= $idx ?>][reward_value]" value="0">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- 5. Active & Delete (2 cols) -->
                                <div class="sm:col-span-2 min-w-0 flex items-center justify-end gap-3 pb-1">
                                    <label class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary-700 cursor-pointer select-none">
                                        <input type="checkbox" name="spend_more_offers_tiers[<?= $idx ?>][enabled]" value="1" <?= $tEnabled ? 'checked' : '' ?>
                                               class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span>Active</span>
                                    </label>
                                    <button type="button" onclick="removeSpendMoreTierRow(this)" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors cursor-pointer shrink-0 shadow-2xs" title="Delete Tier">
                                        <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                    </button>
                                </div>
                            </div>

                            <!-- Bottom Row: Titles & Description (3 columns) -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-secondary-100">
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Title (বাংলা)</label>
                                    <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][title]" value="<?= $tTitle ?>" placeholder="যেমন: ফ্রি গিফট" 
                                           class="w-full px-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Title (English)</label>
                                    <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][title_en]" value="<?= $tTitleEn ?>" placeholder="e.g. Free Gift" 
                                           class="w-full px-2.5 py-1.5 text-xs font-medium border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Description / Benefit</label>
                                    <input type="text" name="spend_more_offers_tiers[<?= $idx ?>][desc]" value="<?= $tDesc ?>" placeholder="উপহার বা ছাড়ের বিবরণ" 
                                           class="w-full px-2.5 py-1.5 text-xs font-medium border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Standard Base Delivery Rates & Order Restrictions -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                    <ion-icon name="bicycle-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Standard Base Rates & Order Limitations</h3>
                    <p class="text-[11px] text-secondary-400">Configure global fallback delivery charges and order value thresholds.</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Default Delivery Charge -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Default Base Delivery Fee (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_charge_default" 
                               value="<?= htmlspecialchars($settings['delivery_charge_default'] ?? '30') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold transition-all">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Standard rate applied if no Union or Point override is defined.</p>
                </div>

                <!-- Minimum Order Value -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Minimum Order Amount for Delivery (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_min_order_amount" 
                               value="<?= htmlspecialchars($settings['delivery_min_order_amount'] ?? '100') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold transition-all">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Customers cannot checkout if order subtotal is below this amount.</p>
                </div>

                <!-- Max COD Limit -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Maximum Cash on Delivery Limit (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_max_cod_amount" 
                               value="<?= htmlspecialchars($settings['delivery_max_cod_amount'] ?? '10000') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold transition-all">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Orders exceeding this total must be prepaid via bKash/Nagad.</p>
                </div>

                <!-- Estimated Delivery Time -->
                <div class="md:col-span-3 pt-4 border-t border-secondary-100">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Standard Delivery Time Commitment</label>
                    <input type="text" name="estimated_delivery_time" 
                           value="<?= htmlspecialchars($settings['estimated_delivery_time'] ?? 'Delivery within 30 minutes') ?>" 
                           placeholder="e.g. Delivery within 30 minutes / Same Day Delivery"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold transition-all">
                    <p class="text-[11px] text-secondary-400 mt-1">Displayed on storefront headers, promotional badges, and checkout screens.</p>
                </div>
            </div>
        </div>

        <!-- 3. Union / Area Specific Delivery Rates -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                        <ion-icon name="map-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Union / Area Specific Delivery Charges</h3>
                        <p class="text-[11px] text-secondary-400">Set custom delivery fees per Union. Orders from customers in that Union will use this rate.</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-200">
                    <?= count($areas ?? []) ?> Unions Configured
                </span>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-secondary-200 text-[11px] font-extrabold text-secondary-500 uppercase tracking-wider bg-secondary-50/50">
                                <th class="py-3 px-4">Union / Area Name</th>
                                <th class="py-3 px-4">Assigned Warehouse</th>
                                <th class="py-3 px-4">Custom Delivery Fee (৳)</th>
                                <th class="py-3 px-4 text-center">Applied Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100 text-xs sm:text-sm">
                            <?php if (!empty($areas)): ?>
                                <?php foreach ($areas as $area): 
                                    $hasCustomArea = isset($areaCharges[$area['id']]) && $areaCharges[$area['id']] !== '';
                                    $areaRate = $hasCustomArea ? $areaCharges[$area['id']] : '';
                                ?>
                                <tr class="hover:bg-secondary-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-secondary-900 flex items-center gap-2">
                                        <ion-icon name="location-outline" class="text-emerald-600 text-base"></ion-icon>
                                        <span><?= htmlspecialchars($area['name']) ?></span>
                                    </td>
                                    <td class="py-3.5 px-4 text-secondary-600 text-xs">
                                        <?= htmlspecialchars($area['warehouse_name'] ?? 'Main Warehouse') ?>
                                    </td>
                                    <td class="py-3.5 px-4 max-w-[200px]">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                                            <input type="number" 
                                                   name="area_delivery_charges[<?= $area['id'] ?>]" 
                                                   value="<?= htmlspecialchars($areaRate) ?>" 
                                                   placeholder="Default (৳<?= $defaultBaseFee ?>)"
                                                   class="w-full pl-7 pr-3 py-1.5 text-xs sm:text-sm border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold bg-white">
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <?php if ($hasCustomArea): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Custom ৳<?= htmlspecialchars($areaRate) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-secondary-100 text-secondary-600">
                                                Default (৳<?= $defaultBaseFee ?>)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-secondary-400 text-xs">
                                        No unions/areas configured in Master Data yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-[11px] text-secondary-400 mt-3">
                    💡 Tip: Leave custom fee empty to automatically apply the standard base charge (৳<?= $defaultBaseFee ?>).
                </p>
            </div>
        </div>

        <!-- 4. Point-Wise Delivery Charges & Hub Pickup -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                        <ion-icon name="pin-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Delivery Point Specific Charges & Subsidies</h3>
                        <p class="text-[11px] text-secondary-400">Override fees for specific pickup spots, central hubs (৳0), or remote drop points.</p>
                    </div>
                </div>
                <!-- Point Search Filter -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-secondary-400">
                        <ion-icon name="search-outline"></ion-icon>
                    </span>
                    <input type="text" id="pointTableSearch" placeholder="Filter points..." 
                           class="w-full pl-8 pr-3 py-1.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto max-h-96 overflow-y-auto custom-scrollbar border border-secondary-100 rounded-xl">
                    <table class="w-full text-left border-collapse" id="pointsTable">
                        <thead class="sticky top-0 bg-secondary-50 z-10">
                            <tr class="border-b border-secondary-200 text-[11px] font-extrabold text-secondary-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Point Name</th>
                                <th class="py-3 px-4">Ward / Zone</th>
                                <th class="py-3 px-4">Union / Area</th>
                                <th class="py-3 px-4">Point Delivery Fee (৳)</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100 text-xs sm:text-sm">
                            <?php if (!empty($points)): ?>
                                <?php foreach ($points as $point): 
                                    $hasCustomPoint = isset($pointCharges[$point['id']]) && $pointCharges[$point['id']] !== '';
                                    $ptRate = $hasCustomPoint ? $pointCharges[$point['id']] : '';
                                ?>
                                <tr class="hover:bg-secondary-50/60 transition-colors point-row" data-point-search="<?= strtolower(htmlspecialchars($point['name'] . ' ' . ($point['zone_name'] ?? '') . ' ' . ($point['area_name'] ?? ''))) ?>">
                                    <td class="py-3 px-4 font-bold text-secondary-900 flex items-center gap-1.5">
                                        <ion-icon name="ellipse" class="text-[8px] text-purple-600"></ion-icon>
                                        <span><?= htmlspecialchars($point['name']) ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-secondary-600 text-xs">
                                        <?= htmlspecialchars($point['zone_name'] ?? '-') ?>
                                    </td>
                                    <td class="py-3 px-4 text-secondary-600 text-xs">
                                        <?= htmlspecialchars($point['area_name'] ?? '-') ?>
                                    </td>
                                    <td class="py-3 px-4 max-w-[180px]">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                                            <input type="number" 
                                                   name="point_delivery_charges[<?= $point['id'] ?>]" 
                                                   value="<?= htmlspecialchars($ptRate) ?>" 
                                                   placeholder="Inherit Union"
                                                   class="w-full pl-7 pr-3 py-1.5 text-xs sm:text-sm border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold bg-white">
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <?php if ($hasCustomPoint && floatval($ptRate) == 0): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                                Free Hub (৳0)
                                            </span>
                                        <?php elseif ($hasCustomPoint): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800">
                                                Custom ৳<?= htmlspecialchars($ptRate) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-secondary-100 text-secondary-500">
                                                Inherited
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-secondary-400 text-xs">
                                        No delivery points found in Master Data.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Weight-Based Cargo & Bulk Orders Surcharge -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                        <ion-icon name="scale-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Weight-Based Tiered Shipping Surcharge</h3>
                        <p class="text-[11px] text-secondary-400">Charge incremental delivery fees for heavy orders exceeding the base weight allowance.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="delivery_weight_surcharge_enabled" value="1" 
                           <?= ($settings['delivery_weight_surcharge_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['delivery_weight_surcharge_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Base Weight Included in Delivery Fee (kg)</label>
                    <div class="relative">
                        <input type="number" name="delivery_base_weight_limit" 
                               value="<?= htmlspecialchars($settings['delivery_base_weight_limit'] ?? '5') ?>" 
                               min="0" step="0.5"
                               class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-secondary-400 font-bold">kg</span>
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Orders up to this weight ship without any weight-based surcharge.</p>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Extra Charge per Additional kg (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_extra_per_kg_fee" 
                               value="<?= htmlspecialchars($settings['delivery_extra_per_kg_fee'] ?? '5') ?>" 
                               min="0" step="1"
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Added incrementally for each kilogram exceeding the base weight limit.</p>
                </div>
            </div>
        </div>

        <!-- 6. Express & Priority 30-Minute Delivery -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-lg">
                        <ion-icon name="flash-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Express 30-Minute Priority Delivery</h3>
                        <p class="text-[11px] text-secondary-400">Offer an optional fast-track service for customers needing immediate delivery.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="express_delivery_enabled" value="1" 
                           <?= ($settings['express_delivery_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['express_delivery_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Express Delivery Surcharge (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="express_delivery_charge" 
                               value="<?= htmlspecialchars($settings['express_delivery_charge'] ?? '60') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Extra fee charged when customer selects priority 30-minute delivery.</p>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Express Service Daily Cutoff Time</label>
                    <input type="text" name="express_cutoff_time" 
                           value="<?= htmlspecialchars($settings['express_cutoff_time'] ?? '08:30 PM') ?>" 
                           placeholder="e.g. 08:30 PM"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                    <p class="text-[11px] text-secondary-400 mt-1">Express fast-track option will not be offered after this hour.</p>
                </div>
            </div>
        </div>

        <!-- 7. Delivery Time Slots & Scheduling -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-lg">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Scheduled Delivery Windows & Slots</h3>
                        <p class="text-[11px] text-secondary-400">Allow customers to choose specific delivery time slots during checkout.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="delivery_slots_enabled" value="1" 
                           <?= ($settings['delivery_slots_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['delivery_slots_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Configured Delivery Slots (One per line)</label>
                    <textarea name="delivery_available_slots" rows="4" 
                              class="w-full px-3.5 py-2 text-xs sm:text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['delivery_available_slots'] ?? "Morning (08:00 AM - 11:00 AM)\nNoon (12:00 PM - 03:00 PM)\nEvening (04:00 PM - 07:00 PM)\nNight (07:30 PM - 10:00 PM)") ?></textarea>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Peak Hour / Evening Slot Surcharge (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_slot_peak_surcharge" 
                               value="<?= htmlspecialchars($settings['delivery_slot_peak_surcharge'] ?? '0') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1">Optional surcharge for rush evening or late-night delivery slots.</p>
                </div>
            </div>
        </div>

        <!-- 8. Bad Weather Emergency Surcharge & COD Handling Fee -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg">
                    <ion-icon name="shield-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Inclement Weather & Payment Handling Fees</h3>
                    <p class="text-[11px] text-secondary-400">Emergency logistics protections and cash handling charges.</p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Bad Weather Surcharge -->
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800">Bad Weather / Heavy Rain Surcharge</label>
                        <p class="text-[11px] text-secondary-500">Temporarily adds an emergency hazard fee during typhoons or waterlogging.</p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" name="delivery_bad_weather_surcharge_enabled" value="1" 
                               <?= ($settings['delivery_bad_weather_surcharge_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['delivery_bad_weather_surcharge_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Weather Surcharge Fee (৳)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                            <input type="number" name="delivery_bad_weather_fee" 
                                   value="<?= htmlspecialchars($settings['delivery_bad_weather_fee'] ?? '20') ?>" 
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Customer Alert Notice Text</label>
                        <input type="text" name="delivery_bad_weather_notice" 
                               value="<?= htmlspecialchars($settings['delivery_bad_weather_notice'] ?? 'Due to heavy monsoon rainfall, a small emergency rider hazard surcharge is temporarily applied.') ?>" 
                               class="w-full px-3.5 py-2 text-xs sm:text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
                    </div>
                </div>

                <!-- COD Processing Fee -->
                <div class="pt-5 border-t border-secondary-100 flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800">Cash on Delivery (COD) Processing Fee</label>
                        <p class="text-[11px] text-secondary-500">Encourage digital payments (bKash/Nagad) by charging a nominal fee for cash handling.</p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" name="delivery_cod_fee_enabled" value="1" 
                               <?= ($settings['delivery_cod_fee_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['delivery_cod_fee_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <div class="max-w-xs pt-1">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">COD Processing Charge (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-secondary-500 font-bold">৳</span>
                        <input type="number" name="delivery_cod_fee" 
                               value="<?= htmlspecialchars($settings['delivery_cod_fee'] ?? '10') ?>" 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. Rider Allocation & Distribution Rules -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                    <ion-icon name="navigate-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Delivery Personnel & Rider Management</h3>
                    <p class="text-[11px] text-secondary-400">Configure order allocation methods and rider package handover permissions.</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Allocation Mode -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-2">Order Assignment Mode</label>
                    <?php $allocMode = $settings['delivery_allocation_mode'] ?? 'auto'; ?>
                    <div class="grid grid-cols-2 gap-3" data-radio-group="delivery_allocation_mode">
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_allocation_mode" value="auto" <?= ($allocMode === 'auto') ? 'checked' : '' ?> class="sr-only" data-theme="emerald">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?= ($allocMode === 'auto') ? 'is-selected-emerald' : '' ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="flash" class="text-xl <?= ($allocMode === 'auto') ? 'text-emerald-600' : 'text-secondary-400' ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Auto Assign</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5">Automated dispatch by delivery point</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_allocation_mode" value="manual" <?= ($allocMode === 'manual') ? 'checked' : '' ?> class="sr-only" data-theme="blue">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?= ($allocMode === 'manual') ? 'is-selected-blue' : '' ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="hand-right-outline" class="text-xl <?= ($allocMode === 'manual') ? 'text-blue-600' : 'text-secondary-400' ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Manual Assign</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5">Admin manually assigns each order</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Allow Rider Transfer -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-2">Rider-to-Rider Package Handover</label>
                    <?php $allowTransfer = $settings['allow_rider_transfer'] ?? '1'; ?>
                    <div class="grid grid-cols-2 gap-3" data-radio-group="allow_rider_transfer">
                        <label class="cursor-pointer">
                            <input type="radio" name="allow_rider_transfer" value="1" <?= ($allowTransfer === '1') ? 'checked' : '' ?> class="sr-only" data-theme="emerald">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?= ($allowTransfer === '1') ? 'is-selected-emerald' : '' ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="swap-horizontal" class="text-xl <?= ($allowTransfer === '1') ? 'text-emerald-600' : 'text-secondary-400' ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Allowed</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5">Riders can handover parcels to peers</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="allow_rider_transfer" value="0" <?= ($allowTransfer === '0') ? 'checked' : '' ?> class="sr-only" data-theme="rose">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?= ($allowTransfer === '0') ? 'is-selected-rose' : '' ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="ban-outline" class="text-xl <?= ($allowTransfer === '0') ? 'text-rose-600' : 'text-secondary-400' ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Disabled</div>
                                <div class="text-[10px] text-secondary-400 mt-0.5">Only admins can transfer assignments</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Multi-Rider Strategy -->
                <div class="md:col-span-2 pt-2 border-t border-secondary-100">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Multi-Rider Distribution Strategy</label>
                    <?php $strategy = $settings['delivery_auto_assign_strategy'] ?? 'least_busy'; ?>
                    <div class="max-w-md">
                        <select name="delivery_auto_assign_strategy" class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="least_busy" <?= ($strategy === 'least_busy') ? 'selected' : '' ?>>
                                Least Busy First (Assign to rider with lowest pending orders)
                            </option>
                            <option value="round_robin" <?= ($strategy === 'round_robin') ? 'selected' : '' ?>>
                                Round Robin (Distribute incoming orders equally among riders)
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter JS for Points Table -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pointSearch = document.getElementById('pointTableSearch');
            if (pointSearch) {
                pointSearch.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('.point-row');
                    rows.forEach(row => {
                        const text = row.getAttribute('data-point-search') || '';
                        if (!query || text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
        </script>

        <?php endif; ?>

        <!-- =========================================================
             TAB 3: PAYMENTS
             ========================================================= -->
        <?php if ($activeTab === 'payments'): ?>

        <!-- Cash on Delivery (COD) -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-black">
                        💵
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Cash on Delivery (COD)</h3>
                        <p class="text-[11px] text-secondary-400">Allow customers to pay in cash upon physical receipt of delivery.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="payment_cod_enabled" value="1" 
                           <?= ($settings['payment_cod_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['payment_cod_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">COD Customer Instructions</label>
                    <textarea name="payment_cod_instructions" rows="2" 
                              class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['payment_cod_instructions'] ?? 'Pay in cash directly to our delivery representative upon receiving your items.') ?></textarea>
                </div>
            </div>
        </div>

        <!-- bKash Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-pink-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center font-black text-xs">
                        bK
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">bKash Mobile Payment</h3>
                        <p class="text-[11px] text-secondary-400">Manual bKash payment account and Transaction ID (TrxID) verification.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="payment_bkash_enabled" value="1" 
                           <?= ($settings['payment_bkash_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['payment_bkash_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">bKash Account Type</label>
                    <?php $bType = $settings['payment_bkash_type'] ?? 'personal'; ?>
                    <select name="payment_bkash_type" class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-semibold">
                        <option value="personal" <?= $bType === 'personal' ? 'selected' : '' ?>>Personal (Send Money)</option>
                        <option value="merchant" <?= $bType === 'merchant' ? 'selected' : '' ?>>Merchant (Make Payment)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">bKash Mobile Number</label>
                    <input type="text" name="payment_bkash_number" 
                           value="<?= htmlspecialchars($settings['payment_bkash_number'] ?? '01609448066') ?>" 
                           placeholder="017xxxxxxxx"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">bKash Payment Instructions</label>
                    <textarea name="payment_bkash_instructions" rows="2" 
                              class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['payment_bkash_instructions'] ?? 'Send money to the designated bKash number and provide your Transaction ID (TrxID) in the checkout box.') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Nagad & Rocket Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-orange-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-black text-xs">
                        N
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Nagad & Rocket Payment</h3>
                        <p class="text-[11px] text-secondary-400">Collect payments via Nagad or Rocket mobile wallets.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="payment_nagad_enabled" value="1" 
                           <?= ($settings['payment_nagad_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['payment_nagad_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Nagad Mobile Number</label>
                    <input type="text" name="payment_nagad_number" 
                           value="<?= htmlspecialchars($settings['payment_nagad_number'] ?? '01609448066') ?>" 
                           placeholder="018xxxxxxxx"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Rocket Mobile Number (Optional)</label>
                    <input type="text" name="payment_rocket_number" 
                           value="<?= htmlspecialchars($settings['payment_rocket_number'] ?? '') ?>" 
                           placeholder="019xxxxxxxx"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                </div>
            </div>
        </div>

        <!-- Currency & Formatting -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center font-bold">
                    ৳
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Currency & Formatting</h3>
                    <p class="text-[11px] text-secondary-400">Configure the currency symbol and its display position across the site.</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Currency Symbol</label>
                    <input type="text" name="currency_symbol" 
                           value="<?= htmlspecialchars($settings['currency_symbol'] ?? '৳') ?>" 
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Symbol Position</label>
                    <?php $cPos = $settings['currency_position'] ?? 'left'; ?>
                    <select name="currency_position" class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-semibold">
                        <option value="left" <?= $cPos === 'left' ? 'selected' : '' ?>>Left of amount (e.g. ৳100)</option>
                        <option value="right" <?= $cPos === 'right' ? 'selected' : '' ?>>Right of amount (e.g. 100 ৳)</option>
                    </select>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <!-- =========================================================
             TAB 4: ACCOUNTS & PRIVACY
             ========================================================= -->
        <?php if ($activeTab === 'privacy'): ?>

        <!-- Guest Checkout & Signup Policies -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Checkout & Customer Accounts Policy</h3>
                    <p class="text-[11px] text-secondary-400">Control guest ordering privileges and user account creation rules.</p>
                </div>
            </div>

            <div class="p-6 space-y-5 divide-y divide-secondary-100">
                <!-- Allow Guest Checkout -->
                <div class="flex items-start justify-between gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="allow_guest_checkout">
                            Allow Guest Checkout
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Customers can complete orders quickly by entering their name, phone, and delivery address without creating a password.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="allow_guest_checkout" name="allow_guest_checkout" value="1" 
                               <?= ($settings['allow_guest_checkout'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['allow_guest_checkout'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Enable Checkout Signup -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="enable_checkout_signup">
                            Account Creation on Checkout
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            Allow customers to set a password during checkout to automatically create an account for tracking future orders.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="enable_checkout_signup" name="enable_checkout_signup" value="1" 
                               <?= ($settings['enable_checkout_signup'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['enable_checkout_signup'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Require Login to Checkout -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="require_login_to_checkout">
                            Require Login to Checkout
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            When enabled, customers must log in to an existing account before proceeding to the checkout page.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="require_login_to_checkout" name="require_login_to_checkout" value="1" 
                               <?= ($settings['require_login_to_checkout'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['require_login_to_checkout'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- OTP Required for Signup -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="otp_required_signup">
                            🔐 OTP Verification for Account Creation
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            When enabled, customers must verify their phone number with a one-time code (OTP) before their account is created. Prevents fake registrations.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="otp_required_signup" name="otp_required_signup" value="1" 
                               <?= ($settings['otp_required_signup'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['otp_required_signup'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- OTP Required for Password Change -->
                <div class="flex items-start justify-between gap-4 pt-5">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="otp_required_password_change">
                            🔐 OTP Verification Before Password Change
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            When enabled, customers must verify their identity with an OTP before they can change their account password. Adds an extra layer of account security.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="otp_required_password_change" name="otp_required_password_change" value="1" 
                               <?= ($settings['otp_required_password_change'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['otp_required_password_change'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- SMS Gateway Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-lg">
                    <ion-icon name="phone-portrait-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">📱 SMS Gateway (OTP Delivery)</h3>
                    <p class="text-[11px] text-secondary-400">Configure a real SMS provider to send OTP verification codes to customers.</p>
                </div>
            </div>

            <div class="p-6 space-y-5">

                <!-- Enable SMS -->
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <label class="font-bold text-xs sm:text-sm text-secondary-800 cursor-pointer" for="sms_enabled">
                            Enable Real SMS Delivery
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed">
                            When ON, OTPs are sent via SMS. When OFF, OTPs are only written to <code class="bg-gray-100 px-1 rounded text-xs">otp_log.txt</code> on the server.
                        </p>
                    </div>
                    <label class="custom-toggle">
                        <input type="checkbox" id="sms_enabled" name="sms_enabled" value="1"
                               <?= ($settings['sms_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-track <?= ($settings['sms_enabled'] ?? '0') == '1' ? 'is-checked' : '' ?>">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- Provider Select -->
                <div>
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wide">SMS Provider</label>
                    <select name="sms_provider" id="sms_provider"
                            class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            onchange="toggleSmsFields()">
                        <option value=""           <?= ($settings['sms_provider'] ?? '') === ''             ? 'selected' : '' ?>>— Select Provider —</option>
                        <option value="greenweb"   <?= ($settings['sms_provider'] ?? '') === 'greenweb'     ? 'selected' : '' ?>>GreenWeb SMS (Bangladesh)</option>
                        <option value="ssl_wireless" <?= ($settings['sms_provider'] ?? '') === 'ssl_wireless' ? 'selected' : '' ?>>SSL Wireless (Bangladesh)</option>
                        <option value="bulksmsbd"  <?= ($settings['sms_provider'] ?? '') === 'bulksmsbd'   ? 'selected' : '' ?>>BulkSMSBD (Bangladesh)</option>
                        <option value="twilio"     <?= ($settings['sms_provider'] ?? '') === 'twilio'      ? 'selected' : '' ?>>Twilio (International)</option>
                    </select>
                </div>

                <!-- API Token (GreenWeb / SSL Wireless / BulkSMSBD) -->
                <div id="field_api_token" class="sms-field">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wide">API Token / API Key</label>
                    <input type="text" name="sms_api_token" value="<?= htmlspecialchars($settings['sms_api_token'] ?? '') ?>"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Your API token from the provider dashboard">
                </div>

                <!-- Sender ID -->
                <div id="field_sender_id" class="sms-field">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wide">Sender ID / SID</label>
                    <input type="text" name="sms_sender_id" value="<?= htmlspecialchars($settings['sms_sender_id'] ?? '') ?>"
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="e.g. FreshMart or your approved Sender ID">
                </div>

                <!-- Twilio Account SID + Auth Token -->
                <div id="field_twilio" class="sms-field hidden">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wide">Twilio Account SID</label>
                            <input type="text" name="sms_username" value="<?= htmlspecialchars($settings['sms_username'] ?? '') ?>"
                                   class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="ACxxxxxxxxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wide">Twilio Auth Token</label>
                            <input type="password" name="sms_password" value="<?= htmlspecialchars($settings['sms_password'] ?? '') ?>"
                                   class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="Auth token">
                        </div>
                    </div>
                </div>

                <!-- Provider-specific help links -->
                <div id="sms_help_links" class="text-xs text-secondary-500 space-y-1">
                    <p id="help_greenweb"   class="sms-help hidden">🔗 Get API token: <a href="https://greenweb.com.bd" target="_blank" class="text-violet-600 hover:underline">greenweb.com.bd</a></p>
                    <p id="help_ssl"        class="sms-help hidden">🔗 Get API token: <a href="https://www.sslwireless.com" target="_blank" class="text-violet-600 hover:underline">sslwireless.com</a></p>
                    <p id="help_bulksmsbd"  class="sms-help hidden">🔗 Get API key: <a href="https://bulksmsbd.net" target="_blank" class="text-violet-600 hover:underline">bulksmsbd.net</a></p>
                    <p id="help_twilio"     class="sms-help hidden">🔗 Dashboard: <a href="https://console.twilio.com" target="_blank" class="text-violet-600 hover:underline">console.twilio.com</a></p>
                </div>

            </div>
        </div>

        <!-- Privacy Policy & Consent Notices -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Privacy Policy & Terms Consent</h3>
                    <p class="text-[11px] text-secondary-400">Define checkout terms agreement notices and user data security statements.</p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Checkout Terms Consent Notice</label>
                    <textarea name="terms_notice" rows="2" 
                              class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['terms_notice'] ?? 'By confirming this order, you agree to our Terms of Service and Privacy Policy.') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Privacy Policy Statement</label>
                    <textarea name="privacy_policy_content" rows="3" 
                              class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['privacy_policy_content'] ?? 'Your personal information including phone number and delivery address is used strictly for fulfilling your order and is never sold or transferred to any third parties.') ?></textarea>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <!-- =========================================================
             TAB 5: SITE VISIBILITY
             ========================================================= -->
        <?php if ($activeTab === 'visibility'): ?>

        <!-- Store Mode (Live vs Maintenance vs Catalog) -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                    <ion-icon name="storefront-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Store Visibility Mode</h3>
                    <p class="text-[11px] text-secondary-400">Manage online store operating mode and order availability.</p>
                </div>
            </div>

            <div class="p-6">
                <?php $siteMode = $settings['site_visibility_status'] ?? 'live'; ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" data-radio-group="site_visibility_status">
                    
                    <!-- Mode 1: Live -->
                    <label class="cursor-pointer">
                        <input type="radio" name="site_visibility_status" value="live" <?= $siteMode === 'live' ? 'checked' : '' ?> class="sr-only" data-theme="emerald">
                        <div class="radio-select-card p-5 h-full flex flex-col justify-between <?= $siteMode === 'live' ? 'is-selected-emerald' : '' ?>">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full <?= $siteMode === 'live' ? 'bg-emerald-500 animate-pulse' : 'bg-secondary-300' ?>" data-status-dot="emerald"></span>
                                        <span class="text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider <?= $siteMode === 'live' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-secondary-100 text-secondary-500' ?>" data-status-badge="emerald">Normal</span>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <h4 class="font-black text-sm text-secondary-900 mb-1.5 flex items-center gap-1.5">
                                    <ion-icon name="checkmark-circle" class="text-base <?= $siteMode === 'live' ? 'text-emerald-600' : 'text-secondary-400' ?>" data-icon="emerald"></ion-icon>
                                    <span>Live Store</span>
                                </h4>
                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    Store is fully operational. Customers can browse products, add items to cart, and checkout freely.
                                </p>
                            </div>
                        </div>
                    </label>

                    <!-- Mode 2: Maintenance Mode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="site_visibility_status" value="maintenance" <?= $siteMode === 'maintenance' ? 'checked' : '' ?> class="sr-only" data-theme="amber">
                        <div class="radio-select-card p-5 h-full flex flex-col justify-between <?= $siteMode === 'maintenance' ? 'is-selected-amber' : '' ?>">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full <?= $siteMode === 'maintenance' ? 'bg-amber-500 animate-pulse' : 'bg-secondary-300' ?>" data-status-dot="amber"></span>
                                        <span class="text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider <?= $siteMode === 'maintenance' ? 'bg-amber-600 text-white shadow-xs' : 'bg-secondary-100 text-secondary-500' ?>" data-status-badge="amber">Paused</span>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <h4 class="font-black text-sm text-secondary-900 mb-1.5 flex items-center gap-1.5">
                                    <ion-icon name="construct-outline" class="text-base <?= $siteMode === 'maintenance' ? 'text-amber-600' : 'text-secondary-400' ?>" data-icon="amber"></ion-icon>
                                    <span>Maintenance Mode</span>
                                </h4>
                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    Displays a prominent maintenance banner to visitors and temporarily pauses ordering.
                                </p>
                            </div>
                        </div>
                    </label>

                    <!-- Mode 3: Catalog Mode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="site_visibility_status" value="catalog" <?= $siteMode === 'catalog' ? 'checked' : '' ?> class="sr-only" data-theme="blue">
                        <div class="radio-select-card p-5 h-full flex flex-col justify-between <?= $siteMode === 'catalog' ? 'is-selected-blue' : '' ?>">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full <?= $siteMode === 'catalog' ? 'bg-blue-500 animate-pulse' : 'bg-secondary-300' ?>" data-status-dot="blue"></span>
                                        <span class="text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider <?= $siteMode === 'catalog' ? 'bg-blue-600 text-white shadow-xs' : 'bg-secondary-100 text-secondary-500' ?>" data-status-badge="blue">Browse Only</span>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <h4 class="font-black text-sm text-secondary-900 mb-1.5 flex items-center gap-1.5">
                                    <ion-icon name="book-outline" class="text-base <?= $siteMode === 'catalog' ? 'text-blue-600' : 'text-secondary-400' ?>" data-icon="blue"></ion-icon>
                                    <span>Catalog Mode</span>
                                </h4>
                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    Products and prices remain visible for browsing, but add-to-cart and checkout buttons are hidden.
                                </p>
                            </div>
                        </div>
                    </label>

                </div>

                <!-- Maintenance Notice Message Box -->
                <div class="mt-5 pt-4 border-t border-secondary-100">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">
                        Maintenance Banner Message
                    </label>
                    <textarea name="maintenance_notice_message" rows="2" 
                              class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($settings['maintenance_notice_message'] ?? 'Our online store is currently undergoing scheduled maintenance. We will be back shortly!') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Top Announcement Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                        <ion-icon name="megaphone-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Top Announcement Bar</h3>
                        <p class="text-[11px] text-secondary-400">Display a global announcement or promotional banner at the very top of the website.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="top_announcement_bar_enabled" value="1" 
                           <?= ($settings['top_announcement_bar_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['top_announcement_bar_enabled'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Announcement Text</label>
                    <input type="text" name="top_announcement_text" 
                           value="<?= htmlspecialchars($settings['top_announcement_text'] ?? '📢 Enjoy super-fast 30-minute delivery on fresh groceries and everyday essentials!') ?>" 
                           placeholder="Enter special offers or announcements..."
                           class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-secondary-800 mb-1.5">Banner Color Theme</label>
                    <?php $annColor = $settings['top_announcement_bg'] ?? 'emerald'; ?>
                    <select name="top_announcement_bg" class="w-full px-3.5 py-2 text-sm border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                        <option value="emerald" <?= $annColor === 'emerald' ? 'selected' : '' ?>>Emerald Green</option>
                        <option value="amber" <?= $annColor === 'amber' ? 'selected' : '' ?>>Amber / Gold</option>
                        <option value="rose" <?= $annColor === 'rose' ? 'selected' : '' ?>>Rose Red</option>
                        <option value="indigo" <?= $annColor === 'indigo' ? 'selected' : '' ?>>Indigo Blue</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SEO & Search Engine Indexing -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/90 overflow-hidden">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                        <ion-icon name="globe-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800 text-sm sm:text-base">Search Engine Visibility (SEO)</h3>
                        <p class="text-[11px] text-secondary-400">Configure indexing rules for Google, Bing, and web search crawlers.</p>
                    </div>
                </div>
                <label class="custom-toggle">
                    <input type="checkbox" name="seo_index_allow" value="1" 
                           <?= ($settings['seo_index_allow'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="toggle-track <?= ($settings['seo_index_allow'] ?? '1') == '1' ? 'is-checked' : '' ?>">
                        <span class="toggle-thumb"></span>
                    </span>
                </label>
            </div>
            <div class="p-6">
                <p class="text-xs text-secondary-500 leading-relaxed">
                    When enabled, search engines (Google, Bing) are permitted to index products and pages. When disabled, a <code class="bg-secondary-100 px-1.5 py-0.5 rounded text-[11px] font-mono text-secondary-700">noindex, nofollow</code> meta tag is automatically added to all storefront pages.
                </p>
            </div>
        </div>

        <?php endif; ?>

        <!-- Save Button Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transform active:scale-95 transition-all flex items-center gap-2 text-sm cursor-pointer">
                <ion-icon name="save-outline" class="text-xl"></ion-icon>
                <span>Save Settings</span>
            </button>
        </div>

    </form>

    <!-- Interactive Radio Handler Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const radioGroups = document.querySelectorAll('[data-radio-group]');
        
        radioGroups.forEach(group => {
            const radios = group.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    radios.forEach(r => {
                        const label = r.closest('label');
                        if (!label) return;
                        const card = label.querySelector('.radio-select-card');
                        if (!card) return;

                        const theme = r.getAttribute('data-theme') || 'emerald';
                        const icon = card.querySelector('[data-icon]');
                        const dot = card.querySelector('[data-status-dot]');
                        const badge = card.querySelector('[data-status-badge]');
                        const simpleIcon = card.querySelector('ion-icon:not([data-icon])');

                        // Clear all theme selected classes
                        card.classList.remove('is-selected-emerald', 'is-selected-blue', 'is-selected-amber', 'is-selected-rose');

                        if (r.checked) {
                            card.classList.add('is-selected-' + theme);

                            if (icon) {
                                icon.className = 'text-base text-' + (theme === 'rose' ? 'rose' : (theme === 'amber' ? 'amber' : (theme === 'blue' ? 'blue' : 'emerald'))) + '-600';
                            }
                            if (simpleIcon) {
                                simpleIcon.className = 'text-xl text-' + (theme === 'rose' ? 'rose' : (theme === 'amber' ? 'amber' : (theme === 'blue' ? 'blue' : 'emerald'))) + '-600';
                            }
                            if (dot) {
                                dot.className = 'w-2.5 h-2.5 rounded-full animate-pulse bg-' + (theme === 'amber' ? 'amber' : (theme === 'blue' ? 'blue' : 'emerald')) + '-500';
                            }
                            if (badge) {
                                badge.className = 'text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider text-white shadow-xs bg-' + (theme === 'amber' ? 'amber' : (theme === 'blue' ? 'blue' : 'emerald')) + '-600';
                            }
                        } else {
                            if (icon) {
                                icon.className = 'text-base text-secondary-400';
                            }
                            if (simpleIcon) {
                                simpleIcon.className = 'text-xl text-secondary-400';
                            }
                            if (dot) {
                                dot.className = 'w-2.5 h-2.5 rounded-full bg-secondary-300';
                            }
                            if (badge) {
                                badge.className = 'text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider bg-secondary-100 text-secondary-500';
                            }
                        }
                    });
                });
            });
        });

        // Live Search for Delivery Points Table
        const pointSearchInput = document.getElementById('pointTableSearch');
        if (pointSearchInput) {
            pointSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.point-row');
                rows.forEach(row => {
                    const text = row.getAttribute('data-point-search') || '';
                    if (query === '' || text.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Live Toggle Switch Handler
        const toggleCheckboxes = document.querySelectorAll('.custom-toggle input[type="checkbox"]');
        toggleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const track = this.closest('.custom-toggle')?.querySelector('.toggle-track');
                if (track) {
                    track.classList.toggle('is-checked', this.checked);
                }
            });
        });
    });

    // Global Document Delegation for Toggle Switches
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('.custom-toggle input[type="checkbox"]')) {
            const track = e.target.closest('.custom-toggle')?.querySelector('.toggle-track');
            if (track) {
                track.classList.toggle('is-checked', e.target.checked);
            }
        }
    });

    // Dynamic Spend-More Offers Tier Management
    function updateTierRewardTypeUI(selectEl) {
        const row = selectEl.closest('.tier-row');
        if (!row) return;
        const type = selectEl.value;
        const wrapper = row.querySelector('.tier-reward-val-wrapper');
        if (!wrapper) return;
        const labelEl = wrapper.querySelector('.tier-val-label');
        const containerEl = wrapper.querySelector('.tier-val-input-container');
        const nameAttr = selectEl.name.replace('[type]', '[reward_value]');
        const existingVal = containerEl.querySelector('input') ? containerEl.querySelector('input').value : '';

        if (type === 'discount_flat') {
            labelEl.textContent = 'Discount (৳ ছাড়)';
            containerEl.innerHTML = `
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                    <input type="number" step="any" min="0" name="${nameAttr}" value="${existingVal || '100'}" placeholder="100" 
                           class="w-full pl-6 pr-2 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>`;
        } else if (type === 'discount_percent') {
            labelEl.textContent = 'Discount (% ছাড়)';
            containerEl.innerHTML = `
                <div class="relative">
                    <input type="number" step="any" min="0" max="100" name="${nameAttr}" value="${existingVal || '10'}" placeholder="10" 
                           class="w-full pl-2.5 pr-6 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-secondary-400 font-bold text-xs">%</span>
                </div>`;
        } else if (type === 'free_gift') {
            labelEl.textContent = 'Gift Item / উপহার পণ্য';
            containerEl.innerHTML = `
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-sm">🎁</span>
                    <input type="text" name="${nameAttr}" value="${existingVal || ''}" placeholder="যেমন: ১ কেজি চিনি বা মসলা প্যাক" 
                           class="w-full pl-7 pr-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>`;
        } else if (type === 'custom') {
            labelEl.textContent = 'Special Perk / সুবিধা';
            containerEl.innerHTML = `
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-sm">🌟</span>
                    <input type="text" name="${nameAttr}" value="${existingVal || ''}" placeholder="যেমন: ডাবল রিওয়ার্ড পয়েন্ট" 
                           class="w-full pl-7 pr-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>`;
        } else { // free_delivery
            labelEl.textContent = 'Reward Value / সুবিধা';
            containerEl.innerHTML = `
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold truncate shadow-2xs">
                    <span>✓</span> <span>100% Free Shipping</span>
                    <input type="hidden" name="${nameAttr}" value="0">
                </div>`;
        }
    }

    function addSpendMoreTierRow() {
        const container = document.getElementById('spend-more-tiers-container');
        if (!container) return;
        const rowCount = container.querySelectorAll('.tier-row').length;
        const newIdx = Date.now();
        const rowNum = rowCount + 1;
        
        const div = document.createElement('div');
        div.className = 'tier-row bg-white hover:bg-secondary-50/50 border border-secondary-200/90 rounded-2xl p-4 transition-all shadow-xs space-y-3.5 animate-fadeIn';
        div.innerHTML = `
            <!-- Top Row: 12-Column Grid (Icon, Spend, Type, Value/Gift, Actions) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <!-- 1. Number & Icon (2 cols) -->
                <div class="sm:col-span-2 min-w-0">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Icon / Emoji</label>
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 shrink-0 rounded-lg bg-secondary-100 border border-secondary-200 flex items-center justify-center text-xs font-black text-secondary-700 shadow-2xs row-number">
                            ${rowNum}
                        </span>
                        <input type="text" name="spend_more_offers_tiers[${newIdx}][icon]" value="🎁" 
                               title="Icon / Emoji" placeholder="🎁"
                               class="w-12 text-center py-1.5 text-base font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                    </div>
                </div>

                <!-- 2. Spend Amount (2 cols) -->
                <div class="sm:col-span-2 min-w-0">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Spend (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-secondary-400 font-bold text-xs">৳</span>
                        <input type="number" name="spend_more_offers_tiers[${newIdx}][min_amount]" value="${(rowNum * 500) + 500}" 
                               class="w-full pl-6 pr-2 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                    </div>
                </div>

                <!-- 3. Reward Type (3 cols) -->
                <div class="sm:col-span-3 min-w-0">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Reward Type</label>
                    <select name="spend_more_offers_tiers[${newIdx}][type]" onchange="updateTierRewardTypeUI(this)" 
                            class="tier-type-select w-full px-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                        <option value="free_delivery">🚚 Free Delivery</option>
                        <option value="free_gift" selected>🎁 Free Gift</option>
                        <option value="discount_flat">🏷️ Flat Cash Discount</option>
                        <option value="discount_percent">⚡ % Discount</option>
                        <option value="custom">🌟 Special Perk</option>
                    </select>
                </div>

                <!-- 4. Dynamic Value / Gift Name (3 cols) -->
                <div class="sm:col-span-3 min-w-0 tier-reward-val-wrapper">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1 tier-val-label truncate">
                        Gift Item / উপহার পণ্য
                    </label>
                    <div class="tier-val-input-container">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-sm">🎁</span>
                            <input type="text" name="spend_more_offers_tiers[${newIdx}][reward_value]" value="" placeholder="যেমন: ১ কেজি চিনি বা মসলা প্যাক" 
                                   class="w-full pl-7 pr-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                        </div>
                    </div>
                </div>

                <!-- 5. Active & Delete (2 cols) -->
                <div class="sm:col-span-2 min-w-0 flex items-center justify-end gap-3 pb-1">
                    <label class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary-700 cursor-pointer select-none">
                        <input type="checkbox" name="spend_more_offers_tiers[${newIdx}][enabled]" value="1" checked
                               class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                        <span>Active</span>
                    </label>
                    <button type="button" onclick="removeSpendMoreTierRow(this)" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors cursor-pointer shrink-0 shadow-2xs" title="Delete Tier">
                        <ion-icon name="trash-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <!-- Bottom Row: Titles & Description -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-secondary-100">
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Title (বাংলা)</label>
                    <input type="text" name="spend_more_offers_tiers[${newIdx}][title]" value="ফ্রি স্পেশাল গিফট" placeholder="যেমন: ফ্রি গিফট" 
                           class="w-full px-2.5 py-1.5 text-xs font-bold border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Title (English)</label>
                    <input type="text" name="spend_more_offers_tiers[${newIdx}][title_en]" value="Special Free Gift" placeholder="e.g. Free Gift" 
                           class="w-full px-2.5 py-1.5 text-xs font-medium border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-1">Description / Benefit</label>
                    <input type="text" name="spend_more_offers_tiers[${newIdx}][desc]" value="একটি আকর্ষণীয় উপহার সামগ্রী" placeholder="উপহার বা ছাড়ের বিবরণ" 
                           class="w-full px-2.5 py-1.5 text-xs font-medium border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    function removeSpendMoreTierRow(btn) {
        const row = btn.closest('.tier-row');
        const container = document.getElementById('spend-more-tiers-container');
        if (!container || !row) return;
        if (container.querySelectorAll('.tier-row').length > 1) {
            row.remove();
            container.querySelectorAll('.tier-row').forEach((r, idx) => {
                const numEl = r.querySelector('.row-number');
                if (numEl) numEl.textContent = idx + 1;
            });
        } else {
            alert('You must keep at least one promotional offer milestone tier.');
        }
    }

    // ── SMS Gateway field visibility ──────────────────────────────────────
    function toggleSmsFields() {
        const provider = document.getElementById('sms_provider').value;

        // All sms-field divs
        document.getElementById('field_api_token').style.display = provider && provider !== 'twilio' ? '' : 'none';
        document.getElementById('field_sender_id').style.display = provider ? '' : 'none';
        document.getElementById('field_twilio').classList.toggle('hidden', provider !== 'twilio');

        // Help links
        document.querySelectorAll('.sms-help').forEach(el => el.classList.add('hidden'));
        const helpMap = { greenweb: 'help_greenweb', ssl_wireless: 'help_ssl', bulksmsbd: 'help_bulksmsbd', twilio: 'help_twilio' };
        if (helpMap[provider]) document.getElementById(helpMap[provider]).classList.remove('hidden');
    }
    // Run on page load to reflect saved provider
    document.addEventListener('DOMContentLoaded', toggleSmsFields);
    </script>

</div>
