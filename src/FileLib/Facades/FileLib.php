<?php

namespace Dthrcrpz\FileLibrary\FileLib\Facades;

use Illuminate\Support\Facades\Facade;

class FileLib extends Facade
{
    protected static function getFacadeAccessor() {
        return 'filelib';
    }
}