<?php

use App\Core\Router;

$router = new Router();

// Product routes
$router->get('/products', 'ProductController@index');
$router->get('/products/create', 'ProductController@create');
$router->post('/products/create', 'ProductController@create');
$router->get('/products/{id}', 'ProductController@view');
$router->get('/products/{id}/edit', 'ProductController@edit');
$router->post('/products/{id}/edit', 'ProductController@edit');
$router->post('/products/{id}/delete', 'ProductController@delete');
$router->get('/products/{id}/variations/{variationId}/edit', 'ProductController@editVariation');
$router->post('/products/{id}/variations/{variationId}/edit', 'ProductController@editVariation');

// Order routes
$router->get('/orders', 'OrderController@index');
$router->get('/orders/create', 'OrderController@create');
$router->post('/orders/create', 'OrderController@create');
$router->get('/orders/{id}', 'OrderController@view');
$router->post('/orders/{id}/status', 'OrderController@updateStatus');
$router->post('/orders/webhook', 'OrderController@webhook');

// Cart routes
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/clear', 'CartController@clear');
$router->get('/cart/checkout', 'CartController@checkout');
$router->post('/cart/checkout', 'CartController@checkout');
$router->get('/cart/checkout/summary', 'CartController@checkoutSummary');
$router->post('/cart/apply-coupon', 'CartController@applyCoupon');

// Coupon routes
$router->get('/coupons', 'CouponController@index');
$router->get('/coupons/create', 'CouponController@create');
$router->post('/coupons/create', 'CouponController@create');
$router->get('/coupons/{id}/edit', 'CouponController@edit');
$router->post('/coupons/{id}/edit', 'CouponController@edit');
$router->post('/coupons/{id}/delete', 'CouponController@delete');
$router->get('/coupons/validate', 'CouponController@validate');

// Home route
$router->get('/', 'HomeController@index');

// Error handler
$router->setNotFoundHandler('ErrorController@notFound');

return $router;
