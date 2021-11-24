<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\FileLib\Facades\FileLib;
use Dthrcrpz\FileLibrary\Models\File;

class FileController extends Controller
{
    public function store (Request $r) {
        $uploadedFile = FileLib::uploadFile($r);

        if (!$uploadedFile->success) {
            return response([
                'errors' => $uploadedFile->errorMessage
            ], $uploadedFile->statusCode);
        }

        return response([
            'file' => $uploadedFile->file
        ]);
    }

    public function update ($file_model, Request $r) {
        $updatedFile = FileLib::updateFile($file_model, $r);

        if (!$updatedFile->success) {
            return response([
                'errors' => $updatedFile->errorMessage
            ], $updatedFile->statusCode);
        }

        return response([
            'file' => $updatedFile->file
        ]);
    }

    public function destroy ($file) {
        $file = File::find($file);

        if (!$file) {
            return response([
                'errors' => [
                    'File not found'
                ]
            ], 404);
        }

        $file->delete();

        return response([
            'message' => 'File deleted'
        ]);
    }
}
