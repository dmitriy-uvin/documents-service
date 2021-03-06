<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['can.use.api']
], function () {
    Route::prefix('v1')->group(function () {
        Route::get(
            '/{apiKey}/mode-person',
            [
                \App\Http\Controllers\Api\IndividualsController::class,
                'getDocumentsPersonMode'
            ]
        );
        Route::post(
            '/{apiKey}/mode-person/collection',
            [
                \App\Http\Controllers\Api\IndividualsController::class,
                'getDocumentsPersonModeCollection'
            ]
        );

        Route::get(
            '/{apiKey}/mode-document',
            [
                \App\Http\Controllers\Api\IndividualsController::class,
                'getDocumentsDocumentMode'
            ]
        );
        Route::post(
            '/{apiKey}/mode-document/collection',
            [
                \App\Http\Controllers\Api\IndividualsController::class,
                'getDocumentsDocumentModeCollection'
            ]
        );
    });
});
