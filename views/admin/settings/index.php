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

    <form action="/sodai-dorkar/public/admin/settings/update" method="POST">
        
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
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Firebase OTP SMS Login</label>
                    <select name="auth_firebase_otp_enabled" class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                        <option value="1" <?php echo ($settings['auth_firebase_otp_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo ($settings['auth_firebase_otp_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                    <p class="text-xs text-secondary-400 mt-1">Allows customers to login or verify their identity using a Firebase SMS code.</p>
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
                     <h3 class="font-bold text-secondary-700">General Information</h3>
                     <p class="text-xs text-secondary-500">Basic application details.</p>
                 </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Site Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Application Name</label>
                    <input type="text" name="site_title" 
                           value="<?php echo htmlspecialchars($settings['site_title'] ?? 'Sodai Dorkar'); ?>" 
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" 
                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-bold text-secondary-700 mb-2">Office Address</label>
                    <input type="text" name="contact_address" 
                           value="<?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?>" 
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
    </script>
</div>
