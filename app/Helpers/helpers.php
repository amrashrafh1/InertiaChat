<?php 

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

if(!function_exists('upload_image')) {

    function upload_image($image, $path, $user_id)
    {
        $FileName = time() . str_replace(['a', 'm', 'r'], '', $image->getClientOriginalName());

        Storage::put('public/files/' . $path . '/' . $user_id . '/' . $FileName, fopen($image, 'r+'));

        $ThumbnailPath = storage_path('app/public/files/' . $path . '/' . $user_id . '/' . $FileName);

        Image::make($ThumbnailPath)->resize(400, 150, function ($constraint) {
            $constraint->aspectRatio();
        })->save($ThumbnailPath);

        return $FileName;
    }
}




if (!function_exists('image_preview')) {
    function image_preview($image, $id, $model)
    {
        $file = 'files/' . $model . '/' . $id . '/' . $image;

        // check if file exists

        if (Storage::disk('public')->exists($file)) {
            // return file

            return Storage::url($file);
        }

        // default image

        return asset('images/default.png');
    }
}

if (!function_exists('delete_image')) {
    function delete_image($table, $model)
    {
        // delete image

        $image = $model->image;

        if ($image) {
            Storage::delete('public/files/' . $table . '/' . $model->id . '/' . $model->image);
        }

        $model->delete();

        return;
    }
}

if (!function_exists('update_image')) {
    function update_image($image, $requestFile, $model, $path)
    {
        // update image

        if ($image) {
            Storage::delete('public/files/' . $path . '/' . $model->id . '/' . $model->image);
        }
        $file = upload_image($requestFile, $path, $model->id);

        return $file;
    }
}