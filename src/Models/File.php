<?php

namespace Dthrcrpz\FileLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $guarded = ['created_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['filename'];

    public function getFilenameAttribute () {
        return $this->attributes['path'];
    }

    public function getPathAttribute ($value) {
        return url('/') . '/storage/' . $value;
    }

    public function getPathResizedAttribute ($value) {
        return url('/') . '/storage/' . $value;
    }
}
