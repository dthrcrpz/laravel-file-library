<?php

namespace Dthrcrpz\FileLibrary\Providers;

use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function boot ()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/files.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2021_11_23_00000_create_files_table.php');
        $this->mergeConfigFrom(__DIR__.'/../config/filelibrary.php', 'filelibrary');
    }
}