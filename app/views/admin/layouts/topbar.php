<?php
defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed');
?>
<div class="topbar">
    <div class="search-bar">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Search products, orders, customers…">
    </div>
    <div class="topbar-actions">
        <div class="notification-bell">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="badge-count">3</span>
        </div>
        <div class="profile-icon" title="Account">A</div>
        <a href="<?= site_url('logout') ?>" style="margin-left:12px; padding:8px 12px; border:1px solid #E5E7EB; border-radius:8px; color:#111827; text-decoration:none; font-size:14px;">Logout</a>
    </div>
</div>
<div class="content">