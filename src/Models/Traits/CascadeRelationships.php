<?php

namespace Dthrcrpz\FileLibrary\Models\Traits;

trait CascadeRelationships
{
    protected static function bootCascadeRelationships () {
        static::deleting(function ($resource) {
            foreach (self::$cascades as $key => $relation) {
                foreach ($resource->{$relation}()->get() as $key => $item) {
                    $item->delete();
                }
            }
        });

        static::restoring(function ($resource) {
            foreach (self::$cascades as $key => $relation) {
                foreach ($resource->{$relation}()->withTrashed()->get() as $key => $item) {
                    $item->restore();
                }
            }
        });
    }
}