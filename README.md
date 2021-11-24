# Laravel File Library
## _A simple package to upload files and attach them to your Models_

> This package was developed mainly for personal usage but I decided to publish it so I can just install it through composer for my projects.

## Installation
Install the package

```sh
composer require dthrcrpz/laravel-file-library
```

Run the migration
```sh
php artisan migrate
```
After running the migration, `files` and `files_attachments` tables will be added to your database. Make sure that there's no conflict with your table names.

## Usage
This package uses routes to upload, update, and delete files.
The routes are automatically generated after installing the package. To check if the routes were added, run `php artisan route:list`
### Upload File
**METHOD:** POST
**ROUTE:** `/api/files`
**BODY:**
| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | yes |
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

### Update File
**METHOD:** PATCH/PUT
**ROUTE:** `/api/files/69`
**BODY:**
| Key | Type | Accepted Values | Required |
| ------ | ------ | ------ | ------ |
| file | file | <your uploaded fle> | no |
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

### Delete File
This will also delete the `file_attachments` related to it
**METHOD:** DELETE
**ROUTE:** `/api/files/69`
**SAMPLE RESPONSE:**
```json
{
    "message": "File deleted"
}
```

### Attaching files to Model
Make sure to use the `HasFiles` trait on your model and define its `$modelName` (kebab-case, singular noun)
```php
<?php

namespace App\Models;

use Dthrcrpz\FileLibrary\Models\Traits\HasFiles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use SoftDeletes, HasFiles;

    public $modelName = 'your-model';
}
```

After setting it up, your model can now call the `attachFile()` method. It accepts `$file_id` (id) parameter which you can get from you `files` table.
```php
    $yourModel = YourModel::find(69);
    $yourModel->attachFile(420);
```
### Calling the file attachments relationship
You can use Eloquent's `with` method.
```php
    $yourModel = YourModel::where('id', 69)
    ->with([
        'file_attachments'
    ])
    ->first();
```

### Deleting Attachment
**METHOD:** DELETE
**ROUTE:** `/api/file-attachments/69`

## Config
| Key | Description | Values | Default |
| ------ | ------ | ------ | ------ |
| storage | The filesystem where you want the files to be uploaded | `public`, `s3` | `public` |
| s3_url | Required if `storage` is set to `s3`. Format should be `https://your-domain.s3-ap-southeast-1.amazonaws.com/`. DO NOT miss out the trailing slash at the end of the URL | `<url>` | `null` |

## Development
Want to contribute? Great! Feel free to submit a pull request.

## License
MIT
