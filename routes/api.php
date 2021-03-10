<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/mode-person', [\App\Http\Controllers\Api\IndividualsController::class, 'getDocumentsPersonMode']);
    Route::get('/mode-document', [\App\Http\Controllers\Api\IndividualsController::class, 'getDocumentsDocumentMode']);
});
