<?php

use Laravel\Lumen\Routing\Router;

define('CARDANO_CLI', '/home/' . env('LINUX_USERNAME') . '/cardano-node/bin/cardano-cli');

/** @var Router $router */
$router->post('/mint/design', 'DesignerController@mintDesign');

if (count(array_intersect(['local', 'staging'], [$router->app->environment()]))) {
    $router->get('demo', function () {
        return view('demo');
    });
}

//$router->get('/test', function() {
//
//    // exit(shell_exec(CARDANO_CLI . ' --version'));
//    // echo (shell_exec('curl http://adosia-market-tx-builder-nodejs'));
//
//    exit(config('adosia.test'));
//
//});
