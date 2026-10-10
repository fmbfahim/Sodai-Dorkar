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
$hideFooter = true;
?>

<!-- Ionicons for Modern Icons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<style>
/* ── Auth Page Premium Styles ───────────────────────────────────── */
.auth-wrap {
  min-height: calc(100vh - 160px);
  display: flex; align-items: center; justify-content: center;
  padding: 2.5rem 1rem;
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
  grid-template-columns: 280px 1fr;
}

/* Sidebar */
.auth-sidebar {
  background: linear-gradient(160deg, #064e3b 0%, #065f46 50%, #047857 100%);
  padding: 2.5rem 1.75rem;
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
  border-radius: 16px; font-weight: 700; font-size: .92rem;
  transition: all .25s; cursor: pointer; border: none;
  background: transparent; color: rgba(255,255,255,.7);
  text-align: left; position: relative; z-index: 1;
}
.auth-tab-btn:hover { background: rgba(255,255,255,.1); color: #fff; }
.auth-tab-btn.active {
  background: rgba(255,255,255,.2); color: #fff;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.auth-tab-btn .tab-icon {
  width: 42px; height: 42px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,.15); font-size: 1.25rem; flex-shrink: 0;
  transition: all .2s;
}
.auth-tab-btn.active .tab-icon { background: #10b981; color: #fff; }

/* Form Panel */
.auth-panel { padding: 2.5rem 2.5rem; position: relative; }

/* Mobile Adaptations */
@media (max-width:768px) {
  .auth-wrap {
    padding: 0.75rem 0.5rem;
    min-height: auto;
    background: #f8fafc;
  }
  .auth-card {
    grid-template-columns: 1fr;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
  }
  .auth-sidebar {
    padding: 1.25rem 1rem 0.85rem;
    gap: 0.5rem;
  }
  .auth-sidebar .auth-logo-box {
    margin-bottom: 0.65rem !important;
    text-align: center;
  }
  .auth-sidebar .auth-logo-box a > div:first-child {
    font-size: 1.25rem !important;
  }
  .auth-sidebar .auth-tabs-wrap {
    display: flex !important;
    flex-direction: row !important;
    gap: 5px !important;
    background: rgba(0, 0, 0, 0.22);
    padding: 4px;
    border-radius: 12px;
  }
  .auth-tab-btn {
    flex: 1;
    padding: 8px 6px;
    border-radius: 10px;
    justify-content: center;
    text-align: center;
    font-size: 0.82rem;
    gap: 5px;
  }
  .auth-tab-btn .tab-icon {
    width: 26px;
    height: 26px;
    font-size: 1rem;
    border-radius: 7px;
  }
  .auth-tab-btn .tab-sub {
    display: none !important;
  }
  .auth-sidebar .auth-security-note {
    display: none !important;
  }
  .auth-panel {
    padding: 1.25rem 1rem !important;
  }
  .auth-input, .select-styled {
    font-size: 16px !important; /* Prevents auto-zoom in iOS Safari */
    padding: 11px 14px;
  }
  .auth-geo-grid {
    grid-template-columns: 1fr !important;
  }
}

.form-section { display: none; }
.form-section.active { display: block; animation: slideIn .3s ease; }
@keyframes slideIn { from{opacity:0;transform:translateX(10px)} to{opacity:1;transform:translateX(0)} }

.auth-input {
  width: 100%; padding: 12px 16px;
  border: 2px solid #e5e7eb; border-radius: 14px;
  font-size: .95rem; font-family: inherit;
  transition: all .2s; background: #fafafa; color: #111827;
  outline: none;
}
.auth-input:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.auth-input::placeholder { color: #9ca3af; }

.auth-input-icon-wrap { position: relative; }
.auth-input-icon-wrap ion-icon {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  color: #9ca3af; font-size: 1.15rem; pointer-events: none;
}
.auth-input-icon-wrap .auth-input { padding-left: 44px; }

.auth-btn-primary {
  width: 100%; padding: 13px 22px;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff; font-weight: 800; font-size: .95rem;
  border: none; border-radius: 14px; cursor: pointer;
  transition: all .25s; letter-spacing: .01em;
  box-shadow: 0 6px 20px rgba(5,150,105,.28);
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
}
.auth-btn-primary:hover {
  background: linear-gradient(135deg, #047857, #059669);
  transform: translateY(-1px); box-shadow: 0 10px 28px rgba(5,150,105,.35);
}
.auth-btn-primary:active { transform: translateY(0); }
.auth-btn-primary:disabled { opacity: .65; cursor: not-allowed; transform: none !important; }

.auth-btn-secondary {
  padding: 12px 20px;
  background: #f3f4f6;
  border: 1.5px solid #e5e7eb;
  border-radius: 14px;
  color: #4b5563;
  font-weight: 700;
  font-size: .9rem;
  cursor: pointer;
  transition: all .2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.auth-btn-secondary:hover {
  background: #e5e7eb;
  color: #1f2937;
}

.form-label {
  display: block; font-size: .82rem; font-weight: 700;
  color: #374151; margin-bottom: 7px; letter-spacing: .02em;
}

.divider {
  display: flex; align-items: center; gap: 12px; margin: 18px 0;
  color: #9ca3af; font-size: .78rem; font-weight: 600;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

.error-box {
  background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 12px; padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  font-size: .85rem; font-weight: 600; color: #dc2626; margin-bottom: 18px;
}
.success-box {
  background: #ecfdf5; border: 1px solid #a7f3d0;
  border-radius: 12px; padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  font-size: .85rem; font-weight: 600; color: #047857; margin-bottom: 18px;
}

.select-styled {
  width: 100%; padding: 12px 16px;
  border: 2px solid #e5e7eb; border-radius: 14px;
  font-size: .9rem; font-family: inherit;
  background: #fafafa; color: #111827;
  outline: none; transition: all .2s; appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%239ca3af' viewBox='0 0 20 20'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 14px center; background-size: 18px;
  padding-right: 42px;
}
.select-styled:focus { border-color: #10b981; background-color: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.select-styled:disabled { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

.otp-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: #d1fae5; color: #065f46; font-size: .72rem; font-weight: 700;
  padding: 3px 10px; border-radius: 999px; border: 1px solid #a7f3d0;
}

/* ── Wizard Stepper Header ─────────────────────────── */
.wizard-header {
  padding-bottom: 1.25rem;
  margin-bottom: 1.75rem;
  border-bottom: 1px solid #f1f5f9;
}
.wizard-progress-track {
  width: 100%;
  height: 6px;
  background: #f1f5f9;
  border-radius: 999px;
  overflow: hidden;
  margin-top: 10px;
}
.wizard-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 999px;
  transition: width .35s ease;
}
.wizard-steps-indicators {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  margin-top: 14px;
}
.wizard-step-node {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: .8rem;
  font-weight: 700;
  color: #9ca3af;
  transition: all .2s;
}
.wizard-step-node .step-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .75rem;
  font-weight: 800;
  background: #f3f4f6;
  color: #6b7280;
  border: 1px solid #e5e7eb;
  transition: all .2s;
}
.wizard-step-node.active {
  color: #065f46;
}
.wizard-step-node.active .step-num {
  background: #10b981;
  color: #fff;
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(16,185,129,.2);
}
.wizard-step-node.completed {
  color: #059669;
}
.wizard-step-node.completed .step-num {
  background: #d1fae5;
  color: #047857;
  border-color: #6ee7b7;
}
@media (max-width: 580px) {
  .wizard-step-node .step-name { display: none; }
}

/* Wizard Step Cards */
.wizard-step-card {
  display: none;
  animation: stepFadeIn .3s ease-out forwards;
}
.wizard-step-card.active {
  display: block;
}
@keyframes stepFadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.step-icon-badge {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  border: 1.5px solid #a7f3d0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  margin-bottom: 12px;
  color: #059669;
}

/* OTP Digits */
.otp-box-digit {
  width: 48px;
  height: 56px;
  border: 2px solid #d1fae5;
  border-radius: 14px;
  font-size: 1.6rem;
  font-weight: 800;
  text-align: center;
  background: #f0fdf4;
  color: #064e3b;
  outline: none;
  transition: all .2s;
}
@media (max-width: 420px) {
  .otp-box-digit { width: 42px; height: 50px; font-size: 1.35rem; }
}
.otp-box-digit:focus {
  border-color: #10b981;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(16,185,129,.18);
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
      'firebase_failed'      => 'OTP যাচাই ব্যর্থ হয়েছে। আবার চেষ্টা করুন।',
      'otp_required'         => 'ওটিপি ভেরিফিকেশন সম্পন্ন করুন।',
    ]; ?>
    <div class="error-box mb-4 max-w-5xl mx-auto">
      <ion-icon name="alert-circle" style="font-size:1.3rem;flex-shrink:0"></ion-icon>
      <span><?= $errorMap[$error] ?? htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="auth-card">

      <!-- ── Sidebar ─────────────────────────────── -->
      <div class="auth-sidebar">
        <div>
          <!-- Logo -->
          <div class="auth-logo-box mb-8" style="position:relative;z-index:1">
            <a href="<?= $base ?>/" style="text-decoration:none;">
              <div style="font-size:1.5rem;font-weight:900;color:#fff;letter-spacing:-.02em;">
                🛒 <?= htmlspecialchars($siteName) ?>
              </div>
              <div style="font-size:.78rem;color:rgba(255,255,255,.65);margin-top:4px;">
                তাজা ও সেরা বাজার আপনার ঘরে
              </div>
            </a>
          </div>

          <!-- Tab Buttons -->
          <div class="auth-tabs-wrap space-y-3" style="position:relative;z-index:1">
            <button onclick="switchTab('login')" id="tab-login"
                    class="auth-tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>">
              <span class="tab-icon"><ion-icon name="log-in-outline"></ion-icon></span>
              <div>
                <div style="font-size:.95rem;">লগইন</div>
                <div class="tab-sub" style="font-size:.72rem;font-weight:500;opacity:.75">আগের অ্যাকাউন্ট আছে</div>
              </div>
            </button>

            <button onclick="switchTab('signup')" id="tab-signup"
                    class="auth-tab-btn <?= $activeTab === 'signup' ? 'active' : '' ?>">
              <span class="tab-icon"><ion-icon name="person-add-outline"></ion-icon></span>
              <div>
                <div style="font-size:.95rem;">রেজিস্ট্রেশন</div>
                <div class="tab-sub" style="font-size:.72rem;font-weight:500;opacity:.75">
                  নতুন অ্যাকাউন্ট খুলুন
                  <?php if ($otpEnabled): ?>
                  &nbsp;<span class="otp-badge">🔐 OTP</span>
                  <?php endif; ?>
                </div>
              </div>
            </button>

            <button onclick="switchTab('forgot')" id="tab-forgot"
                    class="auth-tab-btn <?= $activeTab === 'forgot' ? 'active' : '' ?>">
              <span class="tab-icon"><ion-icon name="key-outline"></ion-icon></span>
              <div>
                <div style="font-size:.95rem;">রিসেট</div>
                <div class="tab-sub" style="font-size:.72rem;font-weight:500;opacity:.75">ভুলে গেছেন? উদ্ধার করুন</div>
              </div>
            </button>
          </div>
        </div>

        <!-- Security note -->
        <div class="auth-security-note" style="position:relative;z-index:1;margin-top:2.5rem;">
          <div style="display:flex;align-items:center;gap:8px;font-size:.76rem;color:rgba(255,255,255,.65);">
            <ion-icon name="shield-checkmark-outline" style="font-size:1.1rem;color:#6ee7b7;"></ion-icon>
            SSL সুরক্ষিত · আপনার তথ্য সম্পূর্ণ নিরাপদ
          </div>
        </div>
      </div>

      <!-- ── Form Panel ─────────────────────────── -->
      <div class="auth-panel">

        <?php if (!empty($_SESSION['cart'])): ?>
          <div class="mb-5 p-3.5 rounded-2xl bg-gradient-to-r from-amber-50 to-emerald-50 border border-emerald-200/80 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
              <span class="text-xl">⚡</span>
              <div>
                <div class="text-xs font-black text-gray-900 leading-tight">পাসওয়ার্ড ছাড়াই সরাসরি অর্ডার করতে চান?</div>
                <div class="text-[10px] text-gray-500 font-medium">নাম, ফোন ও ঠিকানা দিয়ে ১-ক্লিকে ক্যাশ অন ডেলিভারিতে অর্ডার কনফার্ম করুন</div>
              </div>
            </div>
            <button type="button" onclick="openFastOrderModal()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition-all shrink-0 cursor-pointer active:scale-95">
              ১-ক্লিকে অর্ডার ➔
            </button>
          </div>
        <?php endif; ?>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║               LOGIN FORM                 ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <div class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>" id="form-login">
          <div class="mb-6">
            <h2 style="font-size:1.5rem;font-weight:900;color:#111827;margin-bottom:.35rem;">স্বাগতম! 👋</h2>
            <p style="font-size:.88rem;color:#6b7280;">আপনার অ্যাকাউন্টে লগইন করে সহজে বাজার সম্পন্ন করুন।</p>
          </div>

          <form action="<?= $base ?>/checkout/login" method="POST" id="normal-login-form">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? ($base . '/checkout')) ?>">

            <div style="margin-bottom:18px;">
              <label class="form-label" for="login-phone">মোবাইল নম্বর *</label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="call-outline"></ion-icon>
                <input type="tel" name="phone" id="login-phone" class="auth-input font-mono text-base"
                       placeholder="01XXXXXXXXX" required autocomplete="tel"
                       value="<?= $preFillPhone ?>">
              </div>
            </div>

            <div style="margin-bottom:20px;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;">
                <label class="form-label" for="loginPass" style="margin-bottom:0;">
                  পাসওয়ার্ড *
                  <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1'): ?>
                  <span style="font-weight:500;color:#6b7280;">(বা সাপোর্ট পিন)</span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="auth-input-icon-wrap" style="position:relative;">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" id="loginPass" class="auth-input" style="padding-right:44px;"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" onclick="togglePassVisibility('loginPass', this)"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;display:flex;align-items:center;padding:4px;"
                        title="পাসওয়ার্ড দেখুন/লুকান">
                  <ion-icon name="eye-outline" style="font-size:1.2rem;"></ion-icon>
                </button>
              </div>

              <?php if (($settings['auth_manual_pin_enabled'] ?? '1') == '1'): ?>
              <div style="text-align:right;margin-top:8px;">
                <a href="tel:01609448066" style="font-size:.78rem;color:#059669;font-weight:700;text-decoration:none;">
                  পাসওয়ার্ড ভুলে গেছেন? কাস্টমার সাপোর্টে কল করুন →
                </a>
              </div>
              <?php endif; ?>
            </div>

            <button type="submit" class="auth-btn-primary">
              <ion-icon name="log-in-outline" style="font-size:1.2rem;"></ion-icon>
              লগইন করুন &amp; এগিয়ে যান
            </button>
          </form>

          <!-- Switch to Registration Option -->
          <div style="margin-top:28px;padding-top:22px;border-top:1px solid #f1f5f9;text-align:center;">
            <p style="font-size:.85rem;color:#6b7280;margin-bottom:12px;">এখনো কোনো অ্যাকাউন্ট নেই?</p>
            <button type="button" onclick="switchTab('signup')" class="auth-btn-secondary" style="width:100%;background:#ecfdf5;color:#065f46;border-color:#a7f3d0;font-weight:800;padding:13px 20px;">
              <ion-icon name="person-add-outline" style="font-size:1.2rem;color:#059669;"></ion-icon>
              নতুন অ্যাকাউন্ট তৈরি করতে রেজিস্ট্রেশন করুন ➔
            </button>
          </div>
        </div>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║     SIGNUP FORM (STEP-BY-STEP WIZARD)    ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <div class="form-section <?= $activeTab === 'signup' ? 'active' : '' ?>" id="form-signup">

          <!-- Wizard Progress Stepper -->
          <div class="wizard-header">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <h2 style="font-size:1.35rem;font-weight:900;color:#111827;letter-spacing:-.01em;">নতুন অ্যাকাউন্ট তৈরি করুন</h2>
              <span id="step-counter-badge" style="font-size:.75rem;font-weight:800;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:4px 12px;border-radius:999px;">
                ধাপ ১ / <?= $otpEnabled ? '৫' : '৪' ?>
              </span>
            </div>

            <!-- Progress bar fill -->
            <div class="wizard-progress-track">
              <div class="wizard-progress-fill" id="wizard-progress-fill" style="width: <?= $otpEnabled ? '20%' : '25%' ?>;"></div>
            </div>

            <!-- Steps Nodes -->
            <div class="wizard-steps-indicators">
              <div class="wizard-step-node active" id="node-step-1">
                <span class="step-num">১</span>
                <span class="step-name">নাম</span>
              </div>
              <div class="wizard-step-node" id="node-step-2">
                <span class="step-num">২</span>
                <span class="step-name">মোবাইল</span>
              </div>
              <?php if ($otpEnabled): ?>
              <div class="wizard-step-node" id="node-step-3">
                <span class="step-num">৩</span>
                <span class="step-name">ওটিপি</span>
              </div>
              <?php endif; ?>
              <div class="wizard-step-node" id="node-step-4">
                <span class="step-num"><?= $otpEnabled ? '৪' : '৩' ?></span>
                <span class="step-name">পাসওয়ার্ড</span>
              </div>
              <div class="wizard-step-node" id="node-step-5">
                <span class="step-num"><?= $otpEnabled ? '৫' : '৪' ?></span>
                <span class="step-name">ঠিকানা</span>
              </div>
            </div>
          </div>

          <!-- Alert message box for feedback -->
          <div id="wizard-alert" style="display:none;margin-bottom:18px;"></div>

          <!-- Wizard Form Container -->
          <form id="wizard-form" autocomplete="off" onsubmit="return false;">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? ($base . '/checkout')) ?>">

            <!-- ══════════════════════════════════════════════ -->
            <!-- 📌 STEP 1: নাম (শুধুমাত্র একটি ইনপুট)         -->
            <!-- ══════════════════════════════════════════════ -->
            <div class="wizard-step-card active" id="step-card-1">
              <div class="step-icon-badge">👤</div>
              <h3 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.3rem;">আপনার পুরো নাম লিখুন</h3>
              <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.5rem;">আপনার নাম দিন যাতে আপনার ডেলিভারি ও অ্যাকাউন্ট সহজেই চিহ্নিত করা যায়।</p>

              <div style="margin-bottom:1.75rem;">
                <label class="form-label" for="reg-name">পুরো নাম *</label>
                <div class="auth-input-icon-wrap">
                  <ion-icon name="person-outline"></ion-icon>
                  <input type="text" id="reg-name" name="name" class="auth-input" style="font-size:1.05rem;padding:14px 16px 14px 44px;"
                         placeholder="যেমন: আব্দুর রহিম" autofocus required autocomplete="name">
                </div>
                <div id="step-1-error" style="display:none;color:#dc2626;font-size:.8rem;font-weight:600;margin-top:6px;"></div>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;">
                <button type="button" onclick="switchTab('login')" style="background:none;border:none;color:#059669;font-weight:700;font-size:.86rem;cursor:pointer;padding:8px 0;">
                  ইতিমধ্যে অ্যাকাউন্ট আছে? লগইন করুন ➔
                </button>
                <button type="button" id="btn-next-step-1" onclick="wizardNext(1)" class="auth-btn-primary" style="width:auto;min-width:140px;padding:12px 24px;">
                  পরবর্তী ধাপ ➔
                </button>
              </div>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- 📌 STEP 2: মোবাইল নম্বর (শুধুমাত্র একটি ইনপুট) -->
            <!-- ══════════════════════════════════════════════ -->
            <div class="wizard-step-card" id="step-card-2">
              <div class="step-icon-badge">📱</div>
              <h3 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.3rem;">আপনার মোবাইল নম্বর দিন</h3>
              <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.5rem;">অ্যাকাউন্টে লগইন ও প্রতিটি অর্ডারের আপডেট এই নম্বরে পাঠানো হবে।</p>

              <div style="margin-bottom:1.75rem;">
                <label class="form-label" for="reg-phone">১১-সংখ্যার মোবাইল নম্বর *</label>
                <div style="position:relative;display:flex;align-items:center;">
                  <span style="position:absolute;left:14px;font-weight:800;color:#059669;font-size:1rem;pointer-events:none;user-select:none;">+88</span>
                  <input type="tel" id="reg-phone" name="phone" class="auth-input font-mono" style="font-size:1.15rem;font-weight:700;letter-spacing:1px;padding:14px 16px 14px 54px;"
                         placeholder="01XXXXXXXXX" maxlength="11" required autocomplete="tel" value="<?= $preFillPhone ?>">
                </div>
                <div id="step-2-error" style="display:none;margin-top:10px;"></div>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;">
                <button type="button" onclick="wizardBack(2)" class="auth-btn-secondary">
                  ← পেছনে
                </button>
                <button type="button" id="btn-next-step-2" onclick="wizardNext(2)" class="auth-btn-primary" style="width:auto;min-width:140px;padding:12px 24px;">
                  পরবর্তী ধাপ ➔
                </button>
              </div>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- 📌 STEP 3: OTP ইনপুট (যদি OTP অন থাকে)       -->
            <!-- ══════════════════════════════════════════════ -->
            <?php if ($otpEnabled): ?>
            <div class="wizard-step-card" id="step-card-3">
              <div class="step-icon-badge" style="margin-left:auto;margin-right:auto;">🔐</div>
              <div style="text-align:center;margin-bottom:1.5rem;">
                <h3 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.3rem;">মোবাইল নম্বর যাচাই করুন (OTP)</h3>
                <p style="font-size:.85rem;color:#6b7280;margin-bottom:.3rem;">
                  একটি ৬-সংখ্যার ভেরিফিকেশন কোড পাঠানো হয়েছে:
                </p>
                <div style="display:inline-flex;align-items:center;gap:8px;">
                  <span id="display-otp-phone" style="font-weight:900;color:#059669;font-family:monospace;font-size:1.1rem;">01XXXXXXXXX</span>
                  <button type="button" onclick="wizardGoTo(2)" style="background:none;border:none;color:#2563eb;font-size:.78rem;font-weight:700;cursor:pointer;text-decoration:underline;">
                    ✏️ নম্বর পরিবর্তন
                  </button>
                </div>
              </div>

              <div style="margin-bottom:1.75rem;text-align:center;">
                <label class="form-label" style="text-align:center;margin-bottom:12px;">৬-সংখ্যার কোডটি প্রবেশ করান</label>
                <div style="display:flex;justify-content:center;gap:8px;" id="otp-digit-boxes">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="0">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="1">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="2">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="3">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="4">
                  <input type="text" inputmode="numeric" maxlength="1" class="otp-box-digit" data-idx="5">
                </div>
                <input type="hidden" id="reg-otp" name="otp_code">

                <div id="step-3-error" style="display:none;color:#dc2626;font-size:.82rem;font-weight:600;margin-top:12px;"></div>

                <!-- Resend countdown -->
                <div style="margin-top:14px;font-size:.82rem;color:#6b7280;font-weight:600;">
                  <span id="otp-timer-wrap">পুনরায় কোড পাঠানোর সময় বাকি: <span id="otp-countdown" style="font-weight:800;color:#059669;">60</span> সেকেন্ড</span>
                  <button type="button" id="btn-resend-otp" onclick="wizardResendOtp()" style="display:none;background:none;border:none;color:#059669;font-weight:800;cursor:pointer;text-decoration:underline;">
                    🔄 পুনরায় কোড পাঠান (Resend OTP)
                  </button>
                </div>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;">
                <button type="button" onclick="wizardBack(3)" class="auth-btn-secondary">
                  ← পেছনে
                </button>
                <button type="button" id="btn-next-step-3" onclick="wizardNext(3)" class="auth-btn-primary" style="width:auto;min-width:160px;padding:12px 24px;">
                  যাচাই ও পরবর্তী ধাপ ➔
                </button>
              </div>
            </div>
            <?php endif; ?>

            <!-- ══════════════════════════════════════════════ -->
            <!-- 📌 STEP 4: পাসওয়ার্ড ও নিশ্চিতকরণ (২ বারে দেবে) -->
            <!-- ══════════════════════════════════════════════ -->
            <div class="wizard-step-card" id="step-card-4">
              <div class="step-icon-badge">🔒</div>
              <h3 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.3rem;">একটি নতুন পাসওয়ার্ড নির্ধারণ করুন</h3>
              <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.5rem;">ভবিষ্যতে লগইন করার জন্য একটি গোপন পাসওয়ার্ড দুইবারে টাইপ করে নিশ্চিত করুন।</p>

              <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:1.75rem;">
                <div>
                  <label class="form-label" for="reg-password">নতুন পাসওয়ার্ড (Password) *</label>
                  <div class="auth-input-icon-wrap" style="position:relative;">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" id="reg-password" name="password" class="auth-input" style="padding-right:44px;"
                           placeholder="কমপক্ষে ৬ অক্ষরের পাসওয়ার্ড" minlength="6" required autocomplete="new-password">
                    <button type="button" onclick="togglePassVisibility('reg-password', this)"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:4px;"
                            title="পাসওয়ার্ড দেখুন/লুকান">
                      <ion-icon name="eye-outline" style="font-size:1.2rem;"></ion-icon>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="form-label" for="reg-password-confirm">পাসওয়ার্ড নিশ্চিত করুন (Confirm Password) *</label>
                  <div class="auth-input-icon-wrap" style="position:relative;">
                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                    <input type="password" id="reg-password-confirm" class="auth-input" style="padding-right:44px;"
                           placeholder="পাসওয়ার্ডটি পুনরায় লিখুন" minlength="6" required autocomplete="new-password">
                    <button type="button" onclick="togglePassVisibility('reg-password-confirm', this)"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:4px;"
                            title="পাসওয়ার্ড দেখুন/লুকান">
                      <ion-icon name="eye-outline" style="font-size:1.2rem;"></ion-icon>
                    </button>
                  </div>
                  <div id="password-match-status" style="margin-top:6px;font-size:.8rem;font-weight:700;"></div>
                </div>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;">
                <button type="button" onclick="wizardBack(4)" class="auth-btn-secondary">
                  ← পেছনে
                </button>
                <button type="button" id="btn-next-step-4" onclick="wizardNext(4)" class="auth-btn-primary" style="width:auto;min-width:140px;padding:12px 24px;">
                  পরবর্তী ধাপ ➔
                </button>
              </div>
            </div>

            <!-- ══════════════════════════════════════════════ -->
            <!-- 📌 STEP 5: এরিয়া ও ঠিকানা (শেষ ধাপ)            -->
            <!-- ══════════════════════════════════════════════ -->
            <div class="wizard-step-card" id="step-card-5">
              <div class="step-icon-badge">📍</div>
              <h3 style="font-size:1.3rem;font-weight:900;color:#111827;margin-bottom:.3rem;">ডেলিভারি এরিয়া ও ঠিকানা</h3>
              <p style="font-size:.85rem;color:#6b7280;margin-bottom:1.5rem;">আপনার অর্ডার সঠিক স্থানে পৌঁছে দিতে এরিয়া ও ঠিকানা নির্বাচন করুন।</p>

              <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:1.75rem;">

                <!-- Cascading Area / Zone / Point -->
                <div class="auth-geo-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                  <div>
                    <label class="form-label" for="reg_area_id">এরিয়া (Area) *</label>
                    <select id="reg_area_id" name="area_id" class="select-styled" required>
                      <option value="" disabled selected>এলাকা নির্বাচন</option>
                      <?php foreach ($areas as $area): ?>
                      <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div>
                    <label class="form-label" for="reg_zone_id">জোন/থানা (Zone) *</label>
                    <select id="reg_zone_id" name="zone_id" class="select-styled" required disabled>
                      <option value="" disabled selected>আগে এরিয়া বেছে নিন</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label" for="reg_point_id">পয়েন্ট (Point) *</label>
                    <select id="reg_point_id" name="point_id" class="select-styled" required disabled>
                      <option value="" disabled selected>আগে জোন বেছে নিন</option>
                    </select>
                  </div>
                </div>

                <!-- Detailed Address -->
                <div id="address_details_wrapper">
                  <label class="form-label" for="reg_address">বিস্তারিত ঠিকানা (বাড়ি, রোড, ফ্ল্যাট) *</label>
                  <textarea id="reg_address" name="address" rows="2" class="auth-input" style="resize:vertical;"
                            placeholder="যেমন: বাড়ি নং ১২, রোড নং ৩, ব্লক বি..." required></textarea>
                </div>

                <!-- Optional Email -->
                <div>
                  <label class="form-label" for="reg_email">ইমেইল <span style="font-weight:400;color:#9ca3af;">(ঐচ্ছিক / Optional)</span></label>
                  <div class="auth-input-icon-wrap">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" id="reg_email" name="email" class="auth-input" placeholder="you@example.com" autocomplete="email">
                  </div>
                </div>

              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;">
                <button type="button" onclick="wizardBack(5)" class="auth-btn-secondary">
                  ← পেছনে
                </button>
                <button type="button" id="btn-submit-registration" onclick="wizardSubmitRegistration()" class="auth-btn-primary" style="width:auto;min-width:180px;padding:13px 26px;">
                  🎉 রেজিস্ট্রেশন সম্পন্ন করুন
                </button>
              </div>
            </div>

          </form>
        </div>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║         FORGOT PASSWORD FORM             ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <div class="form-section <?= $activeTab === 'forgot' ? 'active' : '' ?>" id="form-forgot">
          <div class="mb-6">
            <h2 style="font-size:1.45rem;font-weight:900;color:#111827;margin-bottom:.35rem;">পাসওয়ার্ড রিসেট করুন 🔑</h2>
            <p style="font-size:.85rem;color:#6b7280;">আপনার রেজিস্টার্ড মোবাইল নম্বর দিন। আমরা ভেরিফিকেশন কোড (OTP) পাঠাব।</p>
          </div>

          <div id="forgot-alert-box" style="display:none;padding:12px 16px;border-radius:14px;margin-bottom:18px;font-size:.85rem;font-weight:600;"></div>

          <!-- Step 1: Send OTP -->
          <div id="forgot-step-phone">
            <div style="margin-bottom:18px;">
              <label class="form-label" for="forgot-phone">মোবাইল নম্বর *</label>
              <div class="auth-input-icon-wrap">
                <ion-icon name="call-outline"></ion-icon>
                <input type="tel" id="forgot-phone" class="auth-input font-mono text-base"
                       placeholder="01XXXXXXXXX" autocomplete="tel" value="<?= $preFillPhone ?>">
              </div>
              <p style="font-size:.75rem;color:#9ca3af;margin-top:6px;">যে নম্বর দিয়ে অ্যাকাউন্ট খোলা হয়েছে সেটি লিখুন।</p>
            </div>

            <button type="button" id="btn-forgot-send-otp" onclick="handleForgotSendOtp()" class="auth-btn-primary">
              <ion-icon name="paper-plane-outline" style="font-size:1.2rem;"></ion-icon>
              <span>ওটিপি কোড পাঠান (Send OTP)</span>
            </button>
          </div>

          <!-- Step 2: Verify OTP & Set New Password -->
          <div id="forgot-step-reset" style="display:none;">
            <div style="margin-bottom:16px;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <label class="form-label" style="margin-bottom:0;">ভেরিফিকেশন কোড (OTP) *</label>
                <span id="forgot-phone-display" style="font-size:.78rem;font-weight:700;color:#059669;font-family:monospace;"></span>
              </div>
              <div class="auth-input-icon-wrap">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
                <input type="text" id="forgot-otp" maxlength="6" class="auth-input font-mono text-center text-lg tracking-widest font-black"
                       placeholder="••••••" autocomplete="one-time-code">
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                <span id="forgot-timer" style="font-size:.75rem;color:#6b7280;">পুনরায় কোড পাঠানোর সময়: <strong>60s</strong></span>
                <button type="button" id="btn-forgot-resend" onclick="handleForgotResendOtp()" style="display:none;background:none;border:none;color:#059669;font-size:.78rem;font-weight:700;cursor:pointer;">
                  পুনরায় পাঠান ↻
                </button>
              </div>
            </div>

            <div style="margin-bottom:16px;">
              <label class="form-label" for="forgot-new-pass">নতুন পাসওয়ার্ড * (কমপক্ষে ৬ অক্ষর)</label>
              <div class="auth-input-icon-wrap" style="position:relative;">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" id="forgot-new-pass" class="auth-input" style="padding-right:44px;"
                       placeholder="••••••••" minlength="6" autocomplete="new-password">
                <button type="button" onclick="togglePassVisibility('forgot-new-pass', this)"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;display:flex;align-items:center;padding:4px;">
                  <ion-icon name="eye-outline" style="font-size:1.2rem;"></ion-icon>
                </button>
              </div>
            </div>

            <div style="margin-bottom:22px;">
              <label class="form-label" for="forgot-confirm-pass">পাসওয়ার্ড নিশ্চিত করুন *</label>
              <div class="auth-input-icon-wrap" style="position:relative;">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" id="forgot-confirm-pass" class="auth-input" style="padding-right:44px;"
                       placeholder="••••••••" minlength="6" autocomplete="new-password">
                <button type="button" onclick="togglePassVisibility('forgot-confirm-pass', this)"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;display:flex;align-items:center;padding:4px;">
                  <ion-icon name="eye-outline" style="font-size:1.2rem;"></ion-icon>
                </button>
              </div>
            </div>

            <button type="button" id="btn-forgot-submit" onclick="handleForgotSubmitReset()" class="auth-btn-primary">
              <ion-icon name="checkmark-done-circle-outline" style="font-size:1.2rem;"></ion-icon>
              <span>পাসওয়ার্ড পরিবর্তন ও লগইন করুন</span>
            </button>
          </div>

          <div style="margin-top:24px;padding-top:18px;border-top:1px solid #f1f5f9;text-align:center;">
            <button type="button" onclick="switchTab('login')" style="background:none;border:none;font-size:.85rem;color:#059669;font-weight:700;cursor:pointer;">
              ← পাসওয়ার্ড মনে পড়েছে? লগইন করুন
            </button>
          </div>
        </div>

      </div><!-- /auth-panel -->
    </div><!-- /auth-card -->
  </div>
</div>

<script>
// ── Global Config & State ─────────────────────────────────
const APP_BASE       = '<?= $base ?>';
const OTP_REQUIRED   = <?= $otpEnabled ? 'true' : 'false' ?>;
const TOTAL_STEPS    = OTP_REQUIRED ? 5 : 4;

let currentStep = 1;
let otpResendTimer = null;
let otpSecondsLeft = 60;

// ── Tab Switching (Login vs Registration) ─────────────────
function switchTab(tab) {
  ['login','signup','forgot'].forEach(t => {
    const f = document.getElementById('form-' + t);
    const b = document.getElementById('tab-' + t);
    if (f) f.classList.remove('active');
    if (b) b.classList.remove('active');
  });
  document.getElementById('form-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');

  if (tab === 'signup') {
    wizardGoTo(1);
  } else {
    setTimeout(() => {
      const phoneInput = document.getElementById('login-phone');
      if (phoneInput && !phoneInput.value) phoneInput.focus();
    }, 100);
  }
}

// ── Stepper UI Synchronizer ──────────────────────────────
function updateStepperUI(step) {
  currentStep = step;

  // Step counter text
  const badge = document.getElementById('step-counter-badge');
  const banglaDigits = {'1':'১','2':'২','3':'৩','4':'৪','5':'৫'};
  const totalBangla  = banglaDigits[TOTAL_STEPS] || TOTAL_STEPS;
  let currentVisual  = step;
  if (!OTP_REQUIRED && step >= 4) {
    currentVisual = step - 1; // Step 4 becomes step 3 visually, Step 5 becomes step 4
  }
  badge.textContent = `ধাপ ${banglaDigits[currentVisual] || currentVisual} / ${totalBangla}`;

  // Progress Bar Fill
  const fillPct = Math.round((currentVisual / TOTAL_STEPS) * 100);
  document.getElementById('wizard-progress-fill').style.width = fillPct + '%';

  // Step Node Indicators
  const nodes = [1, 2, 3, 4, 5];
  nodes.forEach(n => {
    const el = document.getElementById('node-step-' + n);
    if (!el) return;
    el.classList.remove('active', 'completed');
    if (n === step) {
      el.classList.add('active');
    } else if (n < step) {
      el.classList.add('completed');
    }
  });

  // Switch Active Card
  document.querySelectorAll('.wizard-step-card').forEach(card => card.classList.remove('active'));
  const activeCard = document.getElementById('step-card-' + step);
  if (activeCard) {
    activeCard.classList.add('active');
  }

  // Auto-focus primary input of active step
  setTimeout(() => {
    if (step === 1) {
      const el = document.getElementById('reg-name');
      if (el) el.focus();
    } else if (step === 2) {
      const el = document.getElementById('reg-phone');
      if (el) el.focus();
    } else if (step === 3) {
      const firstOtp = document.querySelector('.otp-box-digit[data-idx="0"]');
      if (firstOtp) firstOtp.focus();
    } else if (step === 4) {
      const el = document.getElementById('reg-password');
      if (el) el.focus();
    } else if (step === 5) {
      const el = document.getElementById('reg_area_id');
      if (el) el.focus();
    }
  }, 100);
}

// ── Wizard Navigation Helpers ─────────────────────────────
function wizardGoTo(step) {
  if (step === 3 && !OTP_REQUIRED) {
    step = 4;
  }
  updateStepperUI(step);
}

function wizardBack(fromStep) {
  let prevStep = fromStep - 1;
  if (fromStep === 4 && !OTP_REQUIRED) {
    prevStep = 2; // Skip step 3 (OTP) when going backward
  }
  wizardGoTo(prevStep);
}

// ── Step 1: নাম Validation & Next ─────────────────────────
function validateStep1() {
  const nameInput = document.getElementById('reg-name');
  const err = document.getElementById('step-1-error');
  const val = nameInput.value.trim();

  if (!val || val.length < 2) {
    err.textContent = 'অনুগ্রহ করে আপনার সঠিক পুরো নাম লিখুন (ন্যূনতম ২ অক্ষর)।';
    err.style.display = 'block';
    nameInput.focus();
    return false;
  }
  err.style.display = 'none';
  return true;
}

// ── Step 2: মোবাইল নম্বর Validation & Duplicate Check ─────
async function handleStep2Next() {
  const phoneInput = document.getElementById('reg-phone');
  const errDiv     = document.getElementById('step-2-error');
  const btn        = document.getElementById('btn-next-step-2');
  let phone        = phoneInput.value.trim().replace(/\D/g, '');

  if (phone.startsWith('880')) {
    phone = '0' + phone.substring(3);
  }

  // Validate format (Bangladeshi 11-digit mobile: 013-019)
  const phoneRegex = /^01[3-9]\d{8}$/;
  if (!phoneRegex.test(phone)) {
    errDiv.innerHTML = '<span style="color:#dc2626;font-size:.82rem;font-weight:700;">⚠️ সঠিক ১১ সংখ্যার মোবাইল নম্বর লিখুন (যেমন: 017XXXXXXXX)</span>';
    errDiv.style.display = 'block';
    phoneInput.focus();
    return;
  }

  phoneInput.value = phone;
  errDiv.style.display = 'none';

  // Check phone existence via AJAX
  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = '⏳ নম্বর যাচাই হচ্ছে...';

  try {
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    const res = await fetch(`${APP_BASE}/checkout/check-phone`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();

    if (data.exists) {
      btn.disabled = false;
      btn.innerHTML = originalText;
      errDiv.innerHTML = `
        <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:12px 14px;color:#92400e;font-size:.85rem;display:flex;flex-direction:column;gap:8px;">
          <div style="font-weight:700;">⚠️ এই নম্বরে ইতিমধ্যে একটি একাউন্ট খোলা রয়েছে!</div>
          <div style="display:flex;align-items:center;gap:10px;">
            <button type="button" onclick="goToLoginWithPhone('${phone}')" style="background:#059669;color:#fff;border:none;border-radius:10px;padding:8px 16px;font-size:.8rem;font-weight:800;cursor:pointer;">
              🔑 এই নম্বরে লগইন করুন ➔
            </button>
          </div>
        </div>
      `;
      errDiv.style.display = 'block';
      return;
    }

    // Phone is available!
    if (OTP_REQUIRED) {
      btn.innerHTML = '📤 ওটিপি পাঠানো হচ্ছে...';
      const otpRes = await fetch(`${APP_BASE}/checkout/send-signup-otp`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const otpData = await otpRes.json();

      btn.disabled = false;
      btn.innerHTML = originalText;

      if (!otpData.success) {
        errDiv.innerHTML = `<span style="color:#dc2626;font-size:.82rem;font-weight:700;">${otpData.message || 'ওটিপি পাঠাতে সমস্যা হয়েছে।'}</span>`;
        errDiv.style.display = 'block';
        return;
      }

      // Update OTP Target Phone Display
      document.getElementById('display-otp-phone').textContent = phone;
      startOtpCountdown();
      wizardGoTo(3);
    } else {
      btn.disabled = false;
      btn.innerHTML = originalText;
      wizardGoTo(4); // Straight to password!
    }

  } catch (e) {
    btn.disabled = false;
    btn.innerHTML = originalText;
    errDiv.innerHTML = '<span style="color:#dc2626;font-size:.82rem;font-weight:700;">নেটওয়ার্ক সমস্যা। পুনরায় চেষ্টা করুন।</span>';
    errDiv.style.display = 'block';
  }
}

function goToLoginWithPhone(phone) {
  switchTab('login');
  document.getElementById('login-phone').value = phone;
  const passEl = document.getElementById('loginPass');
  if (passEl) passEl.focus();
}

// ── Step 3: OTP Verification & Resend ─────────────────────
function startOtpCountdown() {
  clearInterval(otpResendTimer);
  otpSecondsLeft = 60;
  document.getElementById('otp-timer-wrap').style.display = 'inline';
  document.getElementById('btn-resend-otp').style.display = 'none';
  document.getElementById('otp-countdown').textContent    = otpSecondsLeft;

  otpResendTimer = setInterval(() => {
    otpSecondsLeft--;
    document.getElementById('otp-countdown').textContent = otpSecondsLeft;
    if (otpSecondsLeft <= 0) {
      clearInterval(otpResendTimer);
      document.getElementById('otp-timer-wrap').style.display = 'none';
      document.getElementById('btn-resend-otp').style.display = 'inline';
    }
  }, 1000);
}

async function wizardResendOtp() {
  const phone = document.getElementById('reg-phone').value.trim();
  const btn   = document.getElementById('btn-resend-otp');
  btn.textContent = 'পাঠানো হচ্ছে...';
  btn.disabled    = true;

  try {
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    const res = await fetch(`${APP_BASE}/checkout/resend-signup-otp`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    btn.disabled = false;
    btn.textContent = '🔄 পুনরায় কোড পাঠান (Resend OTP)';

    if (data.success) {
      startOtpCountdown();
      showToast('নতুন ওটিপি কোড পাঠানো হয়েছে।', 'success');
    } else {
      showToast(data.message || 'কোড পুনরায় পাঠানো যায়নি।', 'error');
    }
  } catch (e) {
    btn.disabled = false;
    btn.textContent = '🔄 পুনরায় কোড পাঠান (Resend OTP)';
    showToast('নেটওয়ার্ক সমস্যা!', 'error');
  }
}

async function handleStep3Next() {
  const digits = Array.from(document.querySelectorAll('.otp-box-digit')).map(d => d.value).join('');
  const errDiv = document.getElementById('step-3-error');
  const btn    = document.getElementById('btn-next-step-3');
  const phone  = document.getElementById('reg-phone').value.trim();

  if (digits.length < 6) {
    errDiv.textContent   = 'অনুগ্রহ করে সম্পূর্ণ ৬-ডিজিটের কোড লিখুন।';
    errDiv.style.display = 'block';
    return;
  }
  errDiv.style.display = 'none';
  document.getElementById('reg-otp').value = digits;

  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = '⏳ যাচাই করা হচ্ছে...';

  try {
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('otp_code', digits);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    const res = await fetch(`${APP_BASE}/checkout/verify-signup-otp`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    btn.disabled = false;
    btn.innerHTML = originalText;

    if (data.success) {
      wizardGoTo(4); // Advance to Password Setup!
    } else {
      errDiv.textContent   = data.message || 'ভুল ওটিপি কোড! অনুগ্রহ করে পুনরায় চেষ্টা করুন।';
      errDiv.style.display = 'block';
    }
  } catch (e) {
    btn.disabled = false;
    btn.innerHTML = originalText;
    errDiv.textContent   = 'যাচাই করতে সমস্যা হয়েছে। ইন্টারনেট সংযোগ চেক করুন।';
    errDiv.style.display = 'block';
  }
}

// ── Step 4: পাসওয়ার্ড ও নিশ্চিতকরণ (২ বারে দেবে) ─────────────
function validateStep4() {
  const p1 = document.getElementById('reg-password').value;
  const p2 = document.getElementById('reg-password-confirm').value;
  const statusDiv = document.getElementById('password-match-status');

  if (!p1 || p1.length < 6) {
    statusDiv.style.color = '#dc2626';
    statusDiv.textContent = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে!';
    document.getElementById('reg-password').focus();
    return false;
  }

  if (p1 !== p2) {
    statusDiv.style.color = '#dc2626';
    statusDiv.textContent = 'দুটি পাসওয়ার্ড মিলছে না! পুনরায় চেক করুন।';
    document.getElementById('reg-password-confirm').focus();
    return false;
  }

  statusDiv.style.color = '#059669';
  statusDiv.textContent = '✓ পাসওয়ার্ড মিলেছে!';
  return true;
}

// Live Password Match Checker
document.addEventListener('DOMContentLoaded', function() {
  const p1 = document.getElementById('reg-password');
  const p2 = document.getElementById('reg-password-confirm');
  const statusDiv = document.getElementById('password-match-status');

  function checkMatch() {
    if (!p2.value) { statusDiv.textContent = ''; return; }
    if (p1.value === p2.value) {
      statusDiv.style.color = '#059669';
      statusDiv.textContent = '✓ পাসওয়ার্ড মিলেছে';
    } else {
      statusDiv.style.color = '#dc2626';
      statusDiv.textContent = '✕ পাসওয়ার্ড দুটি মিলছে না';
    }
  }

  if (p1 && p2) {
    p1.addEventListener('input', checkMatch);
    p2.addEventListener('input', checkMatch);
  }
});

// ── Step 5: এরিয়া ও ঠিকানা & Final Registration Submit ──────
async function wizardSubmitRegistration() {
  const areaSel  = document.getElementById('reg_area_id');
  const zoneSel  = document.getElementById('reg_zone_id');
  const pointSel = document.getElementById('reg_point_id');
  const addrText = document.getElementById('reg_address');
  const alertBox = document.getElementById('wizard-alert');
  const submitBtn= document.getElementById('btn-submit-registration');

  if (!areaSel.value) {
    showToast('অনুগ্রহ করে এরিয়া নির্বাচন করুন।', 'error');
    areaSel.focus();
    return;
  }
  if (!zoneSel.value) {
    showToast('অনুগ্রহ করে জোন/থানা নির্বাচন করুন।', 'error');
    zoneSel.focus();
    return;
  }
  if (!pointSel.value) {
    showToast('অনুগ্রহ করে পয়েন্ট নির্বাচন করুন।', 'error');
    pointSel.focus();
    return;
  }
  if (!addrText.value.trim()) {
    showToast('অনুগ্রহ করে আপনার বিস্তারিত ঠিকানা লিখুন।', 'error');
    addrText.focus();
    return;
  }

  submitBtn.disabled = true;
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '⏳ অ্যাকাউন্ট তৈরি হচ্ছে...';

  // Gather complete form payload
  const formData = new FormData();
  formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
  formData.append('redirect', document.querySelector('input[name="redirect"]').value);
  formData.append('is_ajax', '1');
  formData.append('name', document.getElementById('reg-name').value.trim());
  formData.append('phone', document.getElementById('reg-phone').value.trim());
  formData.append('password', document.getElementById('reg-password').value);
  formData.append('area_id', areaSel.value);
  formData.append('zone_id', zoneSel.value);
  formData.append('point_id', pointSel.value);
  formData.append('address', addrText.value.trim());
  formData.append('email', document.getElementById('reg_email').value.trim());

  if (OTP_REQUIRED) {
    formData.append('otp_code', document.getElementById('reg-otp').value.trim());
  }

  try {
    const res = await fetch(`${APP_BASE}/checkout/signup`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();

    if (data.success) {
      submitBtn.innerHTML = '✅ রেজিস্ট্রেশন সফল!';
      alertBox.className = 'success-box';
      alertBox.innerHTML = `
        <ion-icon name="checkmark-circle" style="font-size:1.5rem;color:#059669;"></ion-icon>
        <div>
          <div style="font-weight:900;font-size:1rem;color:#064e3b;">অভিনন্দন! রেজিস্ট্রেশন সম্পন্ন হয়েছে।</div>
          <div style="font-size:.82rem;color:#065f46;">আপনাকে সরাসরি কেনাকাটায় নিয়ে যাওয়া হচ্ছে...</div>
        </div>
      `;
      alertBox.style.display = 'flex';

      setTimeout(() => {
        window.location.href = data.redirect || `${APP_BASE}/checkout`;
      }, 1000);
    } else {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      alertBox.className = 'error-box';
      alertBox.innerHTML = `
        <ion-icon name="alert-circle" style="font-size:1.4rem;color:#dc2626;"></ion-icon>
        <span>${data.message || 'রেজিস্ট্রেশন করতে সমস্যা হয়েছে।'}</span>
      `;
      alertBox.style.display = 'flex';
      window.scrollTo({ top: alertBox.offsetTop - 40, behavior: 'smooth' });
    }
  } catch (e) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
    alertBox.className = 'error-box';
    alertBox.innerHTML = `
      <ion-icon name="alert-circle" style="font-size:1.4rem;color:#dc2626;"></ion-icon>
      <span>নেটওয়ার্ক ত্রুটি! অনুগ্রহ করে পুনরায় চেষ্টা করুন।</span>
    `;
    alertBox.style.display = 'flex';
  }
}

// ── Master Wizard Next Step Controller ────────────────────
function wizardNext(fromStep) {
  if (fromStep === 1) {
    if (validateStep1()) wizardGoTo(2);
  } else if (fromStep === 2) {
    handleStep2Next();
  } else if (fromStep === 3) {
    handleStep3Next();
  } else if (fromStep === 4) {
    if (validateStep4()) wizardGoTo(5);
  }
}

// ── Keyboard Enter Key Triggers ───────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('reg-name').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); wizardNext(1); }
  });
  document.getElementById('reg-phone').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); wizardNext(2); }
  });
  document.getElementById('reg-password-confirm').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); wizardNext(4); }
  });

  // OTP digit navigation (auto advance, backspace, paste)
  const otpInputs = Array.from(document.querySelectorAll('.otp-box-digit'));
  otpInputs.forEach((input, i) => {
    input.addEventListener('input', e => {
      const v = e.data || input.value;
      if (!/^\d$/.test(v)) { input.value = ''; return; }
      input.value = v;
      if (i < otpInputs.length - 1) otpInputs[i + 1].focus();
      else if (i === otpInputs.length - 1) {
        // All 6 filled: auto trigger verify
        setTimeout(() => handleStep3Next(), 150);
      }
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace') {
        input.value = '';
        if (i > 0) otpInputs[i - 1].focus();
      } else if (e.key === 'Enter') {
        e.preventDefault();
        handleStep3Next();
      }
    });
    input.addEventListener('paste', e => {
      e.preventDefault();
      const txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
      txt.split('').forEach((c, j) => { if (otpInputs[j]) otpInputs[j].value = c; });
      const next = Math.min(txt.length, otpInputs.length - 1);
      otpInputs[next].focus();
      if (txt.length >= 6) {
        setTimeout(() => handleStep3Next(), 150);
      }
    });
  });
});

