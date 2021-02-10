<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group([
    'middleware' => 'auth'
], function () {
    Route::get('/documents', [\App\Http\Controllers\DocumentsController::class, 'index'])
        ->name('documents');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');


    Route::get('/users', [\App\Http\Controllers\UsersController::class, 'usersList'])->name('users');
    Route::get('/users/{id}', [\App\Http\Controllers\UsersController::class, 'getUserById'])->name('users.id');



    Route::group([
        'middleware' => 'admin.or.developer'
    ], function () {
        Route::get('editor', [\App\Http\Controllers\EditorController::class, 'index'])
            ->name('editor');

        Route::post('/users/manager', [\App\Http\Controllers\UsersController::class, 'createManager'])
            ->name('users.add.manager');
    });

});









