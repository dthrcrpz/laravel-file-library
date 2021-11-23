<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\Models\File;
use Illuminate\Support\Facades\Validator;
use Dthrcrpz\FileLibrary\Services\FilesHelper;

class FileController extends Controller
{
    public function store (Request $r) {
        $validator = Validator::make($r->all(), [
            'file' => 'required',
            'title' => 'sometimes',
            'description' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $uploadedFile = (new FilesHelper)->uploadFile($r->file);

        $file = File::create([
            'path' => $uploadedFile->path,
            'path_resized' => $uploadedFile->path_resized,
            'file_name' => $uploadedFile->original_file_name,
            'file_size' => $uploadedFile->file_size,

            'title' => $r->title,
            'description' => $r->description
        ]);

        return response([
            'file' => $file
        ]);
    }

    public function update ($file) {
        $file = File::find($file);

        return $file;

        return response([
            'message' => 'File deleted'
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
