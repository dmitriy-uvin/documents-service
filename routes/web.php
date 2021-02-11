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
    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history');
    Route::get('/tasks', [\App\Http\Controllers\TasksController::class, 'index'])->name('tasks');

    Route::get('/individuals', [\App\Http\Controllers\IndividualsController::class, 'individualsView'])
        ->name('individuals.view');

    Route::get('/individuals/all', [\App\Http\Controllers\IndividualsController::class, 'getIndividuals']);

    Route::get('/documents', [\App\Http\Controllers\DocumentsController::class, 'index'])
        ->name('documents');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');

    Route::get('/users', [\App\Http\Controllers\UsersController::class, 'usersList'])->name('users.view');
    Route::get('/users/all', [\App\Http\Controllers\UsersController::class, 'getAllUsers'])->name('users.all');
    Route::get('/users/{id}', [\App\Http\Controllers\UsersController::class, 'getUserById'])
        ->name('users.id');

    Route::post('/documents/upload', [\App\Http\Controllers\DocumentsController::class, 'uploadDocuments'])
        ->name('documents.upload');

    Route::group([
        'middleware' => 'admin.or.developer'
    ], function () {
        Route::get('editor', [\App\Http\Controllers\EditorController::class, 'index'])
            ->name('editor');

        Route::post('/users', [\App\Http\Controllers\UsersController::class, 'createUser'])
            ->name('users.add');

        Route::delete('/users/{id}', [\App\Http\Controllers\UsersController::class, 'deleteUser'])
            ->name('users.delete');

        Route::put('/users/block/{id}', [\App\Http\Controllers\UsersController::class, 'changeUserBlockStatus'])
            ->name('users.block');
    });
});









