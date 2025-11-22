<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
|
*/

$router->get('/', 'Shop::index');
$router->get('/login', 'Auth::index');
$router->get('/admin-login', 'Auth::admin_login');
$router->get('/cashier-login', 'Auth::cashier_login');
$router->match('/login', 'Auth::login', ['post']);
$router->get('/logout', 'Auth::logout');

// TechTrack Admin Routes
$router->group('/admin', function() use ($router) {
	$router->get('/', 'AdminDashboard::index');
	$router->get('/dashboard', 'AdminDashboard::index');

	// Products
	$router->get('/products', 'AdminProducts::index');
	$router->get('/products/create', 'AdminProducts::create');
	$router->match('/products/store', 'AdminProducts::store', ['post']);
	$router->get('/products/get/{id}', 'AdminProducts::get');
	$router->get('/products/edit/{id}', 'AdminProducts::edit');
	$router->match('/products/update/{id}', 'AdminProducts::update', ['post']);
	$router->match('/products/delete/{id}', 'AdminProducts::delete', ['post']);

	// Product images/specs management
	$router->get('/products/images/{id}', 'AdminProducts::images');
	$router->match('/products/add-image/{id}', 'AdminProducts::add_image', ['post']);
	$router->match('/products/upload-image/{id}', 'AdminProducts::upload_image', ['post']);
	$router->match('/products/delete_image/{image_id}', 'AdminProducts::delete_image', ['post']);
	$router->get('/products/specs/{id}', 'AdminProducts::specs');
	$router->match('/products/add-spec/{id}', 'AdminProducts::add_spec', ['post']);
	$router->get('/products/delete-spec/{spec_id}', 'AdminProducts::delete_spec');

	// Inventory
	$router->get('/inventory', 'AdminInventory::index');
	$router->match('/inventory/adjust', 'AdminInventory::adjust', ['post']);

	// POS
	$router->get('/pos', 'AdminPOS::index');

	// Orders
	$router->get('/orders', 'AdminOrders::index');
	$router->get('/orders/view/{id}', 'AdminOrders::view');
	$router->match('/orders/update-status/{id}', 'AdminOrders::update_status', ['post']);

	// Customers
	$router->get('/customers', 'AdminCustomers::index');
	$router->get('/customers/create', 'AdminCustomers::create');
	$router->match('/customers/store', 'AdminCustomers::store', ['post']);
	$router->get('/customers/view/{id}', 'AdminCustomers::view');
	$router->get('/customers/edit/{id}', 'AdminCustomers::edit');
	$router->match('/customers/update/{id}', 'AdminCustomers::update', ['post']);
	$router->match('/customers/delete/{id}', 'AdminCustomers::delete', ['post']);

	// Reports, Alerts, Settings
	$router->get('/reports', 'AdminReports::index');
	$router->get('/reports/export_pdf', 'AdminReports::export_pdf');
	$router->get('/alerts', 'AdminAlerts::index');
	$router->get('/settings', 'AdminSettings::index');
	$router->match('/settings/update', 'AdminSettings::update', ['post']);

	// Dev migration trigger (DISABLED IN PRODUCTION)
	// $router->get('/migrate', 'DevMigrate::migrate');
    // Dev seeder trigger (DISABLED IN PRODUCTION)
    // $router->get('/seed', 'DevMigrate::seed');
});

// Public shop routes
$router->get('/shop', 'Shop::index');
$router->get('/shop/desktops', 'Shop::desktops');
$router->get('/shop/laptops', 'Shop::laptops');
$router->get('/shop/build-pc', 'Shop::build_pc');
$router->get('/shop/rewards', 'Shop::rewards');
$router->get('/product/{id}', 'Shop::product');
$router->get('/checkout', 'Shop::checkout');
$router->post('/checkout/place-order', 'Shop::place_order');
$router->get('/track/{order_id}', 'Shop::track_order');

// Cart operations
$router->match('/shop/add-to-cart', 'Shop::add_to_cart', ['post']);
$router->match('/shop/get-cart', 'Shop::get_cart', ['get', 'post']);
$router->match('/shop/update-cart', 'Shop::update_cart', ['post']);
$router->match('/shop/remove-from-cart', 'Shop::remove_from_cart', ['post']);
$router->match('/shop/clear-cart', 'Shop::clear_cart', ['post']);
// DEBUG ENDPOINT DISABLED IN PRODUCTION
// $router->match('/shop/debug-cart', 'Shop::debug_cart', ['get']);

// Save customer location (AJAX)
$router->match('/shop/save-location', 'Shop::save_location', ['post']);

// Customer authentication (shop)
$router->get('/shop/login', 'CustomerAuth::login');
$router->match('/shop/login', 'CustomerAuth::do_login', ['post']);
$router->get('/shop/register', 'CustomerAuth::register');
$router->match('/shop/register', 'CustomerAuth::do_register', ['post']);
$router->get('/shop/logout', 'CustomerAuth::logout');

// API endpoints for React frontend
$router->group('/api', function() use ($router) {
	$router->get('/products', 'ApiProducts::index');
	$router->get('/products/{id}', 'ApiProducts::show');
	$router->match('/products', 'ApiProducts::store', ['post']);
	$router->match('/products/{id}', 'ApiProducts::update', ['post']);
	$router->get('/products/delete/{id}', 'ApiProducts::delete');

	$router->get('/orders', 'ApiOrders::index');
	$router->get('/orders/{id}', 'ApiOrders::show');
	$router->match('/orders', 'ApiOrders::store', ['post']);
});