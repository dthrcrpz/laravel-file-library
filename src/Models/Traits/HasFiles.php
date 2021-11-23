<?php

namespace Dthrcrpz\FileLibrary\Models;

use Dthrcrpz\FileLibrary\Models\File;

trait HasFiles
{
    // public function files () {
    //     return $this->hasMany(File::class, 'parent_id', 'id')
    //     ->where('model_name', $this->modelName) # singular noun, kebab-case
    //     ->orderBy('sequence');
    // }

    public function attachFile ($image_id) {
        $image = File::find($image_id);

        if ($image) {
            if ($image->parent_id != $this->id) {
                $image->update([
                    'parent_id' => $this->id,
                    'type' => $this->modelName
                ]);
            }
        }
    }
}