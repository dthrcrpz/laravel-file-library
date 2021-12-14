<?php

namespace Dthrcrpz\FileLibrary\Providers;

use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function boot ()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filelibrary.php', 'filelibrary');

        if (config('filelibrary.enable_routes')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/files.php');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2021_11_23_00000_create_files_table.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2021_11_23_00001_create_file_attachments_table.php');

        $this->publishes([
            __DIR__.'/../config/filelibrary.php' => config_path('filelibrary.php')
        ], 'filelibrary-config');
    }
}