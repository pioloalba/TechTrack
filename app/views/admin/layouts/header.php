<?php
defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechTrack Admin</title>
    <?php
        $fav = null;
        foreach (['favicon.ico','favicon.png'] as $f) {
            $p = ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f;
            if (is_file($p)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
        }
        if (!$fav) {
            // fall back to logo as favicon if exists
            foreach (['logo.png','logo.jpg','logo.jpeg','logo.svg'] as $f) {
                $p = ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f;
                if (is_file($p)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
            }
        }
    ?>
    <?php if ($fav): ?>
        <link rel="icon" href="<?= $fav ?>">
    <?php else: ?>
        <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <?php endif; ?>
    <!-- Tailwind placeholder -->
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/css/tailwind.css">
    <!-- jQuery (CDN with local fallback) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="<?= base_url() ?>public/assets/js/jquery.min.js"><\\/script>');</script>
    <script src="<?= base_url() ?>public/assets/js/admin.js" defer></script>
    <style>
        :root {
            --blue: #3B82F6;
            --deep: #1E40AF;
            --amber: #F59E0B;
            --red: #EF4444;
            --gray: #f1f5f9;
            --sidebar-bg: #1E293B;
        }

        body {
            margin: 0;
            font-family: Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: #F9FAFB;
            color: #111827
        }

        .layout {
            display: flex;
            min-height: 100vh
        }

        .sidebar {
            width: 256px;
            background: var(--sidebar-bg);
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            left: 256px;
            right: 0;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 10;
        }

        .content {
            margin-left: 256px;
            margin-top: 64px;
            padding: 24px;
            flex: 1;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand strong { font-size: 18px; font-weight: 700; }
        .brand img { height: 36px; width: auto; display: block; }

        .nav {
            flex: 1;
            padding: 8px 0;
            overflow-y: auto;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.2s;
        }

        .nav a:hover {
            background: rgba(59,130,246,0.1);
            color: #fff;
        }

        .nav a.active {
            background: #3B82F6;
            color: #fff;
        }

        .nav a svg {
            width: 20px;
            height: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-footer .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3B82F6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .sidebar-footer .info {
            flex: 1;
        }

        .sidebar-footer .info .name {
            font-weight: 600;
            font-size: 14px;
        }

        .sidebar-footer .info .email {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px
        }

        .badge.green {
            background: #DCFCE7;
            color: #166534
        }

        .badge.amber {
            background: #FEF3C7;
            color: #92400E
        }

        .badge.red {
            background: #FEE2E2;
            color: #991B1B
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            padding: 16px
        }

        .grid {
            display: grid;
            gap: 16px
        }

        .grid.cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr))
        }

        @media(max-width:1024px) {
            .grid.cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:640px) {
            .grid.cols-4 {
                grid-template-columns: 1fr
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            text-decoration: none
        }

        .btn.primary {
            background: var(--blue);
            border-color: var(--blue);
            color: #fff
        }

        .table {
            width: 100%;
            border-collapse: collapse
        }

        .table th,
        .table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left
        }

        /* Dashboard specific styles */
        .dashboard-greeting h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
        }

        .dashboard-greeting p {
            color: #6B7280;
            margin-top: 4px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-label {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .stat-sublabel {
            font-size: 12px;
            color: #9CA3AF;
        }

        .stat-trend {
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .stat-trend.positive {
            background: #DCFCE7;
            color: #166534;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-bar svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .notification-bell {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .notification-bell .badge-count {
            position: absolute;
            top: 0;
            right: 0;
            background: #EF4444;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3B82F6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="layout">