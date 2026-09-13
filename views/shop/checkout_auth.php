<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
?>

<div class="bg-gray-50 py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Checkout</h1>
            <p class="text-gray-500">Please login or create an account to proceed with your order.</p>
        </div>

        <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 flex items-center gap-3">
            <ion-icon name="alert-circle-outline" class="text-xl flex-shrink-0"></ion-icon>
            <span class="font-medium">
                <?php 
                    if ($error === 'missing_fields') echo 'Please fill in all required fields.';
                    elseif ($error === 'invalid_credentials') echo 'Invalid phone number or password.';
                    elseif ($error === 'phone_exists') echo 'An account with this phone number already exists. Please login.';
                    else echo htmlspecialchars($error);
                ?>
            </span>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row">
            <!-- Left Side / Tabs -->
            <div class="w-full md:w-1/3 bg-gray-50 border-r border-gray-100 p-6 flex flex-col justify-center gap-4">
                <button onclick="switchTab('login')" id="tab-login" class="w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-green-500 bg-green-50 text-green-700">
                    <div class="flex items-center gap-3">
                        <ion-icon name="log-in-outline" class="text-2xl"></ion-icon>
                        Login
                    </div>
                </button>
                <button onclick="switchTab('signup')" id="tab-signup" class="w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-transparent text-gray-600 hover:bg-gray-100">
                    <div class="flex items-center gap-3">
                        <ion-icon name="person-add-outline" class="text-2xl"></ion-icon>
                        Create Account
                    </div>
                </button>
            </div>

            <!-- Right Side / Forms -->
            <div class="w-full md:w-2/3 p-6 md:p-10">
                
                <!-- Login Form -->
                <div id="form-login" class="block">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome Back!</h2>
                    <form action="/sodai-dorkar/public/checkout/login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                        <input type="hidden" name="redirect" value="/sodai-dorkar/public/checkout">
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="e.g. 01700000000">
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1') echo 'or 4-Digit Support PIN'; ?></label>
                            <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="••••••••">
                            
                            <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1'): ?>
                            <div class="mt-2 text-right">
                                <span class="text-xs text-gray-500">Forgot password? </span>
                                <a href="tel:01609448066" class="text-xs font-bold text-green-600 hover:underline">Contact Support for a PIN</a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-green-700 transition-colors shadow-sm mb-4">
                            Login & Continue
                        </button>

                        <?php if (($settings['auth_firebase_otp_enabled'] ?? '1') == '1'): ?>
                        <div class="relative flex py-2 items-center mb-4">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">OR</span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>
                        
                        <button type="button" onclick="startFirebaseOtp()" class="w-full bg-white text-gray-700 border border-gray-300 font-bold py-3.5 px-6 rounded-xl hover:bg-gray-50 transition-colors shadow-sm flex justify-center items-center gap-2">
                            <ion-icon name="phone-portrait-outline" class="text-xl text-blue-500"></ion-icon>
                            Login with SMS OTP
                        </button>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Firebase OTP Hidden Form (Mock implementation) -->
                    <div id="otp-section" class="hidden mt-6 p-4 border border-blue-100 bg-blue-50 rounded-xl">
                        <h3 class="font-bold text-blue-800 mb-2 text-sm">Verify Phone Number</h3>
                        <p class="text-xs text-blue-600 mb-3">A 6-digit code has been sent to your phone. (Demo: Enter 123456)</p>
                        <form action="/sodai-dorkar/public/checkout/firebase-login" method="POST" class="flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="redirect" value="/sodai-dorkar/public/checkout">
                            <input type="hidden" name="phone" id="otp-phone">
                            <input type="text" name="otp" required placeholder="000000" class="flex-grow px-3 py-2 rounded border border-blue-200 focus:ring-2 focus:ring-blue-500 outline-none text-center tracking-widest font-mono">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700 text-sm">Verify</button>
                        </form>
                    </div>
                </div>

                <!-- Signup Form -->
                <div id="form-signup" class="hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Account</h2>
                    <form action="/sodai-dorkar/public/checkout/signup" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                        <input type="hidden" name="redirect" value="/sodai-dorkar/public/checkout">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" name="email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Area</label>
                                <select id="area_id" name="area_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                    <option value="" disabled selected>Select Area</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Zone</label>
                                <select id="zone_id" name="zone_id" required disabled class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-gray-50">
                                    <option value="" disabled selected>Select Area First</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Point</label>
                                <select id="point_id" name="point_id" required disabled class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-gray-50">
                                    <option value="" disabled selected>Select Zone First</option>
                                </select>
                            </div>
                        </div>

                        <div id="address_details_wrapper" class="hidden mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Detailed Address (For 'Other' Point)</label>
                            <textarea id="address" name="address" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="House/Road details..."></textarea>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Set Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        </div>

                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                            Create Account & Continue
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const loginForm = document.getElementById('form-login');
    const signupForm = document.getElementById('form-signup');
    const loginTab = document.getElementById('tab-login');
    const signupTab = document.getElementById('tab-signup');

    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        signupForm.classList.add('hidden');
        
        loginTab.className = "w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-green-500 bg-green-50 text-green-700";
        signupTab.className = "w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-transparent text-gray-600 hover:bg-gray-100";
    } else {
        loginForm.classList.add('hidden');
        signupForm.classList.remove('hidden');

        signupTab.className = "w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-green-500 bg-green-50 text-green-700";
        loginTab.className = "w-full text-left px-6 py-4 rounded-xl font-bold text-lg transition-all border-2 border-transparent text-gray-600 hover:bg-gray-100";
    }
}

