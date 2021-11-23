<?php

use Dthrcrpz\FileLibrary\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::post('files', [FileController::class, 'store']);
    Route::get('files/images', [FileController::class, 'images']);
    Route::patch('files/images/{image}', [FileController::class, 'updateImage']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);
});