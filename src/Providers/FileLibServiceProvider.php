<?php

namespace Dthrcrpz\FileLibrary\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FileLibServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        App::bind('filelib', function () {
            return new \Dthrcrpz\FileLibrary\FileLib\Actions;
        });
    }
}