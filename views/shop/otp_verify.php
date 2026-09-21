<?php
ob_start();
$base     = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$siteName = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
$purpose  = $purpose ?? 'signup';
$phone    = $phone ?? '';
$resent   = ($_GET['resent'] ?? '') === '1';
?>

<style>
  .otp-input-group { display: flex; gap: 10px; justify-content: center; margin: 24px 0; }
  .otp-digit {
    width: 52px; height: 60px; border: 2px solid #d1fae5; border-radius: 14px;
    font-size: 1.6rem; font-weight: 700; text-align: center; background: #f0fdf4;
    color: #064e3b; outline: none; transition: all .2s; caret-color: #10b981;
  }
  .otp-digit:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
  .otp-digit.filled { border-color: #10b981; background: #ecfdf5; }

  @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }
  .shake { animation: shake 0.35s ease-in-out; }

  @keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  .fade-in { animation: fadeIn .45s ease forwards; }

  .countdown { font-size: .85rem; color: #6b7280; text-align: center; }
  .countdown span { font-weight: 700; color: #10b981; }

  .otp-hero-icon {
    width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg,#d1fae5,#a7f3d0);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;
    box-shadow: 0 6px 24px rgba(16,185,129,.25);
  }
</style>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-green-50 py-10 px-4">
  <div class="w-full max-w-md fade-in">

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl border border-emerald-100 overflow-hidden">

      <!-- Top Accent Bar -->
      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-green-400 to-teal-400"></div>

      <div class="p-8 sm:p-10">

        <!-- Hero Icon -->
        <div class="otp-hero-icon">📱</div>

        <!-- Title -->
        <h1 class="text-2xl font-black text-center text-gray-900 mb-1">Verify Your Phone</h1>
        <p class="text-sm text-center text-gray-500 mb-2">
          A <?= \Core\OtpService::LENGTH ?>-digit code has been sent to
        </p>
        <p class="text-center font-bold text-emerald-700 text-base mb-6">
          <?= htmlspecialchars(substr($phone, 0, 3) . '****' . substr($phone, -3)) ?>
        </p>

        <?php if ($resent): ?>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2.5 text-xs font-bold text-emerald-700 flex items-center gap-2 mb-4">
          <ion-icon name="checkmark-circle" class="text-base"></ion-icon> OTP resent successfully!
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <?php $errMsg = [
          'invalid_otp'    => 'Incorrect or expired OTP. Please try again.',
          'missing_code'   => 'Please enter the 6-digit code.',
          'phone_mismatch' => 'Phone number mismatch. Please go back and try again.',
        ][$error] ?? 'Something went wrong. Please try again.'; ?>
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 text-xs font-bold text-red-700 flex items-center gap-2 mb-4 shake" id="errAlert">
          <ion-icon name="alert-circle" class="text-base"></ion-icon> <?= htmlspecialchars($errMsg) ?>
        </div>
        <?php endif; ?>

        <!-- DEV HELPER: show OTP from session (only if SERVER_NAME is localhost) -->
        <?php
        $devOtp = null;
        if (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])) {
            $devOtp = $_SESSION['pending_otp'] ?? null;
        }
        if ($devOtp): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 text-xs text-amber-800 flex items-center gap-2 mb-5">
          <ion-icon name="bug-outline" class="text-sm text-amber-600"></ion-icon>
          <span><strong>Dev Mode OTP:</strong> <code class="font-mono text-base tracking-widest font-black"><?= htmlspecialchars($devOtp) ?></code></span>
        </div>
        <?php endif; ?>

        <!-- OTP Form -->
        <form id="otpForm" action="<?= $base ?>/checkout/otp-verify" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
          <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">
          <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
          <input type="hidden" name="otp_code" id="hiddenOtp">

          <!-- 6 digit boxes -->
          <div class="otp-input-group" id="otpGroup">
            <?php for ($i = 0; $i < \Core\OtpService::LENGTH; $i++): ?>
            <input class="otp-digit" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                   id="d<?= $i ?>" autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>"
                   aria-label="Digit <?= $i+1 ?>" tabindex="<?= $i+1 ?>">
            <?php endfor; ?>
          </div>

          <!-- Countdown -->
          <div class="countdown mb-6" id="countdown">Code expires in <span id="timer">10:00</span></div>

          <!-- Submit -->
          <button type="submit" id="submitBtn"
                  class="w-full bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-black py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all text-sm flex items-center justify-center gap-2">
            <ion-icon name="shield-checkmark-outline" class="text-lg"></ion-icon>
            Verify &amp; Continue
          </button>
        </form>

        <!-- Resend -->
        <div class="text-center mt-5">
          <form action="<?= $base ?>/checkout/otp-resend" method="POST" class="inline">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">
            <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
            <button type="submit" id="resendBtn" disabled
                    class="text-xs font-bold text-gray-400 cursor-not-allowed transition-all" id="resendBtn">
              Resend code (<span id="resendTimer">30</span>s)
            </button>
          </form>
        </div>

        <!-- Back link -->
        <div class="text-center mt-4">
          <a href="<?= $base ?>/checkout/auth?tab=<?= $purpose === 'signup' ? 'signup' : 'login' ?>"
             class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
            ← Go back
          </a>
        </div>

      </div>
    </div>

    <!-- Brand -->
    <p class="text-center text-xs text-gray-400 mt-6 font-medium"><?= htmlspecialchars($siteName) ?> · Secure Checkout</p>
  </div>
