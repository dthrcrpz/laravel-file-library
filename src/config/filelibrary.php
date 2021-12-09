<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    | The filesystem where you want the files to be uploaded
    | Supported: "public", "s3"
    */
    'storage' => 'public',
    
    /*
    |--------------------------------------------------------------------------
    | AWS S3 URL
    |--------------------------------------------------------------------------
    | Required when storage is set to s3
    | Format should be: https://your-domain.s3-ap-southeast-1.amazonaws.com/
    */
    's3_url' => env('S3_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Enable Routes
    |--------------------------------------------------------------------------
    | Set to false if you want to use custom routes with custom middlewares, etc.
    |
    | The default routes are:
    | POST: 'files'
    | PATCH: 'files/{file_model}'
    | DELETE: 'files/{file}'
    | DELETE: file-attachments/{file_attachment}
    */
    'enable_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Use UUID
    |--------------------------------------------------------------------------
    | Set to true if you're using UUID as your primary key's data type instead of the default id
    | Default is false
    */
    'use_uuid' => false
];