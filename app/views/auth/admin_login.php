<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login – TechTrack</title>
    <?php
        $fav = null;
        foreach (['favicon.ico','favicon.png'] as $f) {
            if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
        }
        if (!$fav) {
            foreach (['logo.png','logo.jpg','logo.jpeg','logo.svg'] as $f) {
                if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
            }
        }
    ?>
    <?php if ($fav): ?>
        <link rel="icon" href="<?= $fav ?>">
    <?php else: ?>
        <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <?php endif; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            display: flex;
            background: #f5f5f5;
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-header {
            position: relative;
            z-index: 1;
        }

        .brand-header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .brand-header p {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 400;
        }

        .features {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 32px;
            margin-top: 60px;
        }

        .feature-item {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-content h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .feature-content p {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .copyright {
            position: relative;
            z-index: 1;
            font-size: 13px;
            opacity: 0.8;
            margin-top: auto;
        }

        .right-panel {
            flex: 0 0 540px;
            background: white;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .login-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 48px 60px;
            overflow-y: auto;
        }

        .shield-icon {
            width: 72px;
            height: 72px;
            background: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: #6b7280;
        }

        .demo-account {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }

        .demo-account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .demo-account-header span {
            font-size: 13px;
            font-weight: 600;
            color: #1e40af;
        }

        .demo-account-header button {
            background: transparent;
            border: none;
            color: #3b82f6;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        .demo-info {
            font-size: 13px;
            color: #1e3a8a;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .password-input {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-primary:hover {
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .terms {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
            line-height: 1.5;
        }

        .terms a {
            color: #3b82f6;
            text-decoration: none;
        }

        .support-link {
            text-align: center;
            margin-top: auto;
            padding-top: 24px;
            font-size: 13px;
            color: #6b7280;
        }

        .support-link a {
            color: #3b82f6;
            text-decoration: none;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        @media (max-width: 1024px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                padding: 40px;
                min-height: 300px;
            }

            .features {
                display: none;
            }

            .right-panel {
                flex: 1;
            }
        }

        @media (max-width: 640px) {
            .login-container {
                padding: 32px 24px;
            }

            .right-panel {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <!-- Left Panel -->
    <div class="left-panel">
        <div class="brand-header">
            <h1>TechTrack</h1>
            <p>Your Ultimate Tech Store Management System</p>
        </div>

        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>For Admins</h3>
                    <p>Complete control over inventory and analytics</p>
                </div>
            </div>
        </div>

        <div class="copyright">
            © 2025 TechTrack. All rights reserved.
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <!-- Login Form -->
        <div class="login-container">
            <div class="shield-icon">
                <img src="<?= base_url() ?>public/assets/img/logo.png" alt="TechTrack Logo" style="width:100%;height:100%;object-fit:contain;">
            </div>

            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Nakakasiguro Parts ay Laging Bago</p>
            </div>

            <?php if (!empty($flash_error)): ?>
                <div class="alert error"><?= html_escape($flash_error) ?></div>
            <?php endif; ?>
            <?php if (!empty($flash_success)): ?>
                <div class="alert success"><?= html_escape($flash_success) ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="<?= site_url('login?role=admin') ?>" id="loginForm">
                <?= $csrf_field ?? '' ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@techtrack.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Sign In as Admin</button>
            </form>

            <div class="terms">
                By signing in, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>
            </div>

            <div class="support-link">
                Need help? Contact support at <a href="mailto:support@techtrack.com">support@techtrack.com</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    </script>
</body>
</html>
