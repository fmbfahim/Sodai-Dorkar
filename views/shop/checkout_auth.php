<?php
ob_start();
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$base     = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$siteName = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
$activeTab  = $tab ?? ($_GET['tab'] ?? 'login');
$otpEnabled = ($settings['otp_required_signup'] ?? '0') === '1';
$fbEnabled  = ($settings['auth_firebase_otp_enabled'] ?? '0') === '1';
$preFillPhone = htmlspecialchars($_GET['phone'] ?? '');
?>

<style>
/* ── Auth Page Premium Styles ───────────────────────────────────── */
.auth-wrap {
  min-height: calc(100vh - 160px);
  display: flex; align-items: center; justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #ecfdf5 100%);
}

.auth-card {
  width: 100%; max-width: 960px;
  background: #fff;
  border-radius: 28px;
  box-shadow: 0 24px 80px rgba(5,150,105,.10), 0 4px 16px rgba(0,0,0,.06);
  border: 1px solid rgba(16,185,129,.12);
  overflow: hidden;
  display: grid;
  grid-template-columns: 260px 1fr;
}
@media (max-width:768px) {
  .auth-card { grid-template-columns: 1fr; }
}

/* Sidebar */
.auth-sidebar {
  background: linear-gradient(160deg, #064e3b 0%, #065f46 50%, #047857 100%);
  padding: 2.5rem 1.5rem;
  display: flex; flex-direction: column; justify-content: space-between;
  position: relative; overflow: hidden;
}
.auth-sidebar::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 200px; height: 200px;
  background: rgba(255,255,255,.05); border-radius: 50%;
}
.auth-sidebar::after {
  content: '';
  position: absolute; bottom: -80px; left: -40px;
  width: 260px; height: 260px;
  background: rgba(255,255,255,.04); border-radius: 50%;
}

