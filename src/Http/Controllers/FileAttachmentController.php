<?php

namespace Dthrcrpz\FileLibrary\Http\Controllers;

use App\Http\Controllers\Controller;
use Dthrcrpz\FileLibrary\Models\FileAttachment;

class FileAttachmentController extends Controller
{
    public function destroy ($file_attachment) {
        $fileAttachment = FileAttachment::find($file_attachment);

        if (!$fileAttachment) {
            return response([
                'errors' => [
                    'File attachment not found'
                ]
            ], 404);
        }

        $fileAttachment->forceDelete();

        return response([
            'message' => 'Attachment deleted'
        ]);
    }
}
