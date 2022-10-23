<?php

use Laravel\Lumen\Routing\Router;

define('CARDANO_CLI', '/home/' . env('LINUX_USERNAME') . '/cardano-node/bin/cardano-cli');

/** @var Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test', function() {

    dd(shell_exec(CARDANO_CLI . ' --version'));
    // dd(shell_exec('curl http://adosia-market-tx-builder-nodejs'));

});
