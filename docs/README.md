> A simple package to upload files and attach them to your Models

This package was developed mainly for personal usage but I decided to publish it so I can just install it through composer for my projects. Feel free to use it for your projects and contribute on the package. 

# Getting Started

## Requirements
`php >= 7.4`

`laravel >= 8`

`intervention/image`

## Installation
Install the package

```bash
composer require dthrcrpz/laravel-file-library
```

Run the migration
```bash
php artisan migrate
```
After running the migration, `files` and `files_attachments` tables will be added to your database. Make sure that there's no conflict with your table names.

Make sure to enable the `FileLib` facade to your Laravel app. This package also uses [Intervention/Image](https://github.com/Intervention/image) to compress images.
Navigate to `config/app.php` and add the following to the `providers` and `aliases` array:
```php
...
'providers' => [
    ...
    Intervention\Image\ImageServiceProvider::class,
    Dthrcrpz\FileLibrary\Providers\FileLibServiceProvider::class,
    ...
],
'aliases' => [
    ...
    'Image' => Intervention\Image\Facades\Image::class,
    'FileLib' => Dthrcrpz\FileLibrary\FileLib\Facades\FileLib::class,
    ...
]
```

# Usage Through Facades

This should be the preferred usage so you can create your own routes and use your middlewares.

## Upload File
```php
FileLib::uploadFile($request);
```
**Make sure that your request body contains the following parameters:**

| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | yes |
| type | string | `image`, `document`, `etc.` | yes |
| title | string | <any> | no |
| description | string | <any> | no |

***Sample Code:***
```php
$uploadedFile = FileLib::uploadFile($request);

if (!$uploadedFile->success) {
    return response([
        'errors' => $uploadedFile->errorMessage
    ], $uploadedFile->statusCode);
}

return response([
    'file' => $uploadedFile->file
]);
```

## Update File
```php
FileLib::updateFile($file, $request);
```
**Make sure that your request body contains the following parameters:**

| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | no |
| type | string | `image`, `document`, `etc.` | no |
| title | string | <any> | no |
| description | string | <any> | no |

***Sample Code:***
```php
$file = File::find(420);
$updatedFile = FileLib::updateFile(file, $r);

if (!$updatedFile->success) {
    return response([
        'errors' => [$updatedFile->errorMessage]
    ], $updatedFile->statusCode);
}

return response([
    'file' => $updatedFile->file
]);
```

## Delete File
```php
FileLib::deleteFile($file);
```

***Sample Code:***
```php
$file = File::find(420);
$fileDelete = FileLib::deleteFile($file);

if (!$fileDelete->success) {
    return response([
        'errors' => [$fileDelete->errorMessage]
    ], $fileDelete->statusCode);
}

return response([
    'message' => $fileDelete->message
]);
```

# Usage Through Routes

If the `enabled_routes` is enabled on the `filelibrary.php` config file, the pacakge will generate routes to upload, update, and delete files.
To check the routes, run `php artisan route:list`

## Upload File (route)
**METHOD:** `POST`

**ROUTE:** `/api/files`

**BODY:**

| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | yes |
| type | string | `image`, `document`, `etc.` | yes |
| title | string | <any> | no |
| description | string | <any> | no |

**SAMPLE RESPONSE:**
```json
{
    "file": {
        "path": "http://your-domain.test/storage/uploads/2021-11-23/13-41-51-297141/filename.jpg",
        "path_resized": "http://your-domain.test/storage/uploads/2021-11-23/13-41-51-297141/filename_thumbnail.jpg",
        "file_name": "filename.jpg",
        "file_size": "69.00 KB",
        "title": null,
        "description": null,
        "id": 420
    }
}
```

Question: Why does the API only accepts 1 file?
Answer: When a user uploads a file, the frontend should call this API to upload the file. Next, it should save the API response then get the file's `id` then attach it to the form that will be submitted later on. During submission, the backend should [attach the file to the model](#attaching-files-to-model).

## Update File (route)
**METHOD:** `PATCH/PUT`

**ROUTE:** `/api/files/69`

**BODY:**

| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | no |
| type | string | `image`, `document`, `etc.` | no |
| title | string | <any> | no |
| description | string | <any> | no |

**SAMPLE RESPONSE:**
```json
{
    "file": {
        "path": "http://your-domain.test/storage/uploads/2021-11-23/13-41-51-297141/filename.jpg",
        "path_resized": "http://your-domain.test/storage/uploads/2021-11-23/13-41-51-297141/filename_thumbnail.jpg",
        "file_name": "filename.jpg",
        "file_size": "69.00 KB",
        "title": null,
        "description": null,
        "id": 420
    }
}
```

## Delete File (route)
This will also delete the `file_attachments` related to it

**METHOD:** `DELETE`

**ROUTE:** `/api/files/69`

**SAMPLE RESPONSE:**
```json
{
    "message": "File deleted"
}
```

# Attaching files to Model
Make sure to use the `HasFiles` trait on your model.
```php
<?php

namespace App\Models;

use Dthrcrpz\FileLibrary\Models\Traits\HasFiles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use SoftDeletes, HasFiles;
}
```

## attachFile(), attachFiles()
After setting it up, your model can now call the `attachFile()` method. It accepts `$file_id` (id) parameter which you can get from you `files` table.

***Attaching a single file***
```php
$yourModel = YourModel::find(69);

# attaching a single file
$yourModel->attachFile(69); # where 69 is the file ID

# attaching a single file WITH category. This can be used if a model has different types of file attachments such as "banner-image", "background-image", etc.
$yourModel->setCategory('background-image')->attachFile(69);
```

***Attaching multiple files***
```php
$yourModel = YourModel::find(69);

# assuming you got an array of file IDs
$fileIdArray = [420, 69, 666];
$yourModel->attachFiles($fileIdArray);

# attaching a single file WITH category. This can be used if a model has different types of file attachments such as "banner-image", "background-image", etc.
$yourModel->setCategory('background-image')->attachFiles($$fileIdArray);
```

# Working with relationships
You can use Eloquent's `with` method. Feel free to build a query according to your needs.
```php
# sample code to include all file_attachments
$yourModel = YourModel::where('id', 69)
->with([
    'file_attachments'
])
->first();

# sample code to include specific category of file_attachments only
$yourModel = YourModel::where('id', 69)
->with([
    'file_attachments' => function ($q) {
        $q->where('category', 'background-image');
    }
])
->first();
```

# Detaching files from your Model

## detachFile()
```php
$yourModel = YourModel::find(69);

# assuming you got an array of file IDs
foreach ($r->file_ids as $file_id) {
    $yourModel->detachFile($file_id);
}

# detaching a single file
$yourModel->detachFile(69); # where 69 is the file ID
```

## detachAllFiles()
```php
$yourModel = YourModel::find(69);

# detaching all files
$yourModel->detachAllFiles();
```

## Deleting Attachment (Route)
**Sample use case:**
You're editing a Blog with multiple attached files. You're displaying the files on that Editor Form and you want to delete one. Call this route when doing so.

**METHOD:** `DELETE`

**ROUTE:** `/api/file-attachments/69`

# Config

Publish the package's config
```bash
php artisan vendor:publish --tag=filelibrary-config
```

A `filelibrary.php` file under your `config` folder will be generated.
```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    | The filesystem where you want the files to be uploaded
    | Currently supported are: "public", "s3"
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
    | POST: 'api/files'
    | PATCH: 'api/files/{file_model}'
    | DELETE: 'api/files/{file}'
    | DELETE: 'api/file-attachments/{file_attachment}'
    */
    'enable_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Use UUID
    |--------------------------------------------------------------------------
    | Set to true if you're using UUID as your primary key's data type instead of the default id
    | Default is false
    */
    'use_uuid' => false,

    /*
    |--------------------------------------------------------------------------
    | Resize Dimensions
    |--------------------------------------------------------------------------
    | Dimension of image when being resized by Image Intervention
    | Default is [300, 300]
    */
    'resize_dimensions' => [300, 300]
];
```

# Development
Wanna contribute? Great! Feel free to submit a pull request.

# License
MIT
