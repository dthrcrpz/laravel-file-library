<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;

use Dthrcrpz\FileLibrary\Models\File;
use Dthrcrpz\FileLibrary\Models\FileAttachment;

trait HasFiles
{
    public function files () {
        return $this->morphToMany(File::class, 'file_attachments')
        ->where('model_name', $this->modelName) # singular noun, kebab-case
        ->orderBy('sequence');
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