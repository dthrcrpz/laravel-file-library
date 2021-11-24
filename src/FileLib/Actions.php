<?php

namespace Dthrcrpz\FileLibrary\FileLib;

use Dthrcrpz\FileLibrary\Models\File;
use Illuminate\Support\Facades\Validator;
use Dthrcrpz\FileLibrary\Services\FilesHelper;
use Dthrcrpz\FileLibrary\Models\FileAttachment;

class Actions
{
    public function uploadFile ($r) {
        $validator = Validator::make($r->all(), [
            'file' => 'required',
            'title' => 'sometimes',
            'description' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return (object) [
                'success' => false,
                'statusCode' => 400,
                'errorMessage' => $validator->errors()->all()
            ];
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

        return (object) [
            'success' => true,
            'statusCode' => 201,
            'file' => $file,
            'message' => 'File added'
        ];
    }

    public function updateFile ($fileModel, $r) {
        $file = File::find($fileModel);

        if (!$file) {
            return (object) [
                'success' => false,
                'statusCode' => 404,
                'errorMessage' => 'File not found'
            ];
        }

        $validator = Validator::make($r->all(), [
            'file' => 'sometimes',
            'title' => 'sometimes',
            'description' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return (object) [
                'success' => false,
                'statusCode' => 400,
                'errorMessage' => $validator->errors()->all()
            ];
        }

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

        return (object) [
            'success' => true,
            'statusCode' => 200,
            'file' => $file,
            'message' => 'File updated'
        ];
    }
    
    public function deleteFile ($file) {
        $file = File::find($file);

        if (!$file) {
            return (object) [
                'success' => false,
                'statusCode' => 404,
                'errorMessage' => 'File not found'
            ];
        }

        $file->delete();

        return (object) [
            'success' => true,
            'statusCode' => 201,
            'message' => 'File deleted'
        ];
    }

    public function deleteAttachment ($file_attachment) {
        $fileAttachment = FileAttachment::find($file_attachment);

        if (!$fileAttachment) {
            return (object) [
                'success' => false,
                'statusCode' => 404,
                'errorMessage' => 'File attachment not found'
            ];
        }

        $fileAttachment->forceDelete();

        return (object) [
            'success' => true,
            'statusCode' => 201,
            'message' => 'File attachment deleted'
        ];
    }
}