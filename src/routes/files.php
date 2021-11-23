<?php

use Dthrcrpz\FileLibrary\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::post('files', [FileController::class, 'store']);
    Route::patch('files/{fileModel}', [FileController::class, 'update']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);
});