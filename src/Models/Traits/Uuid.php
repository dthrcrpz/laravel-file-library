<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;
use Illuminate\Support\Str;

trait Uuid
{
    protected static function bootUuid()
    {
        if (config('filelibrary.use_uuid')) {
            static::creating(function ($model) {
                if (!$model->getKey()) {
                    $model->{$model->getKeyName()} = (string) Str::uuid();
                }
            });
        }
    }

    public function getKeyType()
    {
        if (config('filelibrary.use_uuid')) {
            return 'string';
        }
    }

    public function getIncrementing()
    {
        if (config('filelibrary.use_uuid')) {
            return false;
        }
    }
}