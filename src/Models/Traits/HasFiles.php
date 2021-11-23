<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;

use Dthrcrpz\FileLibrary\Models\File;
use Dthrcrpz\FileLibrary\Models\FileAttachment;

trait HasFiles
{
    public function files () {
        $fileAttachments = $this->hasMany(FileAttachment::class, 'model_id', 'id')
        ->where('model_name', $this->modelName) # singular noun, kebab-case
        ->with([
            'data'
        ]);

        return $fileAttachments;
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
}