<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Account • TechTrack</title>
  <style>
    body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;background:#f8fafc;margin:0;}
    .container{max-width:520px;margin:56px auto;padding:0 16px;}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 25px rgba(2,6,23,.06);padding:24px;}
    h1{margin:0 0 8px 0;font-size:22px}
    .muted{color:#64748b;margin:0 0 16px 0}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .field{display:flex;flex-direction:column;margin-bottom:12px}
    label{font-weight:600;margin-bottom:6px}
    input{border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:14px}
    .btn{display:inline-flex;justify-content:center;align-items:center;width:100%;padding:10px 12px;background:#22c55e;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer}
    .btn:hover{background:#16a34a}
    .link{color:#2563eb;text-decoration:none}
    .alert{padding:10px 12px;border-radius:8px;margin-bottom:12px;font-size:14px}
    .alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
    .alert.ok{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
    .divider{display:flex;align-items:center;text-align:center;margin:20px 0;color:#64748b;font-size:14px;}
    .divider::before,.divider::after{content:'';flex:1;border-bottom:1px solid #e5e7eb;}
    .divider::before{margin-right:12px;}
    .divider::after{margin-left:12px;}
    .social-btn{display:flex;align-items:center;justify-content:center;width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-weight:600;cursor:pointer;margin-bottom:10px;text-decoration:none;color:#1f2937;transition:all 0.2s;}
    .social-btn:hover{background:#f9fafb;border-color:#d1d5db;}
    .social-btn svg{width:20px;height:20px;margin-right:10px;}
    .social-btn.facebook{background:#1877f2;color:white;border-color:#1877f2;}
    .social-btn.facebook:hover{background:#166fe5;}
    .social-btn.google{background:#fff;color:#1f2937;}
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Create your account</h1>
      <p class="muted">Register to track orders and get rewards.</p>
      <?php if (!empty($flash_error)): ?><div class="alert err"><?= htmlspecialchars($flash_error) ?></div><?php endif; ?>
      <?php if (!empty($flash_success)): ?><div class="alert ok"><?= htmlspecialchars($flash_success) ?></div><?php endif; ?>
      <form method="post" action="<?= site_url('shop/register') ?>">
        <?= $csrf_field ?? '' ?>
        <div class="row">
          <div class="field" style="grid-column:1 / -1">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" required />
          </div>
          <div class="field" style="grid-column:1 / -1">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required />
          </div>
        </div>
        <div class="row">
          <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required />
          </div>
          <div class="field">
            <label for="confirm">Confirm Password</label>
            <input id="confirm" name="confirm" type="password" required />
          </div>
        </div>
        <button class="btn" type="submit">Create Account</button>
      </form>
      
      <div class="divider">or</div>
      
      <a href="#" class="social-btn facebook" onclick="alert('Facebook registration coming soon!'); return false;">
        <svg fill="currentColor" viewBox="0 0 24 24">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
        <span>Sign up with Facebook</span>
      </a>
      
      <a href="<?= site_url('googleauth/login') ?>" class="social-btn google">
        <svg viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Sign up with Google</span>
      </a>
      
      <p class="muted" style="margin-top:12px">Already have an account? <a class="link" href="<?= site_url('shop/login') ?>">Sign in</a></p>
      <p style="margin-top:8px"><a class="link" href="<?= site_url('shop') ?>">← Back to Shop</a></p>
    </div>
  </div>
</body>
</html>
