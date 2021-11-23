<?php

namespace Dthrcrpz\FileLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Model
{
    use SoftDeletes;

    protected $guarded = ['created_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public function file () {
        return $this->belongsTo(File::class);
    }
}
