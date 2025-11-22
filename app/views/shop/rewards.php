<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards Program - TechTrack</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F9FAFB;
            color: #111827;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            color: #fff;
            padding: 48px 0;
            text-align: center;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 24px;
            opacity: 0.9;
            transition: opacity 0.2s;
        }

        .back-link:hover { opacity: 1; }

        .header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Container */
        .container {
            max-width: 1280px;
            margin: -40px auto 0;
            padding: 0 24px 48px;
            position: relative;
        }

        /* Points Card */
        .points-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 32px;
            align-items: center;
        }

        .points-info h2 {
            font-size: 16px;
            font-weight: 600;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .points-value {
            font-size: 48px;
            font-weight: 700;
            color: #3B82F6;
            margin-bottom: 8px;
        }

        .tier-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .tier-bronze { background: #FEF3C7; color: #92400E; }
        .tier-silver { background: #E5E7EB; color: #374151; }
        .tier-gold { background: #FEF9C3; color: #854D0E; }
        .tier-platinum { background: #E0E7FF; color: #3730A3; }

        .points-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: #3B82F6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563EB;
        }

        .btn-outline {
            background: white;
            color: #3B82F6;
            border: 2px solid #3B82F6;
        }

        .btn-outline:hover {
            background: #EFF6FF;
        }

        /* Section Headers */
        .section {
            margin-bottom: 48px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .section-header h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section-header p {
            font-size: 16px;
            color: #6B7280;
        }

        /* Tier Cards */
        .tiers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .tier-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .tier-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .tier-card.active {
            border-color: #3B82F6;
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
        }

        .tier-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .tier-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .tier-requirement {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 20px;
        }

        .tier-benefits {
            text-align: left;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .tier-benefits h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #374151;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 13px;
            color: #6B7280;
        }

        .benefit-item svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* How It Works */
        .how-it-works {
            background: white;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 32px;
            margin-top: 32px;
        }

        .step {
            text-align: center;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            margin: 0 auto 16px;
        }

        .step h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .step p {
            font-size: 14px;
            color: #6B7280;
            line-height: 1.6;
        }

        /* FAQ */
        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .faq-item {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #E5E7EB;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .faq-question {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #111827;
        }

        .faq-answer {
            font-size: 15px;
            color: #6B7280;
            line-height: 1.6;
        }

        /* Login Prompt */
        .login-prompt {
            background: white;
            border-radius: 12px;
            padding: 48px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
        }

        .login-prompt-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .login-prompt h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-prompt p {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 32px; }
            .points-card {
                grid-template-columns: 1fr;
                padding: 24px;
            }
            .points-value { font-size: 36px; }
            .how-it-works, .faq-section { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-container">
            <a class="back-link" href="<?= site_url('shop') ?>">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Shop
            </a>
            <h1>🎁 TechTrack Rewards</h1>
            <p>Earn points with every purchase and unlock exclusive benefits</p>
        </div>
    </div>

    <div class="container">
        <?php if ($user): ?>
        <!-- User Points Card -->
        <div class="points-card">
            <div class="points-info">
                <h2>Your Points Balance</h2>
                <div class="points-value"><?= number_format($points) ?></div>
                <span class="tier-badge tier-<?= strtolower($tier) ?>">
                    <?php
                        $tierIcons = [
                            'Bronze' => '🥉',
                            'Silver' => '🥈',
                            'Gold' => '🥇',
                            'Platinum' => '💎'
                        ];
                    ?>
                    <?= $tierIcons[$tier] ?> <?= $tier ?> Member
                </span>
            </div>
            <div class="points-actions">
                <a href="<?= site_url('shop') ?>" class="btn btn-primary">Shop Now</a>
                <a href="#how-it-works" class="btn btn-outline">How It Works</a>
            </div>
        </div>
        <?php else: ?>
        <!-- Login Prompt -->
        <div class="login-prompt">
            <div class="login-prompt-icon">🔐</div>
            <h3>Sign In to View Your Rewards</h3>
            <p>Log in to your account to track your points and redeem exclusive benefits</p>
            <a href="<?= site_url('customer/login') ?>" class="btn btn-primary">Sign In</a>
            <p style="margin-top: 16px; font-size: 14px;">
                Don't have an account? 
                <a href="<?= site_url('customer/register') ?>" style="color: #3B82F6; font-weight: 600;">Create one</a>
            </p>
        </div>
        <?php endif; ?>

        <!-- Membership Tiers -->
        <div class="section">
            <div class="section-header">
                <h2>Membership Tiers</h2>
                <p>Progress through tiers and unlock better rewards</p>
            </div>

            <div class="tiers-grid">
                <div class="tier-card <?= $tier === 'Bronze' ? 'active' : '' ?>">
                    <div class="tier-icon">🥉</div>
                    <div class="tier-name">Bronze</div>
                    <div class="tier-requirement">0 - 999 points</div>
                    <div class="tier-benefits">
                        <h4>Benefits:</h4>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Earn 1 point per ₱100 spent</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Birthday surprise gift</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Exclusive member-only deals</span>
                        </div>
                    </div>
                </div>

                <div class="tier-card <?= $tier === 'Silver' ? 'active' : '' ?>">
                    <div class="tier-icon">🥈</div>
                    <div class="tier-name">Silver</div>
                    <div class="tier-requirement">1,000 - 2,499 points</div>
                    <div class="tier-benefits">
                        <h4>Benefits:</h4>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Earn 1.5 points per ₱100 spent</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>5% discount on all purchases</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Priority customer support</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Early access to sales</span>
                        </div>
                    </div>
                </div>

                <div class="tier-card <?= $tier === 'Gold' ? 'active' : '' ?>">
                    <div class="tier-icon">🥇</div>
                    <div class="tier-name">Gold</div>
                    <div class="tier-requirement">2,500 - 4,999 points</div>
                    <div class="tier-benefits">
                        <h4>Benefits:</h4>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Earn 2 points per ₱100 spent</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>10% discount on all purchases</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Free shipping on all orders</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Exclusive Gold events access</span>
                        </div>
                    </div>
                </div>

                <div class="tier-card <?= $tier === 'Platinum' ? 'active' : '' ?>">
                    <div class="tier-icon">💎</div>
                    <div class="tier-name">Platinum</div>
                    <div class="tier-requirement">5,000+ points</div>
                    <div class="tier-benefits">
                        <h4>Benefits:</h4>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Earn 3 points per ₱100 spent</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>15% discount on all purchases</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>VIP customer support</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Exclusive product previews</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Personal account manager</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="section" id="how-it-works">
            <div class="how-it-works">
                <div class="section-header">
                    <h2>How It Works</h2>
                    <p>Start earning rewards in 3 simple steps</p>
                </div>

                <div class="steps-grid">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>Shop & Earn</h3>
                        <p>Make purchases and automatically earn points based on your tier level</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>Track Progress</h3>
                        <p>Monitor your points balance and see how close you are to the next tier</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>Redeem Rewards</h3>
                        <p>Use your points for discounts, free shipping, and exclusive perks</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="section">
            <div class="faq-section">
                <div class="section-header">
                    <h2>Frequently Asked Questions</h2>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How do I join the rewards program?</div>
                    <div class="faq-answer">
                        Simply create an account or log in. You'll automatically be enrolled in our rewards program and start earning points with your first purchase!
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How many points do I earn per purchase?</div>
                    <div class="faq-answer">
                        Points earned depend on your tier: Bronze (1 point/₱100), Silver (1.5 points/₱100), Gold (2 points/₱100), and Platinum (3 points/₱100).
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Do my points expire?</div>
                    <div class="faq-answer">
                        Points are valid for 12 months from the date they are earned. Keep shopping to maintain your points balance!
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How do I redeem my points?</div>
                    <div class="faq-answer">
                        Your tier benefits are automatically applied at checkout. Higher tiers receive percentage discounts on all purchases, plus additional perks like free shipping.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Can I lose my tier status?</div>
                    <div class="faq-answer">
                        Tier status is reviewed annually. To maintain your tier, you need to maintain the minimum points balance through continued purchases.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
