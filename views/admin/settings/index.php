<div class="max-w-5xl mx-auto mb-16">

    <style>
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
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-rose) {
        border-color: #e2e8f0;
        background-color: #ffffff;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-rose) ion-icon {
        color: #94a3b8 !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-rose) .radio-indicator {
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .radio-select-card:not(.is-selected-emerald):not(.is-selected-blue):not(.is-selected-rose) .radio-indicator-dot {
        transform: scale(0) !important;
    }
    </style>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">সেটিংস (Settings)</h1>
            <p class="text-secondary-500 text-xs mt-1">সিস্টেম কনফিগারেশন, ডেলিভারি চার্জ ও জেনারেল ইনফো।</p>
        </div>
    </div>

    <!-- Settings Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-secondary-200 mb-8">
        <a href="/sodai-dorkar/public/admin/settings" 
           class="px-5 py-3 font-bold text-sm transition-all border-b-2 flex items-center gap-2 text-primary-600 border-primary-600 bg-primary-50/40 rounded-t-xl">
            <ion-icon name="settings-outline" class="text-lg"></ion-icon>
            সাধারণ সেটিংস (General)
        </a>
        <a href="/sodai-dorkar/public/admin/settings/units" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-secondary-500 border-transparent hover:text-secondary-800">
            <ion-icon name="scale-outline" class="text-lg"></ion-icon>
            একক ও প্যাকেজিং অপশন (Units & Packaging)
        </a>
        <?php if (\Core\Auth::can('database_reset') || \Core\Auth::isAdmin()): ?>
        <a href="/sodai-dorkar/public/admin/settings/cleanup" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-red-500 border-transparent hover:text-red-700">
            <ion-icon name="trash-bin-outline" class="text-lg"></ion-icon>
            ডাটা ক্লিনআপ ও রিসেট (Data Reset)
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">Settings updated successfully.</span>
    </div>
    <?php endif; ?>

    <form action="<?= $base ?>/admin/settings/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        
        <!-- Delivery Logic Section -->
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex items-center gap-2">
                 <div class="bg-primary-100 text-primary-600 p-2 rounded-lg">
                     <ion-icon name="bicycle" class="text-xl"></ion-icon>
                 </div>
                 <div>
                     <h3 class="font-bold text-secondary-700">Delivery Charge Logic</h3>
                     <p class="text-xs text-secondary-500">Configure standard charges and free delivery offers.</p>
                 </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Base Charge -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Default Delivery Charge (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-secondary-500">৳</span>
                        <input type="number" name="delivery_charge_default" 
                               value="<?php echo htmlspecialchars($settings['delivery_charge_default'] ?? '60'); ?>" 
                               class="w-full pl-8 pr-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    </div>
                    <p class="text-xs text-secondary-400 mt-1">Applied automatically to new orders.</p>
                </div>

                <!-- Free Threshold -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Free Delivery Threshold (৳)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-secondary-500">৳</span>
                        <input type="number" name="delivery_free_threshold" 
                               value="<?php echo htmlspecialchars($settings['delivery_free_threshold'] ?? '5000'); ?>" 
                               class="w-full pl-8 pr-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    </div>
                    <p class="text-xs text-secondary-400 mt-1">Orders above this amount get <span class="text-green-600 font-bold">Free Delivery</span>.</p>
                </div>
            </div>
        </div>

        <!-- Delivery Man Allocation & Rider Configuration -->
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex items-center gap-2">
                 <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg">
                     <ion-icon name="navigate-outline" class="text-xl"></ion-icon>
                 </div>
                 <div>
                     <h3 class="font-bold text-secondary-700">Delivery Allocation & Rider Management</h3>
                     <p class="text-xs text-secondary-500">Configure order auto-assignment rules and rider parcel transfer permissions.</p>
                 </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Allocation Mode (Auto vs Manual) -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Order Allocation Mode</label>
                    <?php $allocMode = $settings['delivery_allocation_mode'] ?? 'auto'; ?>
                    <div class="grid grid-cols-2 gap-3" data-radio-group="delivery_allocation_mode">
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_allocation_mode" value="auto" <?php echo ($allocMode === 'auto') ? 'checked' : ''; ?> class="sr-only" data-theme="emerald">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?php echo ($allocMode === 'auto') ? 'is-selected-emerald' : ''; ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="flash" class="text-xl <?php echo ($allocMode === 'auto') ? 'text-emerald-600' : 'text-secondary-400'; ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Auto Assign</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5 leading-tight">Assign to area rider automatically</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_allocation_mode" value="manual" <?php echo ($allocMode === 'manual') ? 'checked' : ''; ?> class="sr-only" data-theme="blue">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?php echo ($allocMode === 'manual') ? 'is-selected-blue' : ''; ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="hand-right-outline" class="text-xl <?php echo ($allocMode === 'manual') ? 'text-blue-600' : 'text-secondary-400'; ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Manual Assign</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5 leading-tight">Admin assigns rider manually</div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-secondary-400 mt-2">When Auto Assign is on, new orders are assigned to the delivery man allocated to customer's area.</p>
                </div>

                <!-- Allow Rider-to-Rider Transfer -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Rider-to-Rider Parcel Handover</label>
                    <?php $allowTransfer = $settings['allow_rider_transfer'] ?? '1'; ?>
                    <div class="grid grid-cols-2 gap-3" data-radio-group="allow_rider_transfer">
                        <label class="cursor-pointer">
                            <input type="radio" name="allow_rider_transfer" value="1" <?php echo ($allowTransfer === '1') ? 'checked' : ''; ?> class="sr-only" data-theme="emerald">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?php echo ($allowTransfer === '1') ? 'is-selected-emerald' : ''; ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="swap-horizontal" class="text-xl <?php echo ($allowTransfer === '1') ? 'text-emerald-600' : 'text-secondary-400'; ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Allowed</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5 leading-tight">Riders can handover parcels</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="allow_rider_transfer" value="0" <?php echo ($allowTransfer === '0') ? 'checked' : ''; ?> class="sr-only" data-theme="rose">
                            <div class="radio-select-card p-3.5 flex flex-col items-center text-center <?php echo ($allowTransfer === '0') ? 'is-selected-rose' : ''; ?>">
                                <div class="w-full flex items-center justify-between mb-2">
                                    <ion-icon name="ban-outline" class="text-xl <?php echo ($allowTransfer === '0') ? 'text-rose-600' : 'text-secondary-400'; ?>"></ion-icon>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-secondary-800">Disabled</div>
                                <div class="text-[10px] text-secondary-500 mt-0.5 leading-tight">Only admin can reassign</div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-secondary-400 mt-2">When enabled, riders can transfer their assigned parcel to another active rider from their dashboard.</p>
                </div>

                <!-- Multi-Rider Allocation Strategy -->
                <div class="md:col-span-2 pt-2 border-t border-secondary-100">
                    <label class="block text-sm font-bold text-secondary-700 mb-1">Multi-Rider Area Allocation Strategy</label>
                    <?php $strategy = $settings['delivery_auto_assign_strategy'] ?? 'least_busy'; ?>
                    <div class="max-w-md">
                        <select name="delivery_auto_assign_strategy" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white text-sm">
                            <option value="least_busy" <?php echo ($strategy === 'least_busy') ? 'selected' : ''; ?>>
                                Least Busy First (Rider with fewest active orders)
                            </option>
                            <option value="round_robin" <?php echo ($strategy === 'round_robin') ? 'selected' : ''; ?>>
                                Round Robin (Equal distribution among area riders)
                            </option>
                        </select>
                        <p class="text-xs text-secondary-400 mt-1">If multiple delivery men are allocated to the same union/area, this rule determines who receives the order.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point of Sale (POS) Settings Section -->
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="bg-emerald-100 text-emerald-700 p-2 rounded-lg">
                        <ion-icon name="calculator-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-secondary-800">পয়েন্ট অব সেল (POS) ও কাউন্টার সেটিংস</h3>
                        <p class="text-xs text-secondary-500">POS কাউন্টার সেল ও অর্ডার এডিটের সময় প্রাইসিং কনফিগারেশন।</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">POS Terminal</span>
            </div>
            
            <div class="p-6">
                <?php $isCustomPriceEnabled = (($settings['pos_allow_custom_price'] ?? '0') === '1'); ?>
                <div class="flex items-start justify-between gap-4 p-4 rounded-xl border <?= $isCustomPriceEnabled ? 'border-emerald-300 bg-emerald-50/40' : 'border-secondary-200 bg-secondary-50/40' ?> transition-all">
                    <div class="space-y-1">
                        <label class="font-bold text-sm text-secondary-900 cursor-pointer flex items-center gap-2" for="pos_allow_custom_price">
                            <span>POS এ প্রডাক্ট এর সেল প্রাইস আপডেট বা ইডিট করার অনুমতি</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $isCustomPriceEnabled ? 'bg-emerald-100 text-emerald-800' : 'bg-secondary-200 text-secondary-600' ?>">
                                <?= $isCustomPriceEnabled ? 'অন (Active)' : 'অফ (Disabled)' ?>
                            </span>
                        </label>
                        <p class="text-xs text-secondary-500 leading-relaxed max-w-2xl">
                            চালু থাকলে POS কাউন্টারে বা অর্ডার এডিটের সময় যেকোনো পণ্যের ইউনিট বিক্রয় মূল্য সরাসরি পরিবর্তন/কাস্টমাইজ করা যাবে (যেমন: দরদাম, বিশেষ কাউন্টার ছাড় বা ফ্ল্যাট রেট)। বন্ধ থাকলে পণ্যটি শুধুমাত্র সিস্টেমে নির্ধারিত বিক্রয় মূল্যেই যুক্ত হবে।
                        </p>
                    </div>
                    <div class="flex items-center shrink-0">
                        <input type="hidden" name="pos_allow_custom_price" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="pos_allow_custom_price" name="pos_allow_custom_price" value="1" <?= $isCustomPriceEnabled ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
             <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex items-center gap-2">
                 <div class="bg-purple-100 text-purple-600 p-2 rounded-lg">
                     <ion-icon name="shield-checkmark-outline" class="text-xl"></ion-icon>
                 </div>
                 <div>
                     <h3 class="font-bold text-secondary-700">Customer Authentication</h3>
                     <p class="text-xs text-secondary-500">Enable or disable login features for the checkout flow.</p>
                 </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Manual PIN -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Manual Support PIN Reset</label>
                    <select name="auth_manual_pin_enabled" class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                        <option value="1" <?php echo ($settings['auth_manual_pin_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo ($settings['auth_manual_pin_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                    <p class="text-xs text-secondary-400 mt-1">Allows admins to generate a 4-digit PIN for customers to login and reset their password.</p>
                </div>

                <!-- Firebase OTP -->
                <div class="md:col-span-2">
                    <div class="bg-orange-50 border border-orange-200 rounded-xl overflow-hidden">
                        <div class="px-5 py-3 border-b border-orange-200 bg-orange-100 flex items-center gap-2">
                            <ion-icon name="flame-outline" class="text-orange-600 text-xl"></ion-icon>
                            <div>
                                <h4 class="font-bold text-orange-800 text-sm">Firebase Phone Authentication (OTP)</h4>
                                <p class="text-xs text-orange-600">বিনামূল্যে SMS OTP — Firebase Console থেকে config কপি করুন।</p>
                            </div>
                        </div>
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Enable/Disable -->
                            <div class="md:col-span-2 flex items-center justify-between gap-4 pb-3 border-b border-orange-200">
                                <div>
                                    <label class="block text-sm font-bold text-secondary-700">Firebase OTP Status</label>
                                    <p class="text-xs text-secondary-400 mt-0.5">Signup ও Login-এ Firebase Phone OTP চালু/বন্ধ করুন।</p>
                                </div>
                                <select name="auth_firebase_otp_enabled" class="px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-bold">
                                    <option value="1" <?= ($settings['auth_firebase_otp_enabled'] ?? '0') == '1' ? 'selected' : '' ?>>✅ Enabled</option>
                                    <option value="0" <?= ($settings['auth_firebase_otp_enabled'] ?? '0') == '0' ? 'selected' : '' ?>>❌ Disabled</option>
                                </select>
                            </div>

                            <!-- API Key -->
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wide">API Key</label>
                                <input type="text" name="firebase_api_key"
                                       value="<?= htmlspecialchars($settings['firebase_api_key'] ?? '') ?>"
                                       placeholder="AIzaSy..."
                                       class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm font-mono">
                            </div>

                            <!-- Auth Domain -->
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wide">Auth Domain</label>
                                <input type="text" name="firebase_auth_domain"
                                       value="<?= htmlspecialchars($settings['firebase_auth_domain'] ?? '') ?>"
                                       placeholder="your-project.firebaseapp.com"
                                       class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm font-mono">
                            </div>

                            <!-- Project ID -->
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wide">Project ID</label>
                                <input type="text" name="firebase_project_id"
                                       value="<?= htmlspecialchars($settings['firebase_project_id'] ?? '') ?>"
                                       placeholder="your-project-id"
                                       class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm font-mono">
                            </div>

                            <!-- App ID -->
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wide">App ID</label>
                                <input type="text" name="firebase_app_id"
                                       value="<?= htmlspecialchars($settings['firebase_app_id'] ?? '') ?>"
                                       placeholder="1:000000000000:web:xxxxxxxxxxxxxxxx"
                                       class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm font-mono">
                            </div>

                            <!-- Messaging Sender ID (needed for SDK init, optional) -->
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wide">Messaging Sender ID</label>
                                <input type="text" name="firebase_messaging_sender_id"
                                       value="<?= htmlspecialchars($settings['firebase_messaging_sender_id'] ?? '') ?>"
                                       placeholder="290621496533"
                                       class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm font-mono">
                            </div>

                            <!-- Help note -->
                            <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-xs text-blue-700 leading-relaxed">
                                💡 <strong>কোথায় পাবেন?</strong>
                                <a href="https://console.firebase.google.com" target="_blank" class="underline font-bold">console.firebase.google.com</a>
                                → Project → Project Settings → Your apps → Web app → <code class="bg-blue-100 px-1 rounded">firebaseConfig</code><br>
                                ⚠️ <strong>Authorized domain:</strong> Firebase Console → Authentication → Settings → Authorized domains → <code class="bg-blue-100 px-1 rounded">digisoftbd.xyz</code> যোগ করুন।
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storefront Homepage Variant Selector (3 Variants) -->
        <?php $currentHomeVariant = $settings['homepage_variant'] ?? 'variant_1'; ?>
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-secondary-100 bg-gradient-to-r from-emerald-50/70 via-teal-50/40 to-white flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2.5">
                    <div class="bg-emerald-100 text-emerald-700 p-2 rounded-lg">
                        <ion-icon name="color-wand-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-secondary-800">হোম পেজ ডিজাইন ভ্যারিয়েন্ট (Storefront Homepage Layout Variants)</h3>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">A/B Testing Ready</span>
                        </div>
                        <p class="text-xs text-secondary-500">কাস্টমারদের পছন্দের উপর ভিত্তি করে ৩টি আধুনিক হোম পেজ ডিজাইন থেকে যেকোনো একটি চালু করুন বা লাইভ প্রিভিউ দেখুন।</p>
                    </div>
                </div>
                <a href="<?= $base ?>/" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg shadow-2xs transition-colors">
                    <ion-icon name="open-outline" class="text-sm"></ion-icon>
                    <span>বর্তমান হোম পেজ দেখুন</span>
                </a>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" data-radio-group="homepage_variant">
                    <!-- Variant 1: Modern Full-Width App Grid -->
                    <label class="block">
                        <input type="radio" name="homepage_variant" value="variant_1" data-theme="emerald" class="sr-only" <?= $currentHomeVariant === 'variant_1' ? 'checked' : '' ?>>
                        <div class="radio-select-card p-4 h-full flex flex-col justify-between <?= $currentHomeVariant === 'variant_1' ? 'is-selected-emerald' : '' ?>">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <ion-icon name="phone-portrait-outline" class="text-lg"></ion-icon>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 block">ভ্যারিয়েন্ট ১ (প্রস্তাবিত)</span>
                                            <h4 class="font-black text-secondary-900 text-sm leading-tight">মডার্ন ফুল-উইডথ অ্যাপ স্টাইল</h4>
                                        </div>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>

                                <!-- Mini Visual Wireframe Preview -->
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 mb-3 space-y-1.5">
                                    <div class="h-6 rounded-lg bg-gradient-to-r from-emerald-700 to-teal-600 flex items-center px-2 justify-between">
                                        <span class="w-12 h-1.5 bg-white/80 rounded-full"></span>
                                        <span class="w-5 h-3 bg-amber-400 rounded text-[7px] font-black text-slate-900 flex items-center justify-center">ডিল</span>
                                    </div>
                                    <div class="flex gap-1 overflow-hidden">
                                        <span class="h-4 flex-1 rounded bg-white border border-emerald-200"></span>
                                        <span class="h-4 flex-1 rounded bg-white border border-slate-200"></span>
                                        <span class="h-4 flex-1 rounded bg-white border border-slate-200"></span>
                                        <span class="h-4 flex-1 rounded bg-white border border-slate-200"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5 pt-0.5">
                                        <div class="h-8 rounded bg-white border border-slate-200 p-1 flex flex-col justify-between"><span class="w-full h-3 bg-emerald-50 rounded"></span><span class="w-full h-1.5 bg-emerald-600 rounded"></span></div>
                                        <div class="h-8 rounded bg-white border border-slate-200 p-1 flex flex-col justify-between"><span class="w-full h-3 bg-emerald-50 rounded"></span><span class="w-full h-1.5 bg-emerald-600 rounded"></span></div>
                                        <div class="h-8 rounded bg-white border border-slate-200 p-1 flex flex-col justify-between"><span class="w-full h-3 bg-emerald-50 rounded"></span><span class="w-full h-1.5 bg-emerald-600 rounded"></span></div>
                                    </div>
                                </div>

                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    মোবাইলে প্রশস্ত ফুল-উইডথ প্রোডাক্ট গ্রিড, হরিজন্টাল ভিজ্যুয়াল ক্যাটাগরি স্লাইডার ও ডেস্কটপে মডার্ন সাইডবার। (Chaldal / Zepto স্টাইল)
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-emerald-700">📱 মোবাইলের জন্য সেরা</span>
                                <a href="<?= $base ?>/?home_variant=variant_1" target="_blank" onclick="event.stopPropagation();" class="text-[11px] font-black text-primary-600 hover:underline flex items-center gap-0.5">
                                    <span>প্রিভিউ দেখুন ↗</span>
                                </a>
                            </div>
                        </div>
                    </label>

                    <!-- Variant 2: Smart Split Sidebar App Rail -->
                    <label class="block">
                        <input type="radio" name="homepage_variant" value="variant_2" data-theme="blue" class="sr-only" <?= $currentHomeVariant === 'variant_2' ? 'checked' : '' ?>>
                        <div class="radio-select-card p-4 h-full flex flex-col justify-between <?= $currentHomeVariant === 'variant_2' ? 'is-selected-blue' : '' ?>">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                            <ion-icon name="grid-outline" class="text-lg"></ion-icon>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 block">ভ্যারিয়েন্ট ২</span>
                                            <h4 class="font-black text-secondary-900 text-sm leading-tight">স্মার্ট স্প্লিট অ্যাপ রেল</h4>
                                        </div>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>

                                <!-- Mini Visual Wireframe Preview -->
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 mb-3 flex gap-1.5 h-[82px]">
                                    <div class="w-7 bg-white border border-blue-200 rounded-lg p-1 flex flex-col gap-1 items-center">
                                        <span class="w-4 h-4 rounded bg-blue-100 border border-blue-400"></span>
                                        <span class="w-4 h-4 rounded bg-slate-100"></span>
                                        <span class="w-4 h-4 rounded bg-slate-100"></span>
                                    </div>
                                    <div class="flex-1 flex flex-col gap-1.5">
                                        <div class="h-5 rounded-md bg-gradient-to-r from-emerald-800 to-teal-700 px-2 flex items-center">
                                            <span class="w-10 h-1.5 bg-white/80 rounded-full"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1.5 flex-1">
                                            <div class="rounded bg-white border border-slate-200 p-1 flex flex-col justify-between"><span class="w-full h-3 bg-blue-50 rounded"></span><span class="w-full h-1.5 bg-emerald-600 rounded"></span></div>
                                            <div class="rounded bg-white border border-slate-200 p-1 flex flex-col justify-between"><span class="w-full h-3 bg-blue-50 rounded"></span><span class="w-full h-1.5 bg-emerald-600 rounded"></span></div>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    বাম পাশে স্লিম ও নিখুঁত ক্যাটাগরি আইকন রেল এবং ডান পাশে কমপ্যাক্ট ব্যানার ও অপ্টিমাইজড প্রোডাক্ট কার্ড। (Blinkit / Instamart স্টাইল)
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-blue-700">⚡ দ্রুত ক্যাটাগরি সুইচিং</span>
                                <a href="<?= $base ?>/?home_variant=variant_2" target="_blank" onclick="event.stopPropagation();" class="text-[11px] font-black text-blue-600 hover:underline flex items-center gap-0.5">
                                    <span>প্রিভিউ দেখুন ↗</span>
                                </a>
                            </div>
                        </div>
                    </label>

                    <!-- Variant 3: Superstore Magazine & Flash Showcase -->
                    <label class="block">
                        <input type="radio" name="homepage_variant" value="variant_3" data-theme="rose" class="sr-only" <?= $currentHomeVariant === 'variant_3' ? 'checked' : '' ?>>
                        <div class="radio-select-card p-4 h-full flex flex-col justify-between <?= $currentHomeVariant === 'variant_3' ? 'is-selected-rose' : '' ?>">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                            <ion-icon name="storefront-outline" class="text-lg"></ion-icon>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 block">ভ্যারিয়েন্ট ৩</span>
                                            <h4 class="font-black text-secondary-900 text-sm leading-tight">সুপারস্টোর ও ফ্ল্যাশ শোকেস</h4>
                                        </div>
                                    </div>
                                    <div class="radio-indicator">
                                        <div class="radio-indicator-dot"></div>
                                    </div>
                                </div>

                                <!-- Mini Visual Wireframe Preview -->
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 mb-3 space-y-1.5">
                                    <div class="grid grid-cols-3 gap-1">
                                        <div class="col-span-2 h-6 rounded-md bg-gradient-to-r from-emerald-800 to-emerald-600"></div>
                                        <div class="h-6 rounded-md bg-gradient-to-r from-amber-500 to-rose-500"></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-1">
                                        <span class="h-4 rounded bg-rose-50 border border-rose-200"></span>
                                        <span class="h-4 rounded bg-amber-50 border border-amber-200"></span>
                                        <span class="h-4 rounded bg-emerald-50 border border-emerald-200"></span>
                                        <span class="h-4 rounded bg-sky-50 border border-sky-200"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5">
                                        <div class="h-7 rounded bg-white border border-slate-200 p-1"><span class="block w-full h-full bg-rose-50 rounded"></span></div>
                                        <div class="h-7 rounded bg-white border border-slate-200 p-1"><span class="block w-full h-full bg-rose-50 rounded"></span></div>
                                        <div class="h-7 rounded bg-white border border-slate-200 p-1"><span class="block w-full h-full bg-rose-50 rounded"></span></div>
                                    </div>
                                </div>

                                <p class="text-xs text-secondary-500 leading-relaxed">
                                    বেন্টো হিরো ব্যানার, ভিজ্যুয়াল ক্যাটাগরি বক্স গ্রিড, ফ্ল্যাশ ডিলস হাইলাইট সেকশন ও ট্যাবড ক্যাটালগ। (Shwapno / MeenaClick স্টাইল)
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-rose-700">🔥 অফার ও ব্র্যান্ড ফোকাসড</span>
                                <a href="<?= $base ?>/?home_variant=variant_3" target="_blank" onclick="event.stopPropagation();" class="text-[11px] font-black text-rose-600 hover:underline flex items-center gap-0.5">
                                    <span>প্রিভিউ দেখুন ↗</span>
                                </a>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- General Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
             <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex items-center gap-2">
                 <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                     <ion-icon name="globe-outline" class="text-xl"></ion-icon>
                 </div>
                 <div>
                     <h3 class="font-bold text-secondary-700">General Information (স্টোরের ব্র্যান্ডিং, লোগো ও যোগাযোগের তথ্য)</h3>
                     <p class="text-xs text-secondary-500">ওয়েবসাইটের লোগো, নাম, স্লোগান, যোগাযোগের ফোন, ইমেইল, হোয়াটসঅ্যাপ এবং ঠিকানা আপডেট করুন।</p>
                 </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Site Logo Upload -->
                <div class="md:col-span-2 bg-emerald-50/40 border border-emerald-200/80 rounded-xl p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-1">
                        <ion-icon name="ribbon-outline" class="text-emerald-600 text-lg"></ion-icon>
                        <label class="block text-sm font-bold text-secondary-800">ওয়েবসাইটের অফিসিয়াল লোগো (Site Logo Upload)</label>
                    </div>
                    <p class="text-xs text-secondary-500 mb-4">স্টোরফ্রন্টের হেডার, মোবাইল বার এবং ফুটারে প্রদর্শনের জন্য আপনার ব্র্যান্ডের লোগো আপলোড করুন। (প্রস্তাবিত ফরম্যাট: PNG, SVG বা WEBP, ট্রান্সপারেন্ট ব্যাকগ্রাউন্ড হলে সবচেয়ে সুন্দর দেখাবে)</p>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <?php 
                            $currentSiteLogo = !empty($settings['site_logo']) ? $settings['site_logo'] : '';
                            $logoPreviewUrl = !empty($currentSiteLogo) ? ($base . $currentSiteLogo) : '';
                        ?>
                        <div class="relative w-40 h-20 rounded-xl overflow-hidden border-2 border-emerald-500/40 shadow-xs bg-white shrink-0 flex items-center justify-center p-2 group">
                            <img id="site-logo-preview" src="<?= htmlspecialchars($logoPreviewUrl) ?>" alt="Site Logo" class="max-w-full max-h-full object-contain <?= empty($logoPreviewUrl) ? 'hidden' : '' ?>">
                            <div id="site-logo-fallback" class="flex items-center gap-1.5 <?= !empty($logoPreviewUrl) ? 'hidden' : '' ?>">
                                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                                    <ion-icon name="cart-outline" class="text-lg"></ion-icon>
                                </div>
                                <span class="text-xs font-black text-slate-800"><?= htmlspecialchars($settings['site_title'] ?? 'Sohoz Sodai') ?></span>
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-2.5">
                            <input type="file" name="site_logo" id="site_logo_input" accept="image/png,image/webp,image/svg+xml,image/jpeg,image/gif"
                                   onchange="previewSiteLogo(this)"
                                   class="block w-full text-xs text-secondary-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 file:cursor-pointer cursor-pointer border border-secondary-300 rounded-lg p-1.5 bg-white">
                            
                            <?php if (!empty($currentSiteLogo)): ?>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-1.5 text-xs text-red-600 cursor-pointer font-semibold hover:text-red-700">
                                    <input type="checkbox" name="remove_site_logo" value="1" class="rounded border-secondary-300 text-red-600 focus:ring-red-500">
                                    <span>বর্তমান লোগো মুছে ডিফল্ট আইকন লোগোতে ফিরিয়ে নিন</span>
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="text-[11px] text-secondary-500 bg-white border border-emerald-200/60 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                                <span>✨</span>
                                <span>লোগো আপলোড করলে হেডার, ফুটার এবং ব্রাউজার ট্যাবে (Favicon) স্বয়ংক্রিয়ভাবে যুক্ত হবে।</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Site Title -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">ওয়েবসাইটের নাম (Website / Brand Name)</label>
                    <input type="text" name="site_title" 
                           value="<?php echo htmlspecialchars($settings['site_title'] ?? 'Fresh E mart'); ?>" 
                           placeholder="Fresh E mart"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all font-semibold">
                    <p class="text-xs text-secondary-400 mt-1">ব্রাউজার ট্যাব, হেডার, সোশ্যাল মিডিয়া শেয়ার ও ইনভয়েসে প্রধান নাম হিসেবে প্রদর্শিত হবে।</p>
                </div>

                <!-- Site Tagline -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">ওয়েবসাইটের ট্যাগলাইন / স্লোগান (Tagline & Tab Title)</label>
                    <input type="text" name="site_tagline" 
                           value="<?php echo htmlspecialchars($settings['site_tagline'] ?? 'ফ্রেশ গ্রোসারি ডেলিভারি'); ?>" 
                           placeholder="ফ্রেশ গ্রোসারি ডেলিভারি"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    <p class="text-xs text-secondary-400 mt-1">ব্রাউজার ট্যাবে এবং ফেসবুক/হোয়াটসঅ্যাপে লিংক শেয়ারে সাইটের নামের পাশে এই ট্যাগলাইন দেখাবে (যেমন: Fresh E mart - ফ্রেশ গ্রোসারি ডেলিভারি)।</p>
                </div>

                <!-- Site Meta Description for SEO and Social Share -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary-700 mb-2">ওয়েবসাইটের বিবরণ / মেটা ডেসক্রিপশন (SEO & Social Share Description)</label>
                    <textarea name="site_description" rows="2" 
                              placeholder="অনলাইনে নিত্যপ্রয়োজনীয় গ্রোসারি ও সেরা মানের ফ্রেশ বাজার। হোম ডেলিভারি দ্রুত ও বিশ্বস্ত সেবায়।"
                              class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm leading-relaxed"><?php echo htmlspecialchars($settings['site_description'] ?? 'অনলাইনে নিত্যপ্রয়োজনীয় গ্রোসারি ও সেরা মানের ফ্রেশ বাজার। হোম ডেলিভারি দ্রুত ও বিশ্বস্ত সেবায়।'); ?></textarea>
                    <p class="text-xs text-secondary-400 mt-1">ওয়েবসাইটের হোমপেজ বা মূল লিংক সোশ্যাল মিডিয়ায় (Facebook, WhatsApp, Messenger) শেয়ার করলে প্রিভিউ কার্ডে এই বিবরণটি দেখাবে।</p>
                </div>

                <!-- Social Share Image (Open Graph Image) -->
                <div class="md:col-span-2 bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-1">
                        <ion-icon name="image-outline" class="text-emerald-600 text-lg"></ion-icon>
                        <label class="block text-sm font-bold text-secondary-800">সোশ্যাল মিডিয়া শেয়ার ইমেজ / প্রিভিউ ব্যানার (Open Graph Preview Image)</label>
                    </div>
                    <p class="text-xs text-secondary-500 mb-4">ফেসবুক, হোয়াটসঅ্যাপ, মেসেঞ্জার ইত্যাদিতে ওয়েবসাইটের প্রধান লিংক শেয়ার করলে এই ছবিটি বড় প্রিভিউ কার্ড হিসেবে দেখাবে। (প্রস্তাবিত সাইজ: 1200×630 পিক্সেল, ফরম্যাট: JPG বা PNG)</p>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <?php 
                            $currentShareImg = !empty($settings['site_share_image']) ? $settings['site_share_image'] : '';
                            $previewUrl = !empty($currentShareImg) ? ($base . $currentShareImg) : ($base . '/images/grocery_cart_hero.jpg');
                        ?>
                        <div class="relative w-40 h-24 rounded-xl overflow-hidden border-2 border-emerald-500/50 shadow-sm bg-white shrink-0 group">
                            <img id="share-img-preview" src="<?= htmlspecialchars($previewUrl) ?>" alt="Share Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[11px] font-bold">
                                প্রিভিউ থাম্বনেইল
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-2.5">
                            <input type="file" name="site_share_image" id="site_share_image_input" accept="image/jpeg,image/png,image/webp,image/jpg"
                                   onchange="previewShareImage(this)"
                                   class="block w-full text-xs text-secondary-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 file:cursor-pointer cursor-pointer border border-secondary-300 rounded-lg p-1.5 bg-white">
                            
                            <?php if (!empty($currentShareImg)): ?>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-1.5 text-xs text-red-600 cursor-pointer font-semibold hover:text-red-700">
                                    <input type="checkbox" name="remove_site_share_image" value="1" class="rounded border-secondary-300 text-red-600 focus:ring-red-500">
                                    <span>কাস্টম ইমেজ মুছে ডিফল্ট স্টোর ব্যানারে ফিরিয়ে নিন</span>
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="text-[11px] text-secondary-500 bg-amber-50 border border-amber-200/60 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                                <span>💡</span>
                                <span><strong>টিপস:</strong> কোনো প্রোডাক্ট পেজের লিংক শেয়ার করলে স্বয়ংক্রিয়ভাবে সেই প্রোডাক্টের আসল ছবি, নাম ও মূল্য প্রিভিউতে আসবে।</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">হটলাইন / যোগাযোগের ফোন (Contact Phone)</label>
                    <input type="text" name="contact_phone" 
                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '01609448066'); ?>" 
                           placeholder="01609448066"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>

                <!-- Contact Email -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">সাপোর্ট ইমেইল (Support / Contact Email)</label>
                    <input type="email" name="contact_email" 
                           value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'support@freshemart.com'); ?>" 
                           placeholder="support@freshemart.com"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>

                <!-- Contact WhatsApp -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">হোয়াটসঅ্যাপ নম্বর (WhatsApp Number)</label>
                    <input type="text" name="contact_whatsapp" 
                           value="<?php echo htmlspecialchars($settings['contact_whatsapp'] ?? ($settings['contact_phone'] ?? '01609448066')); ?>" 
                           placeholder="01609448066"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>

                <!-- Office Address -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">অফিস / শোরুমের ঠিকানা (Store / Office Address)</label>
                    <input type="text" name="contact_address" 
                           value="<?php echo htmlspecialchars($settings['contact_address'] ?? 'Dhaka, Bangladesh'); ?>" 
                           placeholder="Dhaka, Bangladesh"
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform active:scale-95 transition-all flex items-center gap-2">
                <ion-icon name="save-outline" class="text-xl"></ion-icon>
                Save Settings
            </button>
        </div>

    </form>

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

                        card.classList.remove('is-selected-emerald', 'is-selected-blue', 'is-selected-rose');
                        if (r.checked) {
                            card.classList.add('is-selected-' + theme);
                        }
                    });
                });
            });
        });
    });

    function previewSiteLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('site-logo-preview');
                var fallback = document.getElementById('site-logo-fallback');
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (fallback) {
                    fallback.classList.add('hidden');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewShareImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('share-img-preview');
                if (preview) preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</div>
