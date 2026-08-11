<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Customer\HomeController::index');
$routes->get('search', 'Customer\HomeController::search');
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
