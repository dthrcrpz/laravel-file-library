<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\FileLib\Facades\FileLib;

class FileAttachmentController extends Controller
{
    public function destroy($file_attachment)
    {
        $fileAttachmentDelete = FileLib::deleteAttachment($file_attachment);

        if (!$fileAttachmentDelete->success) {
            return response([
                'errors' => [$fileAttachmentDelete->errorMessage]
            ], $fileAttachmentDelete->statusCode);
        }

        return response([
            'message' => $fileAttachmentDelete->message
        ]);
    }
}
