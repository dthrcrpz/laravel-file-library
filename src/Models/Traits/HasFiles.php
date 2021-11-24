<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;

use Dthrcrpz\FileLibrary\Models\File;
use Dthrcrpz\FileLibrary\Models\FileAttachment;
use Exception;

trait HasFiles
{
    public function file_attachments () {
        $fileAttachments = $this->hasMany(FileAttachment::class, 'model_id', 'id')
        ->where('model_name', $this->modelName) # singular noun, kebab-case
        ->with([
            'files'
        ]);

        return $fileAttachments;
    }

    public function attachFiles ($file_ids) {
        $acceptedDataTypes = ['array', 'object'];
        if (in_array(gettype($file_ids), $acceptedDataTypes)) {
            foreach ($file_ids as $key => $file_id) {
                $this->attachFile($file_id);
            }
        } else {
            throw new Exception("attachFiles function can only accept array");
        }
    }

    public function attachFile ($file_id) {
        if ($this->modelName) {
            $attachmentExists = FileAttachment::where('model_name', $this->modelName)
            ->where('model_id', $this->id)
            ->where('file_id', $file_id)
            ->exists();

            if (!$attachmentExists) {
                $file = File::find($file_id);
        
                if ($file) {
                    FileAttachment::create([
                        'model_id' => $this->id,
                        'model_name' => $this->modelName,
                        'file_id' => $file_id
                    ]);
                }
            }
        }
    }

    public function detachFiles ($file_ids) {
        $acceptedDataTypes = ['array', 'object'];
        if (in_array(gettype($file_ids), $acceptedDataTypes)) {
            foreach ($file_ids as $key => $file_id) {
                $this->detachFile($file_id);
            }
        } else {
            throw new Exception("detachFiles function can only accept array");
        }
    }

    public function detachFile ($file_id) {
        if ($this->modelName) {
            $attachment = FileAttachment::where('model_name', $this->modelName)
            ->where('model_id', $this->id)
            ->where('file_id', $file_id)
            ->first();

            if ($attachment) {
                $attachment->delete();
            }
        }
    }

    public function detachAllFiles () {
        if ($this->modelName) {
        $fileAttachments = FileAttachment::where('model_name', $this->modelName)
            ->where('model_id', $this->id)
            ->get();

            foreach ($fileAttachments as $key => $fileAttachment) {
                $fileAttachment->delete();
            }
        }
    }
}