<?php

use Illuminate\Support\Facades\Route;
use Dthrcrpz\FileLibrary\Http\Controllers\FileController;
use Dthrcrpz\FileLibrary\Http\Controllers\FileAttachmentController;

Route::group(['prefix' => 'api'], function () {
    Route::post('files', [FileController::class, 'store']);
    Route::patch('files/{file_model}', [FileController::class, 'update']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);
    Route::delete('file-attachments/{file_attachment}', [FileAttachmentController::class, 'destroy']);
});