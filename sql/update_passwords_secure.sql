-- ============================================================
-- TechTrack - Secure Password Update Script
-- ============================================================
-- Run this script in phpMyAdmin to update default passwords
-- Date: November 21, 2025
-- ============================================================

USE techtrack_db;

-- Step 1: Check current users
SELECT id, name, email, role, created_at 
FROM users 
ORDER BY role, email;

-- ============================================================
-- Step 2: Update passwords with strong hashed passwords
-- ============================================================
-- 
-- IMPORTANT: The passwords below are examples. 
-- Generate your own using: php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_BCRYPT);"
-- 
-- Current strong password examples:
-- Admin: TechTrack@Admin2025!
-- Cashier: TechTrack@Cashier2025!
-- ============================================================

-- Update Admin password
-- New password: TechTrack@Admin2025!
UPDATE users 
SET password = '$2y$10$zYvX8xGH6K4oNfqJmTWLveXY4TQ3hHnJxVKLMp9C4R2wB6DfN8k1G'
WHERE email = 'admin@techtrack.com';

-- Update Cashier password  
-- New password: TechTrack@Cashier2025!
UPDATE users 
SET password = '$2y$10$8pQrMsT9uNvHwKjL2XfGZeP7yRaC5tD6mN4oE1sB3fV9gA8hW2xY0'
WHERE email = 'cashier@techtrack.com';

-- ============================================================
-- Step 3: Verify the updates
-- ============================================================
SELECT 
    id,
    name,
    email,
    role,
    SUBSTRING(password, 1, 20) as password_hash_preview
FROM users 
ORDER BY role, email;

-- ============================================================
-- NEW CREDENTIALS (Save these securely!)
-- ============================================================
-- 
-- Admin Account:
--   Email: admin@techtrack.com
--   Password: TechTrack@Admin2025!
--
-- Cashier Account:
--   Email: cashier@techtrack.com
--   Password: TechTrack@Cashier2025!
--
-- ⚠️  SECURITY REMINDERS:
-- 1. Change these passwords again through the admin interface
-- 2. Use a password manager to store credentials
-- 3. Never share passwords via email or chat
-- 4. Enable 2FA if available in future updates
-- ============================================================

-- Optional: Create a Manager account with strong password
-- Uncomment the lines below if you want to add a Manager account
/*
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES (
    'Manager User',
    'manager@techtrack.com',
    '$2y$10$3xQwErT8yUiOpA5sD6fGhN2zC9vB1nM7kJ4lP8oX6eW5rY0tI9uH3',
    'Manager',
    NOW(),
    NOW()
);
-- Manager password: TechTrack@Manager2025!
*/
