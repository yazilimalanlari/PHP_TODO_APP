<?php

use Kernel\Router\Route;
use Kernel\Router\Routes;

Route::get('/', 'TodoController::home');

// Entry
Routes::controller('EntryController', '/api/entry', function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::get('/register', 'EntryController::registerShow');
Route::get('/login', 'EntryController::loginShow');
Route::get('/logout', 'EntryController::logout');

// Todo
Routes::auth()::controller('TodoController', '/api/todo', function() {
    Route::post('/', 'create');
    Route::put('/:id([0-9]+)', 'update');
    Route::put('/complete/:id([0-9]+)', 'setComplete');
    Route::delete('/:id([0-9]+)', 'delete');
});