</div>

<script>
// ── OTP Digit Navigation ─────────────────────────────────────────────────
const digits = Array.from({length: <?= \Core\OtpService::LENGTH ?>}, (_, i) => document.getElementById('d' + i));
const hidden = document.getElementById('hiddenOtp');
const form   = document.getElementById('otpForm');

function updateHidden() {
  hidden.value = digits.map(d => d.value).join('');
}

digits.forEach((input, i) => {
  input.addEventListener('input', e => {
    const v = e.data || input.value;
    if (!/^\d$/.test(v)) { input.value = ''; return; }
    input.value = v;
    input.classList.add('filled');
    updateHidden();
    if (i < digits.length - 1) digits[i + 1].focus();
  });

  input.addEventListener('keydown', e => {
    if (e.key === 'Backspace') {
      input.value = '';
      input.classList.remove('filled');
      updateHidden();
      if (i > 0) digits[i - 1].focus();
    } else if (e.key === 'ArrowLeft' && i > 0) {
      digits[i - 1].focus();
    } else if (e.key === 'ArrowRight' && i < digits.length - 1) {
      digits[i + 1].focus();
    }
  });

  input.addEventListener('paste', e => {
    e.preventDefault();
    const txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
    txt.split('').forEach((c, j) => {
      if (digits[j]) { digits[j].value = c; digits[j].classList.add('filled'); }
    });
    updateHidden();
    const next = Math.min(txt.length, digits.length - 1);
    digits[next].focus();
  });
});

form.addEventListener('submit', e => {
  if (hidden.value.length < <?= \Core\OtpService::LENGTH ?>) {
    e.preventDefault();
    document.getElementById('otpGroup').classList.add('shake');
    setTimeout(() => document.getElementById('otpGroup').classList.remove('shake'), 400);
    digits[0].focus();
  }
});

digits[0].focus();

// ── Countdown Timer ──────────────────────────────────────────────────────
let totalSeconds = 600; // 10 minutes
const timerEl   = document.getElementById('timer');
const countdownInterval = setInterval(() => {
  if (totalSeconds <= 0) {
    clearInterval(countdownInterval);
    timerEl.parentElement.textContent = 'Code has expired. Please request a new one.';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
    return;
  }
  totalSeconds--;
  const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
  const s = String(totalSeconds % 60).padStart(2, '0');
  timerEl.textContent = m + ':' + s;
}, 1000);

// ── Resend Button Cooldown ───────────────────────────────────────────────
let resendSeconds = 30;
const resendEl = document.getElementById('resendTimer');
const resendBtn = document.getElementById('resendBtn');
const resendInterval = setInterval(() => {
  resendSeconds--;
  if (resendSeconds <= 0) {
    clearInterval(resendInterval);
    resendBtn.disabled = false;
    resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
    resendBtn.classList.add('text-emerald-600', 'hover:underline', 'cursor-pointer');
    resendBtn.textContent = 'Resend OTP';
    return;
  }
  resendEl.textContent = resendSeconds;
}, 1000);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
