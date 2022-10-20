<?php

use Kernel\Router\Route;
use Kernel\Router\Routes;

Routes::controller('HomeController', cb: function() {
    Route::get('/', 'index');
});

// Route::get('/users', 'UserController::users');

Routes::controller('EntryController', '/api/entry', function() {
    Route::put('/register', 'register');
});