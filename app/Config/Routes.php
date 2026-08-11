<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Customer\HomeController::index');
$routes->get('search', 'Customer\HomeController::search');
$routes->get('categories', 'Customer\HomeController::categories');
$routes->get('category/(:segment)', 'Customer\HomeController::category/$1');
$routes->get('product/(:segment)', 'Customer\HomeController::product/$1');
$routes->get('store/(:segment)', 'Customer\HomeController::store/$1');

$routes->get('login', 'Auth\AuthController::loginForm');
$routes->post('login', 'Auth\AuthController::login');
$routes->get('register', 'Auth\AuthController::registerForm');
$routes->post('register', 'Auth\AuthController::register');
$routes->get('logout', 'Auth\AuthController::logout');

$routes->group('', ['filter' => 'auth:customer'], static function ($routes) {
    $routes->get('cart', 'Customer\CartController::index');
    $routes->post('cart/add', 'Customer\CartController::add');
    $routes->post('cart/update', 'Customer\CartController::update');
    $routes->post('cart/remove', 'Customer\CartController::remove');

    $routes->get('checkout', 'Customer\CheckoutController::index');
    $routes->post('checkout', 'Customer\CheckoutController::process');

    $routes->get('orders', 'Customer\OrderController::index');
    $routes->get('orders/(:num)', 'Customer\OrderController::show/$1');

    $routes->get('addresses', 'Customer\AddressController::index');
    $routes->get('addresses/create', 'Customer\AddressController::create');
    $routes->post('addresses', 'Customer\AddressController::store');
    $routes->get('addresses/(:num)/edit', 'Customer\AddressController::edit/$1');
    $routes->post('addresses/(:num)', 'Customer\AddressController::update/$1');
    $routes->post('addresses/(:num)/delete', 'Customer\AddressController::delete/$1');
});

$routes->group('admin', ['filter' => 'auth:super_admin,government_admin'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('statistics', 'Admin\StatisticsController::index');
    $routes->get('reports', 'Admin\ReportsController::index');

    $routes->get('homepage', 'Admin\CmsController::homepage');
    $routes->get('banners', 'Admin\CmsController::banners');
    $routes->post('banners', 'Admin\CmsController::saveBanner');
    $routes->post('banners/(:num)/delete', 'Admin\CmsController::deleteBanner/$1');
    $routes->post('banners/(:num)/duplicate', 'Admin\CmsController::duplicateBanner/$1');
    $routes->post('banners/(:num)/toggle', 'Admin\CmsController::toggleBanner/$1');
    $routes->get('featured-products', 'Admin\CmsController::featuredProducts');
    $routes->post('featured-products', 'Admin\CmsController::saveFeaturedProducts');
    $routes->get('featured-stores', 'Admin\CmsController::featuredStores');
    $routes->post('featured-stores', 'Admin\CmsController::saveFeaturedStores');
    $routes->get('settings', 'Admin\CmsController::settings');
    $routes->post('settings', 'Admin\CmsController::saveSettings');
});

$routes->group('umkm', ['filter' => 'auth:umkm'], static function ($routes) {
    $routes->get('dashboard', 'Umkm\DashboardController::index');
    $routes->get('products', 'Umkm\SellerController::products');
    $routes->get('products/create', 'Umkm\SellerController::createProduct');
    $routes->post('products', 'Umkm\SellerController::storeProduct');
    $routes->get('products/(:num)/edit', 'Umkm\SellerController::editProduct/$1');
    $routes->post('products/(:num)', 'Umkm\SellerController::updateProduct/$1');
    $routes->post('products/(:num)/delete', 'Umkm\SellerController::deleteProduct/$1');
    $routes->post('products/(:num)/toggle', 'Umkm\SellerController::toggleProduct/$1');
    $routes->get('orders', 'Umkm\SellerController::orders');
    $routes->post('orders/(:num)/status', 'Umkm\SellerController::updateOrderStatus/$1');
    $routes->get('reports', 'Umkm\SellerController::reports');
    $routes->get('store', 'Umkm\SellerController::storeProfile');
    $routes->post('store', 'Umkm\SellerController::saveStoreProfile');
});

$routes->group('courier', ['filter' => 'auth:courier'], static function ($routes) {
    $routes->get('dashboard', 'Courier\DashboardController::index');
    $routes->post('shipments/(:num)/accept', 'Courier\DashboardController::accept/$1');
    $routes->post('status', 'Courier\DashboardController::toggleStatus');
    $routes->post('shipments/(:num)/pickup', 'Courier\DashboardController::pickup/$1');
    $routes->post('shipments/(:num)/delivery', 'Courier\DashboardController::onDelivery/$1');
    $routes->post('shipments/(:num)/complete', 'Courier\DashboardController::complete/$1');
});
