<?php

use Laravel\Lumen\Routing\Router;

define('CARDANO_CLI', '/home/' . env('LINUX_USERNAME') . '/cardano-node/bin/cardano-cli');
define('BECH32', '/home/' . env('LINUX_USERNAME') . '/cardano-node/bin/bech32');

/** @var Router $router */
$router->group(['prefix' => 'auth'], function() use($router) {
    $router->post('validate/signature', 'AuthController@validateSignature');
});

/** @var Router $router */
$router->group(['prefix' => 'designer'], function() use($router) {
    $router->post('mint/design', 'DesignerController@mintDesign');
    $router->post('mint/update', 'DesignerController@mintUpdate');
});

/** @var Router $router */
$router->group(['prefix' => 'customer'], function() use($router) {
    $router->post('purchase-order/print-design', 'CustomerController@purchaseOrderPrintDesign');
    $router->post('purchase-order/remove', 'CustomerController@purchaseOrderRemove');
    $router->post('purchase-order/add', 'CustomerController@purchaseOrderAdd');
    $router->post('purchase-order/accept-offer', 'CustomerController@purchaseOrderAcceptOffer');
});

/** @var Router $router */
$router->group(['prefix' => 'printer-operator'], function() use($router) {
    $router->post('purchase-order/make-offer', 'PrinterOperatorController@purchaseOrderMakeOffer');
    $router->post('purchase-order/remove-offer', 'PrinterOperatorController@purchaseOrderRemoveOffer');
    $router->post('purchase-order/set-shipped', 'PrinterOperatorController@purchaseOrderSetShipped');
});

// TEST ROUTE
if (count(array_intersect(['local', 'staging'], [$router->app->environment()]))) {
    $router->get('demo', function () { return view('demo'); });
}
