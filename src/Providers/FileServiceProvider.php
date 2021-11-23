<?php

namespace Dthrcrpz\FileLibrary\Providers;

use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function boot () {
        $this->loadRoutesFrom(__DIR__.'/../routes/files.php');
    }
}