// ── Cascading Dropdowns for Area / Zone / Point ───────────
document.addEventListener('DOMContentLoaded', function() {
  const areaSel  = document.getElementById('reg_area_id');
  const zoneSel  = document.getElementById('reg_zone_id');
  const pointSel = document.getElementById('reg_point_id');

  if (areaSel) {
    areaSel.addEventListener('change', function() {
      const areaId = this.value;
      zoneSel.innerHTML = '<option value="" disabled selected>লোড হচ্ছে...</option>';
      zoneSel.disabled  = true;
      pointSel.innerHTML = '<option value="" disabled selected>আগে জোন বেছে নিন</option>';
      pointSel.disabled  = true;

      fetch(`${APP_BASE}/api/zones?area_id=${areaId}`)
        .then(r => r.json())
        .then(data => {
          zoneSel.innerHTML = '<option value="" disabled selected>জোন/থানা নির্বাচন করুন</option>';
          data.forEach(z => zoneSel.innerHTML += `<option value="${z.id}">${z.name}</option>`);
          zoneSel.disabled = false;
        });
    });
  }

  if (zoneSel) {
    zoneSel.addEventListener('change', function() {
      const zoneId = this.value;
      pointSel.innerHTML = '<option value="" disabled selected>লোড হচ্ছে...</option>';
      pointSel.disabled  = true;

      fetch(`${APP_BASE}/api/points?zone_id=${zoneId}`)
        .then(r => r.json())
        .then(data => {
          pointSel.innerHTML = '<option value="" disabled selected>পয়েন্ট নির্বাচন করুন</option>';
          data.forEach(p => pointSel.innerHTML += `<option value="${p.id}">${p.name}</option>`);
          pointSel.innerHTML += `<option value="other" style="font-weight:700;color:#059669;">Other (অন্যান্য/আর্দাস)</option>`;
          pointSel.disabled = false;
        });
    });
  }
});

// ── Password Visibility Toggle Helper ─────────────────────
function togglePassVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const isPass = input.type === 'password';
  input.type = isPass ? 'text' : 'password';
  const icon = btn.querySelector('ion-icon');
  if (icon) {
    icon.setAttribute('name', isPass ? 'eye-off-outline' : 'eye-outline');
  }
}

// ── Toast Notification Helper ─────────────────────────────
function showToast(msg, type = 'info') {
  const alertBox = document.getElementById('wizard-alert');
  if (!alertBox) return;
  alertBox.className = type === 'success' ? 'success-box' : 'error-box';
  alertBox.innerHTML = `
    <ion-icon name="${type === 'success' ? 'checkmark-circle' : 'alert-circle'}" style="font-size:1.3rem;"></ion-icon>
    <span>${msg}</span>
  `;
  alertBox.style.display = 'flex';
  setTimeout(() => {
    alertBox.style.display = 'none';
  }, 4500);
}

// ── Initial State Setup ───────────────────────────────────
if (window.location.search.includes('tab=signup')) {
  switchTab('signup');
}
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