// Check for signup parameter in URL to auto-open signup tab
if (window.location.search.includes('tab=signup')) {
    switchTab('signup');
}

// Cascading Dropdowns
document.getElementById('area_id').addEventListener('change', function() {
    const areaId = this.value;
    const zoneSelect = document.getElementById('zone_id');
    const pointSelect = document.getElementById('point_id');
    
    zoneSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
    zoneSelect.disabled = true;
    pointSelect.innerHTML = '<option value="" disabled selected>Select Zone First</option>';
    pointSelect.disabled = true;

    fetch(`/sodai-dorkar/public/api/zones?area_id=${areaId}`)
        .then(res => res.json())
        .then(data => {
            zoneSelect.innerHTML = '<option value="" disabled selected>Select Zone</option>';
            data.forEach(zone => {
                zoneSelect.innerHTML += `<option value="${zone.id}">${zone.name}</option>`;
            });
            zoneSelect.disabled = false;
            zoneSelect.classList.remove('bg-gray-50');
            zoneSelect.classList.add('bg-white');
        });
});

document.getElementById('zone_id').addEventListener('change', function() {
    const zoneId = this.value;
    const pointSelect = document.getElementById('point_id');
    
    pointSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
    pointSelect.disabled = true;

    fetch(`/sodai-dorkar/public/api/points?zone_id=${zoneId}`)
        .then(res => res.json())
        .then(data => {
            pointSelect.innerHTML = '<option value="" disabled selected>Select Point</option>';
            data.forEach(point => {
                pointSelect.innerHTML += `<option value="${point.id}">${point.name}</option>`;
            });
            pointSelect.innerHTML += `<option value="other" class="font-bold text-primary-600">Other (আর্দাস)</option>`;
            pointSelect.disabled = false;
            pointSelect.classList.remove('bg-gray-50');
            pointSelect.classList.add('bg-white');
        });
});

document.getElementById('point_id').addEventListener('change', function() {
    const wrapper = document.getElementById('address_details_wrapper');
    const addressInput = document.getElementById('address');
    
    if (this.value === 'other') {
        wrapper.classList.remove('hidden');
        addressInput.required = true;
    } else {
        wrapper.classList.add('hidden');
        addressInput.required = false;
    }
});

function startFirebaseOtp() {
    const phoneInput = document.querySelector('input[name="phone"]');
    if (!phoneInput.value || phoneInput.value.length < 11) {
        alert("Please enter a valid phone number in the login form first.");
        return;
    }
    
    // In a real scenario, you would initialize Firebase here:
    /*
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    window.recaptchaVerifier = new RecaptchaVerifier('recaptcha-container', {}, auth);
    signInWithPhoneNumber(auth, phoneInput.value, window.recaptchaVerifier)
        .then((confirmationResult) => { window.confirmationResult = confirmationResult; });
    */
    
    // Dummy UI flow
    document.getElementById('otp-phone').value = phoneInput.value;
    document.getElementById('otp-section').classList.remove('hidden');
}
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
