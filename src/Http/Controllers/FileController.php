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
            'file_title' => 'sometimes',
            'file_alt' => 'sometimes',
            'file_category' => 'sometimes',
            'type' => 'required|in:image'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $uploadedFile = $this->uploadFile($r->file);

        $toReturn = [
            'path' => $uploadedFile->path,
            'path_resized' => $uploadedFile->path_resized,
            'original_file_name' => $uploadedFile->original_file_name,
            'file_size' => $uploadedFile->file_size,
        ];

        if ($r->type == 'image') {
            $image = File::create([
                'path' => $uploadedFile->path,
                'path_resized' => $uploadedFile->path_resized,
                'file_name' => $uploadedFile->original_file_name,
                'file_size' => $uploadedFile->file_size,
            ]);

            $toReturn['image'] = $image;
        }

        /* add more conditions here if you want to save the file data to other models
        * EXAMPLE:
        * if ($r->type == 'user-document') {
        *     $userDocument = UserDocument::create([
        *         'path' => $uploadedFile->path
        *     ]);
        * }
        */

        return $toReturn;
    }

    protected function uploadFile ($file, $oldFilePath = null, $oldFilePathResized = null) {
        $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave
    
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
        $otherAcceptedExtensions = ['svg', 'gif', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'docx', 'doc', 'txt', 'SVG', 'GIF', 'PDF', 'PPT', 'PPTX', 'XLS', 'XLSX', 'DOCX', 'DOC', 'TXT'];
        $uploadPath = "uploads/$folderDate/$folderTime/$filenameToStore";
    
        # if the file is svg or gif, directly upload it and stop the function immediately by returning the path names
        if (in_array($extension, $otherAcceptedExtensions)) {
            Storage::disk($disk)->put($uploadPath, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => getContentType($extension)
            ]);
    
            $toReturn = (object) [
                'path' => $uploadPath,
                'path_resized' => $uploadPath,
                'original_file_name' => $filenameToStore,
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
}
