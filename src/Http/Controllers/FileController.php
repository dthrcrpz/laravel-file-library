<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

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

        $uploadedFile = $this->uploadFile($r->file);

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

    private function uploadFile ($file, $oldFilePath = null, $oldFilePathResized = null) {
        $disk = config('filelibrary.storage');
    
        # get the file size
        $size = filesize($file);
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        $fileSize = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    
        # delete the old file if it exists
        if ($oldFilePath != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePath");
        }
        if ($oldFilePathResized != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePathResized");
        }
    
        $filenameWithExtension = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
        $folderDate = Carbon::now()->format('Y-m-d');
        $folderTime = Carbon::now()->format('H-i-s-u');
        $filenameToStore = $filename . '.' . $extension;
        $otherAcceptedExtensions = $this->getExtensions();
        $uploadPath = "uploads/$folderDate/$folderTime/$filenameToStore";
    
        # if the file type is not image, skip the compression; directly upload it; and stop the function immediately by returning the path names
        if (in_array($extension, $otherAcceptedExtensions)) {
            Storage::disk($disk)->put($uploadPath, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $this->getContentType($extension)
            ]);
    
            $toReturn = (object) [
                'path' => $uploadPath,
                'path_resized' => $uploadPath,
                'original_file_name' => $filenameWithExtension,
                'file_size' => $fileSize
            ];
    
            return $toReturn;
        }
    
        $unresizedFile = Image::make($file->getRealPath())->interlace()->encode($extension, 80)->orientate();
        Storage::disk($disk)->put($uploadPath, $unresizedFile->getEncoded(), 'public');
    
        # upload resized file
        $resizedFile = Image::make($file->getRealPath())->resize(750, 750, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })->interlace()->encode($extension, 80)
        ->orientate();
    
        $filenameToStore_resized = $filename . '_thumbnail.' . $extension;
        $uploadPathResized = "uploads/$folderDate/$folderTime/$filenameToStore_resized";
    
        Storage::disk($disk)->put($uploadPathResized, $resizedFile->getEncoded(), 'public');
    
        $toReturn = (object) [
            'path' => "uploads/$folderDate/$folderTime/$filenameToStore",
            'path_resized' => "uploads/$folderDate/$folderTime/$filenameToStore_resized",
            'original_file_name' => $filenameWithExtension,
            'file_size' => $fileSize
        ];
    
        return $toReturn;
    }

    private function getExtensions () {
        $extensions = ['svg', 'gif', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'docx', 'doc', 'txt'];

        $extensionsArray = [];

        foreach ($extensions as $key => $extension) {
            array_push($extensionsArray, $extension);
            array_push($extensionsArray, strtoupper($extension));
        }

        return $extensionsArray;
    }
    
    private function getContentType ($extension) {
        $result = null;
        switch ($extension) {
            case 'svg':
            case 'SVG':
                $result = 'image/svg+xml';
                break;
            case 'gif':
            case 'GIF':
                $result = 'image/gif';
                break;
            case 'pdf':
            case 'PDF':
                $result = 'application/pdf';
                break;
            case 'ppt':
            case 'PPT':
                $result = 'application/vnd.ms-powerpoint';
                break;
            case 'pptx':
            case 'PPTX':
                $result = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                break;
            case 'xls':
            case 'XLS':
                $result = 'application/vnd.ms-excel';
                break;
            case 'xlsx':
            case 'XLSX':
                $result = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'docx':
            case 'DOCX':
                $result = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'doc':
            case 'DOC':
                $result = 'application/msword';
                break;
            case 'txt':
            case 'TXT':
                $result = 'text/plain';
                break;
        }
    
        return $result;
    }
}
