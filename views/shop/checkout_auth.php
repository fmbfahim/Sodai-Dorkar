<?php
ob_start();
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$base     = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$siteName = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
$activeTab = $tab ?? ($_GET['tab'] ?? 'login');
$otpEnabled = ($settings['otp_required_signup'] ?? '0') === '1';
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
</style>

<div class="auth-wrap">
  <div class="w-full max-w-5xl px-2">

    <?php if (!empty($error)): ?>
    <?php $errorMap = [
      'missing_fields'       => 'সব প্রয়োজনীয় তথ্য পূরণ করুন।',
      'invalid_credentials'  => 'ফোন নম্বর বা পাসওয়ার্ড ভুল।',
      'phone_exists'         => 'এই ফোন নম্বর দিয়ে আগেই অ্যাকাউন্ট আছে। লগইন করুন।',
      'create_account_first' => 'নতুন অ্যাকাউন্ট তৈরি করতে নিচের ফর্ম ব্যবহার করুন।',
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
                  <?php if ($otpEnabled): ?>
                  &nbsp;<span class="otp-badge">🔐 OTP</span>
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
        </div>
      </div>

      <!-- ── Form Panel ─────────────────────────── -->
      <div class="auth-panel">

        <!-- ╔══ LOGIN FORM ══╗ -->
        <div class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>" id="form-login">
          <h2 style="font-size:1.5rem;font-weight:900;color:#111827;margin-bottom:.4rem;">Welcome Back! 👋</h2>
          <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.75rem;">Enter your details to continue shopping.</p>

          <form action="<?= $base ?>/checkout/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= $base ?>/checkout">

            <div style="margin-bottom:16px;">
              <label class="form-label">Phone Number</label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="call-outline"></ion-icon>
                <input type="tel" name="phone" class="auth-input" placeholder="01700000000" required autocomplete="tel">
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

            <?php if (($settings['auth_firebase_otp_enabled'] ?? '0') == '1'): ?>
            <div class="divider">OR</div>
            <button type="button" onclick="startOtpLogin()"
                    style="width:100%;padding:12px 20px;border:2px solid #e5e7eb;border-radius:14px;background:#fff;font-weight:700;font-size:.88rem;color:#374151;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;"
                    onmouseover="this.style.borderColor='#10b981';this.style.color='#059669'"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
              <ion-icon name="phone-portrait-outline" style="color:#3b82f6;font-size:1.1rem;"></ion-icon>
              Login with SMS OTP
            </button>
            <?php endif; ?>
          </form>

          <!-- OTP login section (hidden by default) -->
          <div id="otp-login-section" style="display:none;margin-top:18px;">
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:16px;">
              <h4 style="font-size:.85rem;font-weight:700;color:#1d4ed8;margin-bottom:6px;">📱 Verify Phone Number</h4>
              <p style="font-size:.78rem;color:#3b82f6;margin-bottom:12px;">Enter your OTP below (Demo: 123456)</p>
              <form action="<?= $base ?>/checkout/firebase-login" method="POST" style="display:flex;gap:8px;">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="redirect" value="<?= $base ?>/checkout">
                <input type="hidden" name="phone" id="otp-login-phone">
                <input type="text" name="otp" class="auth-input" placeholder="000000" style="flex:1;text-align:center;letter-spacing:.25em;font-family:monospace;font-weight:700;font-size:1.1rem;" required maxlength="6">
                <button type="submit" class="auth-btn-primary" style="width:auto;padding:11px 18px;">Verify</button>
              </form>
            </div>
          </div>
        </div>

        <!-- ╔══ SIGNUP FORM ══╗ -->
        <div class="form-section <?= $activeTab === 'signup' ? 'active' : '' ?>" id="form-signup">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:.4rem;">
            <h2 style="font-size:1.5rem;font-weight:900;color:#111827;">Create Account</h2>
            <?php if ($otpEnabled): ?>
            <span class="otp-badge">🔐 OTP Required</span>
            <?php endif; ?>
          </div>
          <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.75rem;">
            <?= $otpEnabled
              ? 'Fill in your details. You\'ll verify your phone with an OTP before your account is created.'
              : 'Join Fresh E mart and get fresh groceries delivered to your door.' ?>
          </p>

          <form action="<?= $base ?>/checkout/signup" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= $base ?>/checkout">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
              <div>
                <label class="form-label">Full Name *</label>
                <div class="auth-input-icon-wrap">
                  <ion-icon name="person-outline"></ion-icon>
                  <input type="text" name="name" class="auth-input" placeholder="Your name" required autocomplete="name">
                </div>
              </div>
              <div>
                <label class="form-label">Phone Number *</label>
                <div class="auth-input-icon-wrap">
                  <ion-icon name="call-outline"></ion-icon>
                  <input type="tel" name="phone" class="auth-input" placeholder="01700000000" required autocomplete="tel">
                </div>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <label class="form-label">Email <span style="font-weight:400;text-transform:none;color:#9ca3af;">(Optional)</span></label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="email" name="email" class="auth-input" placeholder="you@example.com" autocomplete="email">
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
                <input type="password" name="password" class="auth-input" placeholder="Min. 6 characters" required minlength="6" autocomplete="new-password">
              </div>
            </div>

            <button type="submit" class="auth-btn-primary">
              <?php if ($otpEnabled): ?>
              <ion-icon name="shield-checkmark-outline" style="font-size:1.1rem;"></ion-icon>
              Continue to OTP Verification
              <?php else: ?>
              <ion-icon name="person-add-outline" style="font-size:1.1rem;"></ion-icon>
              Create Account &amp; Continue
              <?php endif; ?>
            </button>
          </form>
        </div>

      </div><!-- /auth-panel -->
    </div><!-- /auth-card -->
  </div>
</div>

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

// Auto-open if URL has ?tab=
if (window.location.search.includes('tab=signup')) switchTab('signup');

// ── OTP Login helper ────────────────────────────────────
function startOtpLogin() {
  const phone = document.querySelector('#form-login input[name="phone"]').value;
  if (!phone || phone.length < 11) { alert('Please enter your 11-digit phone number first.'); return; }
  document.getElementById('otp-login-phone').value = phone;
  document.getElementById('otp-login-section').style.display = 'block';
  document.getElementById('otp-login-section').scrollIntoView({ behavior: 'smooth' });
}

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