.auth-tab-btn {
  display: flex; align-items: center; gap: 12px;
  width: 100%; padding: 14px 18px;
  border-radius: 14px; font-weight: 700; font-size: .92rem;
  transition: all .25s; cursor: pointer; border: none;
  background: transparent; color: rgba(255,255,255,.6);
  text-align: left; position: relative; z-index: 1;
}
.auth-tab-btn:hover { background: rgba(255,255,255,.1); color: #fff; }
.auth-tab-btn.active {
  background: rgba(255,255,255,.18); color: #fff;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.auth-tab-btn .tab-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,.15); font-size: 1.25rem; flex-shrink: 0;
}
.auth-tab-btn.active .tab-icon { background: #10b981; }

/* Form Panel */
.auth-panel { padding: 2.5rem 2.5rem; }
@media (max-width:640px) { .auth-panel { padding: 1.5rem; } }

.form-section { display: none; }
.form-section.active { display: block; animation: slideIn .3s ease; }
@keyframes slideIn { from{opacity:0;transform:translateX(12px)} to{opacity:1;transform:translateX(0)} }

.auth-input {
  width: 100%; padding: 11px 16px;
  border: 2px solid #e5e7eb; border-radius: 12px;
  font-size: .9rem; font-family: inherit;
  transition: all .2s; background: #fafafa; color: #111827;
  outline: none;
}
.auth-input:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.auth-input::placeholder { color: #9ca3af; }

.auth-input-icon-wrap { position: relative; }
.auth-input-icon-wrap ion-icon {
  position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
  color: #9ca3af; font-size: 1rem; pointer-events: none;
}
.auth-input-icon-wrap .auth-input { padding-left: 40px; }

.auth-btn-primary {
  width: 100%; padding: 14px 20px;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff; font-weight: 800; font-size: .95rem;
  border: none; border-radius: 14px; cursor: pointer;
  transition: all .25s; letter-spacing: .01em;
  box-shadow: 0 6px 20px rgba(5,150,105,.3);
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.auth-btn-primary:hover {
  background: linear-gradient(135deg, #047857, #059669);
  transform: translateY(-1px); box-shadow: 0 10px 28px rgba(5,150,105,.35);
}
.auth-btn-primary:active { transform: translateY(0); }
.auth-btn-primary:disabled { opacity: .6; cursor: not-allowed; transform: none !important; }

.auth-btn-firebase {
  width: 100%; padding: 13px 20px;
  background: #fff;
  border: 2px solid #e5e7eb; border-radius: 14px;
  color: #374151; font-weight: 700; font-size: .9rem;
  cursor: pointer; transition: all .25s;
  display: flex; align-items: center; justify-content: center; gap: 10px;
}
.auth-btn-firebase:hover { border-color: #f59e0b; color: #b45309; background: #fffbeb; }

.form-label {
  display: block; font-size: .78rem; font-weight: 700;
  color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em;
}

.divider {
  display: flex; align-items: center; gap: 12px; margin: 16px 0;
  color: #9ca3af; font-size: .78rem;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

.error-box {
  background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 12px; padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  font-size: .83rem; font-weight: 600; color: #dc2626; margin-bottom: 18px;
}

.select-styled {
  width: 100%; padding: 11px 16px;
  border: 2px solid #e5e7eb; border-radius: 12px;
  font-size: .9rem; font-family: inherit;
  background: #fafafa; color: #111827;
  outline: none; transition: all .2s; appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%239ca3af' viewBox='0 0 20 20'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center; background-size: 18px;
  padding-right: 40px;
}
.select-styled:focus { border-color: #10b981; background-color: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.select-styled:disabled { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

.otp-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: #d1fae5; color: #065f46; font-size: .72rem; font-weight: 700;
  padding: 3px 10px; border-radius: 999px; border: 1px solid #a7f3d0;
}

/* ── Firebase OTP Modal ─────────────────────────────── */
#fb-otp-modal {
  display: none;
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
  align-items: center; justify-content: center; padding: 1rem;
}
#fb-otp-modal.open { display: flex; }
.fb-modal-card {
  background: #fff; border-radius: 24px;
  box-shadow: 0 32px 80px rgba(0,0,0,.22);
  padding: 2.5rem 2rem; width: 100%; max-width: 400px;
  text-align: center; position: relative;
}
.fb-otp-digits {
  display: flex; gap: 10px; justify-content: center; margin: 1.5rem 0;
}
.fb-otp-digit {
  width: 50px; height: 58px; border: 2px solid #d1fae5;
  border-radius: 14px; font-size: 1.6rem; font-weight: 700;
  text-align: center; background: #f0fdf4; color: #064e3b;
  outline: none; transition: all .2s;
}
.fb-otp-digit:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
</style>

<!-- ───────────────────────────────────── -->
<!--  Firebase OTP Modal (shared)         -->
<!-- ───────────────────────────────────── -->
<div id="fb-otp-modal" role="dialog" aria-modal="true" aria-label="Phone Verification">
  <div class="fb-modal-card">
    <div style="font-size:3rem;margin-bottom:.5rem;">📱</div>
    <h2 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.35rem;">Verify Your Phone</h2>
    <p style="font-size:.82rem;color:#6b7280;margin-bottom:.25rem;">A 6-digit code was sent to</p>
    <p id="fb-modal-phone-display" style="font-weight:800;color:#059669;font-size:.95rem;margin-bottom:1rem;"></p>

    <!-- OTP digit boxes -->
    <div class="fb-otp-digits" id="fb-otp-digits">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="1">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="2">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="3">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="4">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="5">
      <input class="fb-otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="6">
    </div>

    <div id="fb-otp-error" style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:.82rem;font-weight:600;color:#dc2626;margin-bottom:1rem;"></div>
    <div id="fb-otp-loading" style="display:none;font-size:.82rem;color:#6b7280;margin-bottom:.75rem;">⏳ Verifying…</div>

    <button id="fb-verify-btn" onclick="fbConfirmOtp()"
            style="width:100%;padding:13px;background:linear-gradient(135deg,#059669,#10b981);color:#fff;font-weight:800;border:none;border-radius:14px;cursor:pointer;font-size:.95rem;margin-bottom:.75rem;">
      ✅ Verify & Continue
    </button>
    <button onclick="fbCloseModal()"
            style="width:100%;padding:10px;background:transparent;border:1.5px solid #e5e7eb;border-radius:12px;font-size:.83rem;color:#6b7280;cursor:pointer;font-weight:600;">
      ✕ Cancel
    </button>

    <!-- reCAPTCHA container (invisible) -->
    <div id="recaptcha-container" style="margin-top:.75rem;"></div>
  </div>
</div>

<div class="auth-wrap">
  <div class="w-full max-w-5xl px-2">

    <?php if (!empty($error)): ?>
    <?php $errorMap = [
      'missing_fields'       => 'সব প্রয়োজনীয় তথ্য পূরণ করুন।',
      'invalid_credentials'  => 'ফোন নম্বর বা পাসওয়ার্ড ভুল।',
      'phone_exists'         => 'এই ফোন নম্বর দিয়ে আগেই অ্যাকাউন্ট আছে। লগইন করুন।',
      'create_account_first' => 'নতুন অ্যাকাউন্ট তৈরি করতে নিচের ফর্ম ব্যবহার করুন।',
      'firebase_failed'      => 'OTP যাচাই ব্যর্থ হয়েছে। আবার চেষ্টা করুন।',
    ]; ?>
    <div class="error-box mb-4 max-w-5xl mx-auto">
      <ion-icon name="alert-circle" style="font-size:1.2rem;flex-shrink:0"></ion-icon>
      <span><?= $errorMap[$error] ?? htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="auth-card">

      <!-- ── Sidebar ─────────────────────────────── -->
      <div class="auth-sidebar">
        <div>
          <!-- Logo -->
          <div class="mb-8" style="position:relative;z-index:1">
            <div style="font-size:1.5rem;font-weight:900;color:#fff;letter-spacing:-.02em;">
              🛒 <?= htmlspecialchars($siteName) ?>
            </div>
            <div style="font-size:.78rem;color:rgba(255,255,255,.55);margin-top:4px;">
              Fresh Groceries Delivered
            </div>
          </div>

          <!-- Tab Buttons -->
          <div class="space-y-2.5" style="position:relative;z-index:1">
            <button onclick="switchTab('login')" id="tab-login"
                    class="auth-tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>">
              <span class="tab-icon"><ion-icon name="log-in-outline"></ion-icon></span>
              <div>
                <div>Login</div>
                <div style="font-size:.72rem;font-weight:500;opacity:.7">Existing account</div>
              </div>
            </button>

            <button onclick="switchTab('signup')" id="tab-signup"
                    class="auth-tab-btn <?= $activeTab === 'signup' ? 'active' : '' ?>">
              <span class="tab-icon"><ion-icon name="person-add-outline"></ion-icon></span>
              <div>
                <div>Create Account</div>
                <div style="font-size:.72rem;font-weight:500;opacity:.7">
                  New customer
                  <?php if ($fbEnabled): ?>
                  &nbsp;<span class="otp-badge">🔥 OTP</span>
                  <?php endif; ?>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- Security note -->
        <div style="position:relative;z-index:1;margin-top:2rem;">
          <div style="display:flex;align-items:center;gap:8px;font-size:.75rem;color:rgba(255,255,255,.45);">
            <ion-icon name="shield-checkmark-outline" style="font-size:1rem;"></ion-icon>
            SSL secured · Your data is safe
          </div>
          <?php if ($fbEnabled): ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:.72rem;color:rgba(255,255,255,.35);margin-top:6px;">
            <ion-icon name="flame-outline" style="font-size:.9rem;"></ion-icon>
            Powered by Firebase Auth
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Form Panel ─────────────────────────── -->
      <div class="auth-panel">

        <!-- ╔══ LOGIN FORM ══╗ -->
        <div class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>" id="form-login">
          <h2 style="font-size:1.5rem;font-weight:900;color:#111827;margin-bottom:.4rem;">Welcome Back! 👋</h2>
          <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.75rem;">Enter your details to continue shopping.</p>

          <form action="<?= $base ?>/checkout/login" method="POST" id="normal-login-form">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= $base ?>/checkout">

            <div style="margin-bottom:16px;">
              <label class="form-label">Phone Number</label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="call-outline"></ion-icon>
                <input type="tel" name="phone" id="login-phone" class="auth-input"
                       placeholder="01700000000" required autocomplete="tel"
                       value="<?= $preFillPhone ?>">
              </div>
            </div>

            <div style="margin-bottom:22px;">
              <label class="form-label">
                Password
                <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1'): ?>
                <span style="text-transform:none;font-weight:500;color:#6b7280;letter-spacing:0"> or Support PIN</span>
                <?php endif; ?>
              </label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" id="loginPass" class="auth-input" placeholder="••••••••" required autocomplete="current-password">
              </div>
              <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1'): ?>
              <div style="text-align:right;margin-top:6px;">
                <a href="tel:01609448066" style="font-size:.78rem;color:#059669;font-weight:700;text-decoration:none;">
                  Forgot password? Contact support →
                </a>
              </div>
              <?php endif; ?>
            </div>

            <button type="submit" class="auth-btn-primary">
              <ion-icon name="log-in-outline" style="font-size:1.1rem;"></ion-icon>
              Login &amp; Continue
            </button>
          </form>

          <!-- Firebase OTP Login -->
          <?php if ($fbEnabled): ?>
          <div class="divider">OR</div>
          <!-- Hidden form submitted after Firebase verify -->
          <form id="fb-login-form" action="<?= $base ?>/checkout/firebase-verify-login" method="POST" style="display:none;">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="firebase_token" id="fb-login-token">
            <input type="hidden" name="redirect" value="<?= $base ?>/checkout">
          </form>
          <button type="button" class="auth-btn-firebase" onclick="fbStartLoginOtp()">
            🔥 Login with OTP (Free SMS)
          </button>
          <?php endif; ?>
        </div>

        <!-- ╔══ SIGNUP FORM ══╗ -->
        <div class="form-section <?= $activeTab === 'signup' ? 'active' : '' ?>" id="form-signup">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:.4rem;">
            <h2 style="font-size:1.5rem;font-weight:900;color:#111827;">Create Account</h2>
            <?php if ($fbEnabled): ?>
            <span class="otp-badge">🔥 OTP Required</span>
            <?php endif; ?>
          </div>
          <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.75rem;">
            <?= $fbEnabled
              ? 'Fill in your details. Your phone will be verified with a free OTP before account creation.'
              : 'Join ' . htmlspecialchars($siteName) . ' and get fresh groceries delivered to your door.' ?>
          </p>

          <!-- Hidden form for Firebase signup: submitted programmatically after OTP verify -->
          <?php if ($fbEnabled): ?>
          <form id="fb-signup-form" action="<?= $base ?>/checkout/firebase-verify-signup" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="firebase_token" id="fb-signup-token">
            <input type="hidden" name="name"     id="fb-signup-name">
            <input type="hidden" name="email"    id="fb-signup-email">
            <input type="hidden" name="password" id="fb-signup-password">
            <input type="hidden" name="area_id"  id="fb-signup-area">
            <input type="hidden" name="zone_id"  id="fb-signup-zone">
            <input type="hidden" name="point_id" id="fb-signup-point">
            <input type="hidden" name="address"  id="fb-signup-address">
          </form>
          <?php endif; ?>

          <!-- Normal signup form (always shown as input UI) -->
          <form action="<?= $base ?>/checkout/signup" method="POST" id="signup-form-ui">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= $base ?>/checkout">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
              <div>
                <label class="form-label">Full Name *</label>
                <div class="auth-input-icon-wrap">
                  <ion-icon name="person-outline"></ion-icon>
                  <input type="text" name="name" id="su-name" class="auth-input" placeholder="Your name" required autocomplete="name">
                </div>
              </div>
              <div>
                <label class="form-label">Phone Number *</label>
                <div class="auth-input-icon-wrap">
                  <ion-icon name="call-outline"></ion-icon>
                  <input type="tel" name="phone" id="su-phone" class="auth-input"
                         placeholder="01700000000" required autocomplete="tel"
                         value="<?= $preFillPhone ?>">
                </div>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <label class="form-label">Email <span style="font-weight:400;text-transform:none;color:#9ca3af;">(Optional)</span></label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="email" name="email" id="su-email" class="auth-input" placeholder="you@example.com" autocomplete="email">
              </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:14px;">
              <div>
                <label class="form-label">Area *</label>
                <select id="area_id" name="area_id" class="select-styled" required>
                  <option value="" disabled selected>Select</option>
                  <?php foreach ($areas as $area): ?>
                  <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="form-label">Zone *</label>
                <select id="zone_id" name="zone_id" class="select-styled" required disabled>
                  <option value="" disabled selected>Area first</option>
                </select>
              </div>
              <div>
                <label class="form-label">Point *</label>
                <select id="point_id" name="point_id" class="select-styled" required disabled>
                  <option value="" disabled selected>Zone first</option>
                </select>
              </div>
            </div>

            <div id="address_details_wrapper" style="display:none;margin-bottom:14px;">
              <label class="form-label">Address Details</label>
              <textarea id="address" name="address" rows="2" class="auth-input" style="resize:vertical;"
                        placeholder="House/Road details..."></textarea>
            </div>

            <div style="margin-bottom:22px;">
              <label class="form-label">Set Password *</label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" id="su-password" class="auth-input"
                       placeholder="Min. 6 characters" required minlength="6" autocomplete="new-password">
              </div>
            </div>

            <?php if ($fbEnabled): ?>
            <!-- Firebase OTP button intercepts the form -->
            <button type="button" id="fb-signup-btn" class="auth-btn-primary" onclick="fbStartSignupOtp()">
              <ion-icon name="shield-checkmark-outline" style="font-size:1.1rem;"></ion-icon>
              Verify Phone &amp; Create Account
            </button>
            <?php else: ?>
            <button type="submit" class="auth-btn-primary">
              <?php if ($otpEnabled): ?>
              <ion-icon name="shield-checkmark-outline" style="font-size:1.1rem;"></ion-icon>
              Continue to OTP Verification
              <?php else: ?>
              <ion-icon name="person-add-outline" style="font-size:1.1rem;"></ion-icon>
              Create Account &amp; Continue
              <?php endif; ?>
            </button>
            <?php endif; ?>
          </form>
        </div>

      </div><!-- /auth-panel -->
    </div><!-- /auth-card -->
  </div>
</div>

<!-- ─────────────────────────────────────── -->
<!--  Firebase SDK + Auth Logic             -->
<!-- ─────────────────────────────────────── -->
<?php if ($fbEnabled): ?>
<script type="module">
  import { initializeApp }                     from 'https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js';
  import { getAuth, RecaptchaVerifier,
           signInWithPhoneNumber }             from 'https://www.gstatic.com/firebasejs/10.13.0/firebase-auth.js';

  const firebaseConfig = {
    apiKey:            "AIzaSyCqRe6RdZ6XPtr06L6lCbDs5_uf0ryMMDc",
    authDomain:        "sodai-dorkar01.firebaseapp.com",
    projectId:         "sodai-dorkar01",
    storageBucket:     "sodai-dorkar01.firebasestorage.app",
    messagingSenderId: "290621496533",
    appId:             "1:290621496533:web:ed121bf764be513a8d565c",
  };

  const app  = initializeApp(firebaseConfig);
  const auth = getAuth(app);

  // ── State ────────────────────────────────────
  let confirmationResult = null;
  let currentMode        = null; // 'login' | 'signup'
  let recaptchaVerifier  = null;

  // Expose globally for non-module buttons
  window._fbAuth = { start, confirmOtp, closeModal };

  // ── reCAPTCHA setup ───────────────────────────
  function initRecaptcha() {
    if (recaptchaVerifier) return;
    recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', {
      size: 'invisible',
      callback: () => {},
    });
  }

  // ── Send OTP ──────────────────────────────────
  async function start(phone, mode) {
    currentMode = mode;
    initRecaptcha();

    // Format phone → +880...
    let formatted = phone.replace(/\D/g, '');
    if (formatted.startsWith('0') && formatted.length === 11) {
      formatted = '+88' + formatted;
    } else if (!formatted.startsWith('+')) {
      formatted = '+' + formatted;
    }

    try {
      showModalLoading(formatted);
      confirmationResult = await signInWithPhoneNumber(auth, formatted, recaptchaVerifier);
      showModalOtp(formatted);
    } catch (err) {
      recaptchaVerifier.clear();
      recaptchaVerifier = null;
      showError(friendlyError(err));
    }
  }

  // ── Confirm OTP ───────────────────────────────
  async function confirmOtp(code) {
    if (!confirmationResult) { showError('Please request OTP first.'); return; }
    setLoading(true);
    try {
      const result    = await confirmationResult.confirm(code);
      const idToken   = await result.user.getIdToken();

      if (currentMode === 'signup') {
        populateAndSubmitSignup(idToken);
      } else {
        populateAndSubmitLogin(idToken);
      }
    } catch (err) {
      setLoading(false);
      showError(friendlyError(err));
    }
  }

  function populateAndSubmitSignup(idToken) {
    document.getElementById('fb-signup-token').value   = idToken;
    document.getElementById('fb-signup-name').value    = document.getElementById('su-name').value;
    document.getElementById('fb-signup-email').value   = document.getElementById('su-email').value;
    document.getElementById('fb-signup-password').value= document.getElementById('su-password').value;
    document.getElementById('fb-signup-area').value    = document.getElementById('area_id').value;
    document.getElementById('fb-signup-zone').value    = document.getElementById('zone_id').value;
    document.getElementById('fb-signup-point').value   = document.getElementById('point_id').value;
    document.getElementById('fb-signup-address').value = (document.getElementById('address') || {}).value || '';
    document.getElementById('fb-signup-form').submit();
  }

  function populateAndSubmitLogin(idToken) {
    document.getElementById('fb-login-token').value = idToken;
    document.getElementById('fb-login-form').submit();
  }

  // ── Modal helpers ─────────────────────────────
  function showModalLoading(phone) {
    document.getElementById('fb-modal-phone-display').textContent = phone;
    document.getElementById('fb-otp-error').style.display  = 'none';
    document.getElementById('fb-otp-loading').style.display = 'block';
    document.getElementById('fb-otp-loading').textContent   = '📤 Sending OTP…';
    document.getElementById('fb-verify-btn').disabled        = true;
    openModal();
  }

  function showModalOtp(phone) {
    document.getElementById('fb-otp-loading').style.display = 'none';
    document.getElementById('fb-verify-btn').disabled        = false;
    // focus first digit
    const firstDigit = document.querySelector('.fb-otp-digit');
    if (firstDigit) setTimeout(() => firstDigit.focus(), 100);
  }

  function openModal()  { document.getElementById('fb-otp-modal').classList.add('open'); }
  function closeModal() { document.getElementById('fb-otp-modal').classList.remove('open'); }

  function showError(msg) {
    const el = document.getElementById('fb-otp-error');
    el.textContent    = msg;
    el.style.display  = 'block';
  }

  function setLoading(on) {
    document.getElementById('fb-otp-loading').style.display = on ? 'block' : 'none';
    document.getElementById('fb-otp-loading').textContent   = '⏳ Verifying…';
    document.getElementById('fb-verify-btn').disabled       = on;
  }

  function friendlyError(err) {
    const map = {
      'auth/invalid-phone-number'  : 'Invalid phone number. Use format: 01700000000',
      'auth/too-many-requests'     : 'Too many attempts. Please wait a minute and try again.',
      'auth/invalid-verification-code': 'Wrong OTP code. Please check and try again.',
      'auth/code-expired'          : 'OTP expired. Please request a new one.',
      'auth/missing-phone-number'  : 'Please enter your phone number.',
    };
    return map[err.code] || err.message || 'Something went wrong. Please try again.';
  }

  // Expose to window for onclick handlers
  window.fbStartSignupOtp = function() {
    const form = document.getElementById('signup-form-ui');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    const phone = document.getElementById('su-phone').value.trim();
    window._fbAuth.start(phone, 'signup');
  };

  window.fbStartLoginOtp = function() {
    const phone = document.getElementById('login-phone').value.trim();
    if (!phone || phone.length < 11) {
      alert('আপনার ১১ সংখ্যার ফোন নম্বর লিখুন।');
      return;
    }
    window._fbAuth.start(phone, 'login');
  };

  window.fbConfirmOtp = function() {
    const digits = Array.from(document.querySelectorAll('.fb-otp-digit'));
    const code   = digits.map(d => d.value).join('');
    if (code.length < 6) {
      showError('Please enter all 6 digits.');
      return;
    }
    window._fbAuth.confirmOtp(code);
  };

  window.fbCloseModal = function() { window._fbAuth.closeModal(); };
</script>
<?php endif; ?>

<script>
// ── Tab switching ────────────────────────────────────────
function switchTab(tab) {
  ['login','signup'].forEach(t => {
    document.getElementById('form-' + t).classList.remove('active');
    document.getElementById('tab-' + t).classList.remove('active');
  });
  document.getElementById('form-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}

if (window.location.search.includes('tab=signup')) switchTab('signup');

// ── OTP digit navigation in modal ───────────────────────
document.addEventListener('DOMContentLoaded', function() {
  const digits = Array.from(document.querySelectorAll('.fb-otp-digit'));
  digits.forEach((input, i) => {
    input.addEventListener('input', e => {
      const v = e.data || input.value;
      if (!/^\d$/.test(v)) { input.value = ''; return; }
      input.value = v;
      if (i < digits.length - 1) digits[i + 1].focus();
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace') {
        input.value = '';
        if (i > 0) digits[i - 1].focus();
      }
    });
    input.addEventListener('paste', e => {
      e.preventDefault();
      const txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
      txt.split('').forEach((c, j) => { if (digits[j]) digits[j].value = c; });
      const next = Math.min(txt.length, digits.length - 1);
      digits[next].focus();
    });
  });

  // Close modal on backdrop click
  document.getElementById('fb-otp-modal').addEventListener('click', function(e) {
    if (e.target === this) window.fbCloseModal && window.fbCloseModal();
  });
});

// ── Cascading Dropdowns ─────────────────────────────────
document.getElementById('area_id').addEventListener('change', function() {
  const areaId = this.value;
  const zSel   = document.getElementById('zone_id');
  const pSel   = document.getElementById('point_id');
  zSel.innerHTML = '<option value="" disabled selected>Loading...</option>';
  zSel.disabled  = true;
  pSel.innerHTML = '<option value="" disabled selected>Zone first</option>';
  pSel.disabled  = true;

  fetch(`${window.APP_BASE || ''}/api/zones?area_id=${areaId}`)
    .then(r => r.json())
    .then(data => {
      zSel.innerHTML = '<option value="" disabled selected>Select Zone</option>';
      data.forEach(z => zSel.innerHTML += `<option value="${z.id}">${z.name}</option>`);
      zSel.disabled = false;
    });
});

document.getElementById('zone_id').addEventListener('change', function() {
  const zoneId = this.value;
  const pSel   = document.getElementById('point_id');
  pSel.innerHTML = '<option value="" disabled selected>Loading...</option>';
  pSel.disabled  = true;

  fetch(`${window.APP_BASE || ''}/api/points?zone_id=${zoneId}`)
    .then(r => r.json())
    .then(data => {
      pSel.innerHTML = '<option value="" disabled selected>Select Point</option>';
      data.forEach(p => pSel.innerHTML += `<option value="${p.id}">${p.name}</option>`);
      pSel.innerHTML += `<option value="other" style="font-weight:700;color:#059669;">Other (আর্দাস)</option>`;
      pSel.disabled = false;
    });
});

document.getElementById('point_id').addEventListener('change', function() {
  const wrapper = document.getElementById('address_details_wrapper');
  const addr    = document.getElementById('address');
  if (this.value === 'other') {
    wrapper.style.display = 'block';
    addr.required = true;
  } else {
    wrapper.style.display = 'none';
    addr.required = false;
  }
});
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
