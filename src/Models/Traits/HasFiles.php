<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;

use Dthrcrpz\FileLibrary\Models\File;
use Dthrcrpz\FileLibrary\Models\FileAttachment;
use Exception;

trait HasFiles
{
    public $fileCategory = null;

    public function setCategory ($fileCategory = null) {
        $this->fileCategory = $fileCategory;
        return $this;
    }

    public function file_attachments () {
        $fileAttachments = $this->hasMany(FileAttachment::class, 'model_id', 'id')
        ->where('model_name', class_basename($this)) # singular noun, kebab-case
        ->with([
            'file'
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
            throw new Exception("attachFiles() function can only accept array");
        }

        return $this;
    }

    public function attachFile ($file_id) {
        $attachmentExists = FileAttachment::where('model_name', class_basename($this))
        ->where('model_id', $this->id)
        ->where('file_id', $file_id)
        ->exists();

        if (!$attachmentExists) {
            $file = File::find($file_id);
    
            if ($file) {
                FileAttachment::create([
                    'model_id' => $this->id,
                    'model_name' => class_basename($this),
                    'file_id' => $file_id,
                    'category' => $this->fileCategory
                ]);
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
            throw new Exception("detachFiles() function can only accept array");
        }

        return $this;
    }

    public function detachFile ($file_id) {
        $attachment = FileAttachment::where('model_name', class_basename($this))
        ->where('model_id', $this->id)
        ->where('file_id', $file_id)
        ->first();

        if ($attachment) {
            $attachment->delete();
        }

        return $this;
    }

    public function detachAllFiles () {
        $fileAttachments = FileAttachment::where('model_name', class_basename($this))
        ->where('model_id', $this->id)
        ->get();

        foreach ($fileAttachments as $key => $fileAttachment) {
            $fileAttachment->delete();
        }

        return $this;
    }
}