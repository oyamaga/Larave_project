<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('auth/users', AdminUserController::class);
    // $router->resource('auth/upload', CsvImport::class);
    // $router->get('auth/users/{user}', 'AdminUserController@form2');
    // $router->post('auth/import', 'TestUserController@csvImport');
    $router->resource('auth/test', TestUserController::class);
});
