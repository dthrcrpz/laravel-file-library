<?php

namespace Dthrcrpz\FileLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dthrcrpz\FileLibrary\Models\Traits\CascadeRelationships;

class File extends Model
{
    use SoftDeletes;

    protected $guarded = ['created_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected static $cascades = ['file_attachments'];

    public function getFilenameAttribute () {
        return $this->attributes['path'];
    }

    public function getPathAttribute ($value) {
        return $this->formatPath($value);
    }

    public function getPathResizedAttribute ($value) {
        return $this->formatPath($value);
    }

    private function formatPath ($value) {
        switch (config('filelibrary.storage')) {
            case 'public':
                return url('/') . '/storage/' . $value;
                break;
            case 's3':
                return config('filelibrary.s3_url') . urlencode($value);
                break;
        }
    }

    public function file_attachments () {
        return $this->hasMany(FileAttachment::class);
    }
}
