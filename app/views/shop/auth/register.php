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
      <p class="muted" style="margin-top:12px">Already have an account? <a class="link" href="<?= site_url('shop/login') ?>">Sign in</a></p>
      <p style="margin-top:8px"><a class="link" href="<?= site_url('shop') ?>">← Back to Shop</a></p>
    </div>
  </div>
</body>
</html>
