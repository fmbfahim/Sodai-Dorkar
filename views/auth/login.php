<?php
$base     = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$siteName = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – <?= htmlspecialchars($siteName) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    *, body, button, input { font-family: 'Outfit', 'Hind Siliguri', sans-serif; }

    .login-bg {
      min-height: 100vh;
      background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 70%, #059669 100%);
      display: flex; align-items: center; justify-content: center;
      padding: 1.5rem; position: relative; overflow: hidden;
    }
    .login-bg::before {
      content: '';
      position: absolute; width: 600px; height: 600px; border-radius: 50%;
      background: rgba(255,255,255,.04); top: -200px; right: -200px;
    }
    .login-bg::after {
      content: '';
      position: absolute; width: 400px; height: 400px; border-radius: 50%;
      background: rgba(255,255,255,.03); bottom: -150px; left: -100px;
    }

    .login-card {
      background: rgba(255,255,255,.97);
      border-radius: 28px;
      width: 100%; max-width: 420px;
      box-shadow: 0 32px 80px rgba(0,0,0,.3), 0 8px 24px rgba(0,0,0,.15);
      overflow: hidden; position: relative; z-index: 1;
    }

    .card-header {
      background: linear-gradient(135deg, #064e3b, #065f46);
      padding: 2rem 2rem 1.5rem;
      text-align: center; position: relative;
    }
    .card-header::after {
      content: '';
      position: absolute; bottom: -16px; left: 50%; transform: translateX(-50%);
      width: 48px; height: 48px; border-radius: 50%;
      background: #fff; display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }

    .admin-avatar {
      width: 64px; height: 64px; border-radius: 50%;
      background: rgba(255,255,255,.15); border: 3px solid rgba(255,255,255,.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.8rem; margin: 0 auto 12px; backdrop-filter: blur(4px);
    }

    .card-body { padding: 2rem; }

    .field-group { margin-bottom: 18px; }
    .field-label {
      display: block; font-size: .75rem; font-weight: 700;
      color: #374151; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 7px;
    }
    .field-wrap { position: relative; }
    .field-icon {
      position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
      color: #9ca3af; font-size: 1rem; pointer-events: none;
    }
    .field-input {
      width: 100%; padding: 12px 16px 12px 40px;
      border: 2px solid #e5e7eb; border-radius: 12px;
      font-size: .9rem; color: #111827; background: #f9fafb;
      outline: none; transition: all .2s;
      box-sizing: border-box;
    }
    .field-input:focus { border-color: #059669; background: #fff; box-shadow: 0 0 0 3px rgba(5,150,105,.12); }
    .field-input::placeholder { color: #9ca3af; }

    .btn-login {
      width: 100%; padding: 13px 20px;
      background: linear-gradient(135deg, #059669, #10b981);
      color: #fff; font-weight: 800; font-size: .95rem;
      border: none; border-radius: 12px; cursor: pointer;
      transition: all .25s;
      box-shadow: 0 6px 20px rgba(5,150,105,.3);
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #047857, #059669);
      transform: translateY(-1px); box-shadow: 0 10px 28px rgba(5,150,105,.35);
    }
    .btn-login:active { transform: translateY(0); }

    .error-alert {
      background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
      padding: 10px 14px; margin-bottom: 16px;
      display: flex; align-items: center; gap: 8px;
      font-size: .82rem; font-weight: 600; color: #dc2626;
    }

    @keyframes fadeSlide { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
    .login-card { animation: fadeSlide .5s ease; }
  </style>
</head>
<body>

<div class="login-bg">
  <div class="login-card">

    <!-- Header -->
    <div class="card-header">
      <div class="admin-avatar">🛒</div>
      <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 4px;">
        <?= htmlspecialchars($siteName) ?>
      </h1>
      <p style="font-size:.8rem;color:rgba(255,255,255,.65);margin:0;">Admin Panel – Secure Login</p>
    </div>

    <!-- Body -->
    <div class="card-body" style="padding-top: 2.5rem;">

      <?php if (isset($error)): ?>
      <div class="error-alert">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        <?php echo htmlspecialchars($error); ?>
      </div>
      <?php endif; ?>

      <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

        <div class="field-group">
          <label class="field-label" for="username">Username</label>
          <div class="field-wrap">
            <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            <input class="field-input" id="username" name="username" type="text" placeholder="Enter username" required autocomplete="username">
          </div>
        </div>

        <div class="field-group" style="margin-bottom:24px;">
          <label class="field-label" for="password">Password</label>
          <div class="field-wrap">
            <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            <input class="field-input" id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password">
          </div>
        </div>

        <button class="btn-login" type="submit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
          Sign In to Admin Panel
        </button>
      </form>

    </div>

    <div style="text-align:center;padding:1rem 2rem 1.5rem;border-top:1px solid #f3f4f6;">
      <p style="font-size:.73rem;color:#9ca3af;margin:0;">
        🔒 This page is restricted to authorized personnel only.
      </p>
    </div>

  </div>
</div>

</body>
</html>
