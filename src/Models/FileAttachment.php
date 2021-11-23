<?php

namespace Dthrcrpz\FileLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Pivot
{
    use SoftDeletes;

    protected $table = 'file_attachments';

    protected $guarded = ['created_at'];
    protected $hidden = ['file_id', 'model_id', 'model_name', 'created_at', 'updated_at', 'deleted_at'];

    public function files () {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }
}
