<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\Models\File;
use Illuminate\Support\Facades\Validator;
use Dthrcrpz\FileLibrary\Services\FilesHelper;
use Illuminate\Support\Facades\Redis;

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

    public function update ($fileModel, Request $r) {
        $validator = Validator::make($r->all(), [
            'file' => 'sometimes',
            'title' => 'sometimes',
            'description' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $file = File::find($fileModel);

        $uploadedFile = null;

        if ($r->file) {
            $uploadedFile = (new FilesHelper)->uploadFile($r->file);
        }

        $file->update([
            'path' => ($uploadedFile != null) ? $uploadedFile->path : $file->getRawOriginal('path'),
            'path_resized' => ($uploadedFile != null) ? $uploadedFile->path_resized : $file->getRawOriginal('path_resized'),
            'file_name' => ($uploadedFile != null) ? $uploadedFile->original_file_name : $file->file_name,
            'file_size' => ($uploadedFile != null) ? $uploadedFile->file_size : $file->file_size,

            'title' => ($r->title) ? $r->title : $file->title,
            'description' => ($r->description) ? $r->description : $file->description
        ]);

        return response([
            'file' => $file
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
