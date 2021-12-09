<?php

namespace Dthrcrpz\FileLibrary\Models;

use Dthrcrpz\FileLibrary\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Pivot
{
    use SoftDeletes, Uuid;

    protected $table = 'file_attachments';

    protected $guarded = ['created_at'];
    protected $hidden = ['file_id', 'model_id', 'model_name', 'created_at', 'updated_at', 'deleted_at'];

    public function file () {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }
}
