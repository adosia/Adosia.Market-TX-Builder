<?php

use Laravel\Lumen\Routing\Router;

define('CARDANO_CLI', '/home/' . env('LINUX_USERNAME') . '/cardano-node/bin/cardano-cli');

/** @var Router $router */
$router->post('/mint/design', 'DesignerController@mintDesign');
$router->post('/mint/update', 'DesignerController@mintUpdate');

if (count(array_intersect(['local', 'staging'], [$router->app->environment()]))) {
    $router->get('demo', function () { return view('demo'); });
}
